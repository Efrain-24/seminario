<?php

namespace App\Services;

use App\Models\{Lote, Alimentacion, Seguimiento};
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class BiomasaService
{
    protected float $FCR;              // 1.5–1.8 típico
    protected float $SGR_percent_day;  // 1–3 %/día típico
    protected const LB_TO_KG = 0.45359237;
    protected const KG_TO_G = 1000;

    public function __construct(float $FCR = 1.6, float $SGR_percent_day = 1.5)
    {
        $this->FCR = $FCR;
        $this->SGR_percent_day = $SGR_percent_day;
    }

    /** Conversión de kg a gramos */
    public static function kgToGramos(float $kg): float 
    {
        return $kg * self::KG_TO_G;
    }

    /** Conversión de gramos a kg */
    public static function gramosToKg(float $gramos): float 
    {
        return $gramos / self::KG_TO_G;
    }

    /** Devuelve el nombre de la columna fecha disponible en una tabla */
    protected function columnaFecha(string $table, array $candidatas = [
        'fecha',
        'fecha_muestreo',
        'fecha_biometria',
        'fecha_seguimiento',
        'fecha_registro',
        'date',
        'dia',
        'created_at',
        'updated_at'
    ]): string
    {
        $cols = Schema::getColumnListing($table);
        foreach ($candidatas as $c) {
            if (in_array($c, $cols, true)) return $c;
        }
        // último recurso
        return 'created_at';
    }

    /** Intenta leer el último registro de biometría y su fecha */
    protected function ultimaBiometria(Lote $lote): ?array
    {
        $tablaSeg = (new Seguimiento)->getTable(); // normalmente 'seguimientos'
        $colFecha = $this->columnaFecha($tablaSeg);

        $bio = Seguimiento::where('lote_id', $lote->id)
            ->orderBy($colFecha, 'desc')
            ->first();

        if (!$bio) {
            // Fallback: usa peso promedio inicial del lote si existe
            $aLote = $lote->getAttributes();
            $pesoLote = ($aLote['peso_promedio_inicial'] ?? null) ?? ($aLote['peso_promedio'] ?? null);
            if ($pesoLote !== null) {
                return ['peso_kg' => (float)$pesoLote, 'fecha' => Carbon::parse($lote->fecha_inicio ?? now())];
            }
            return null;
        }

        $a = $bio->getAttributes();

        // Posibles nombres para el peso promedio (en kilogramos por pez)
        $peso =
            ($a['peso_promedio_kg'] ?? null) ??
            ($a['peso_promedio']   ?? null) ??
            ($a['peso_muestra_kg']  ?? null) ??
            ($a['peso_muestra']    ?? null) ??
            ($a['peso']            ?? null);

        if ($peso === null) return null;

        $fechaValor = $bio->{$colFecha} ?? now();

        return ['peso_kg' => (float)$peso, 'fecha' => Carbon::parse($fechaValor)];
    }

    /** Suma alimento en KG desde una fecha, detectando unidad/columna */
    protected function alimentoDesde(Lote $lote, Carbon $desde): float
    {
        $tablaAli = (new Alimentacion)->getTable(); // en tu proyecto parece 'alimentacions'
        $colFecha = $this->columnaFecha($tablaAli);

        $regs = Alimentacion::where('lote_id', $lote->id)
            ->whereDate($colFecha, '>', $desde)
            ->get();

        $kg = 0.0;
        foreach ($regs as $r) {
            $a = $r->getAttributes();

            if (array_key_exists('cantidad_kg', $a)) {
                $kg += (float)$a['cantidad_kg'];
            } elseif (array_key_exists('cantidad_libras', $a)) {
                $kg += (float)$a['cantidad_libras'] * self::LB_TO_KG;
            } elseif (array_key_exists('cantidad_lb', $a)) {
                $kg += (float)$a['cantidad_lb'] * self::LB_TO_KG;
            } elseif (array_key_exists('cantidad', $a)) { // sin unidad explícita: asumimos kg
                $kg += (float)$a['cantidad'];
            }
        }
        return $kg;
    }

    /** Estima peso promedio actual (kg/pez) usando alimento/FCR o SGR */
    public function estimarPesoPromedioActual(Lote $lote): ?float
    {
        $base = $this->ultimaBiometria($lote);
        if (!$base) return null;

        $w0   = $base['peso_kg'];
        $t0   = $base['fecha'];
        $dias = max(0, $t0->diffInDays(today()));

        $feedKg = $this->alimentoDesde($lote, $t0);
        if ($feedKg > 0) {
            $pobl_prom = max(1, (int)$lote->cantidad_actual_real);
            $ganancia_total_kg  = $feedKg / $this->FCR;
            $ganancia_por_pez_kg = $ganancia_total_kg / $pobl_prom;
            return max(0.0, $w0 + $ganancia_por_pez_kg);
        }

        // Sin alimentación registrada, usa SGR
        if ($dias > 0) {
            return $w0 * exp(($this->SGR_percent_day / 100.0) * $dias);
        }
        return $w0;
    }

    /** Peso promedio actual en gramos (g/pez) para mostrar en interfaz */
    public function estimarPesoPromedioActualEnGramos(Lote $lote): ?float
    {
        $pesoKg = $this->estimarPesoPromedioActual($lote);
        return $pesoKg ? round(self::kgToGramos($pesoKg), 1) : null; // convertir kg a gramos
    }

    /** Biomasa estimada hoy (kg) */
    public function estimarBiomasaKg(Lote $lote): ?float
    {
        $w = $this->estimarPesoPromedioActual($lote);
        if ($w === null) return null;
        return round($w * max(0, (int)$lote->cantidad_actual_real), 2);
    }

    /** Predicción hasta fecha objetivo */
    public function predecirHastaFecha(Lote $lote, Carbon $fechaObjetivo): ?array
    {
        $w_hoy = $this->estimarPesoPromedioActual($lote);
        if ($w_hoy === null) return null;

        $dias  = max(0, today()->diffInDays($fechaObjetivo));
        $w_obj = $w_hoy * exp(($this->SGR_percent_day / 100.0) * $dias);
        $bio   = round($w_obj * max(0, (int)$lote->cantidad_actual_real), 2);

        return ['dias' => $dias, 'peso_promedio_kg' => round($w_obj, 3), 'biomasa_kg' => $bio];
    }

    /** Fecha para alcanzar un peso objetivo (kg/pez) */
    public function predecirDiaParaPesoObjetivo(Lote $lote, float $pesoObjetivo_kg): ?array
    {
        $w_hoy = $this->estimarPesoPromedioActual($lote);
        if ($w_hoy === null || $w_hoy <= 0) return null;
        if ($pesoObjetivo_kg <= $w_hoy) {
            return [
                'dias' => 0,
                'fecha' => today(),
                'peso_promedio_kg' => $w_hoy,
                'biomasa_kg' => $this->estimarBiomasaKg($lote)
            ];
        }
        $t     = log($pesoObjetivo_kg / $w_hoy) / ($this->SGR_percent_day / 100.0);
        $dias  = (int) ceil($t);
        $fecha = today()->addDays($dias);
        $bio   = round($pesoObjetivo_kg * max(0, (int)$lote->cantidad_actual_real), 2);

        return ['dias' => $dias, 'fecha' => $fecha, 'peso_promedio_kg' => $pesoObjetivo_kg, 'biomasa_kg' => $bio];
    }
}
