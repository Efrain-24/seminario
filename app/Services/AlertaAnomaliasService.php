<?php

namespace App\Services;

use App\Models\{Lote, Seguimiento, Alimentacion, Mortalidad};
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class AlertaAnomaliasService
{
    public function __construct(
        private float $FCR = 1.6,       // eficiencia de conversión
        private float $tolerancia = 0.20, // 20% por debajo de lo esperado => alerta
        private int   $minDias = 5,     // mínimo días entre biometrías a evaluar
        private float $minFeedKg = 1.0  // al menos 1 kg registrado en el periodo
    ) {}

    /** Descubre la columna de fecha en una tabla */
    protected function colFecha(string $table, array $candidatas = [
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
        foreach ($candidatas as $c) if (in_array($c, $cols, true)) return $c;
        return 'created_at';
    }

    /** Descubre una columna de peso promedio (kg/pez) en Seguimiento */
    protected function leerPesoSeguimiento(array $a): ?float
    {
        $peso = $a['peso_promedio_kg'] ?? $a['peso_promedio'] ?? $a['peso_muestra_kg']
            ?? $a['peso_muestra'] ?? $a['peso'] ?? null;
        return $peso !== null ? (float)$peso : null;
    }

    /**
     * Devuelve una alerta (array) o null si no hay anomalía.
     * Lógica:
     *  - Toma las 2 últimas biometrías del lote (s0, s1).
     *  - Suma alimento (kg) entre s0->fecha y s1->fecha.
     *  - Estima ganancia esperada = (feed_kg / FCR) * 1000 / población_prom.
     *  - Observada = peso1 - peso0.
     *  - Si feed >= minFeedKg y días >= minDias y observada < esperada*(1 - tolerancia) => alerta.
     */
    public function detectarBajoPeso(Lote $lote): ?array
    {
        $tablaSeg   = (new Seguimiento)->getTable();
        $fSeg       = $this->colFecha($tablaSeg);

        $seg = Seguimiento::where('lote_id', $lote->id)
            ->orderBy($fSeg, 'desc')
            ->take(2)
            ->get();

        if ($seg->count() < 2) return null;

        $s1 = $seg[0]; // más reciente
        $s0 = $seg[1];

        $peso1 = $this->leerPesoSeguimiento($s1->getAttributes());
        $peso0 = $this->leerPesoSeguimiento($s0->getAttributes());
        if ($peso0 === null || $peso1 === null) return null;

        $fecha0 = Carbon::parse($s0->{$fSeg});
        $fecha1 = Carbon::parse($s1->{$fSeg});
        $dias   = $fecha0->diffInDays($fecha1);
        if ($dias < $this->minDias) return null;

        // Alimentación en el periodo (fecha0, fecha1]
        $tablaAli = (new Alimentacion)->getTable();
        $fAli     = $this->colFecha($tablaAli);
        $feedRegs = Alimentacion::where('lote_id', $lote->id)
            ->whereDate($fAli, '>', $fecha0)
            ->whereDate($fAli, '<=', $fecha1)
            ->get();

        $kg = 0.0;
        foreach ($feedRegs as $r) {
            $a = $r->getAttributes();
            if (array_key_exists('cantidad_kg', $a))          $kg += (float)$a['cantidad_kg'];
            elseif (array_key_exists('cantidad_libras', $a))  $kg += (float)$a['cantidad_libras'] * 0.45359237;
            elseif (array_key_exists('cantidad_lb', $a))      $kg += (float)$a['cantidad_lb'] * 0.45359237;
            elseif (array_key_exists('cantidad', $a))         $kg += (float)$a['cantidad'];
        }
        if ($kg < $this->minFeedKg) return null;

        // Población promedio aproximada del periodo (considera mortalidad en el rango)
        $muertes = Mortalidad::where('lote_id', $lote->id)
            ->whereDate('fecha', '>', $fecha0)->whereDate('fecha', '<=', $fecha1)
            ->sum('cantidad');

        $pobl_fin   = (int) $lote->cantidad_actual;
        $pobl_ini   = max(1, $pobl_fin + (int)$muertes);     // antes de morir
        $pobl_prom  = max(1, (int) floor(($pobl_ini + $pobl_fin) / 2));

        $gananciaEsperada_kg = ($kg / $this->FCR) / $pobl_prom;
        $gananciaObservada_kg = $peso1 - $peso0;

        if ($gananciaEsperada_kg <= 0) return null;

        $deficit = $gananciaEsperada_kg - $gananciaObservada_kg;
        $defPct  = $deficit / $gananciaEsperada_kg; // 0..1

        if ($defPct >= $this->tolerancia) {
            return [
                'lote_id'             => $lote->id,
                'codigo_lote'         => $lote->codigo_lote,
                'desde'               => $fecha0->toDateString(),
                'hasta'               => $fecha1->toDateString(),
                'dias'                => $dias,
                'pobl_prom'           => $pobl_prom,
                'alimento_kg'         => round($kg, 2),
                'peso_inicial_kg'      => round($peso0, 3),
                'peso_final_kg'        => round($peso1, 3),
                'ganancia_esperada_kg' => round($gananciaEsperada_kg, 3),
                'ganancia_observada_kg' => round($gananciaObservada_kg, 3),
                'deficit_kg'           => round($deficit, 3),
                'deficit_pct'         => round($defPct * 100, 2),
                'fcr'                 => $this->FCR,
                'tolerancia_pct'      => $this->tolerancia * 100,
            ];
        }

        return null;
    }
}
