<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';


    protected $fillable = [
        'tipo',
        'titulo',
        'mensaje',
        'datos',
        'icono',
        'url',
        'leida',
        'resuelta',
        'fecha_vencimiento',
        'user_id',
        'nombre_enfermedad',
        'cantidad_afectados',
        'porcentaje_afectados',
        'nivel_riesgo',
        'estado_tratamiento',
        'descripcion_tratamiento',
        'fecha_deteccion',
        'fecha_inicio_tratamiento'
    ];


    protected $casts = [
        'datos' => 'array',
        'leida' => 'boolean',
        'resuelta' => 'boolean',
        'fecha_vencimiento' => 'datetime'
    ];

    // Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeParaUsuario($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhereNull('user_id');
        });
    }

    public function scopeVigentes($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('fecha_vencimiento')
              ->orWhere('fecha_vencimiento', '>', now());
        });
    }

    // Métodos auxiliares
    public function getIconoClases(): string
    {
        return match ($this->tipo) {
            'error' => 'text-red-500',
            'warning' => 'text-yellow-500',
            'success' => 'text-green-500',
            default => 'text-blue-500'
        };
    }

    public function getBadgeClases(): string
    {
        return match ($this->tipo) {
            'error' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
            'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
            'success' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300',
            default => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
        };
    }

    public function marcarComoLeida(): bool
    {
        return $this->update(['leida' => true]);
    }

    // Métodos estáticos para crear notificaciones
    public static function crearAlertaInventario(array $datos): void
    {
        self::create([
            'tipo' => 'warning',
            'titulo' => 'Alerta de Inventario',
            'mensaje' => $datos['mensaje'],
            'datos' => $datos,
            'icono' => 'package-x',
            'url' => route('produccion.inventario.alertas.index')
        ]);
    }

    public static function crearAlertaEnfermedad(array $datos): void
    {
        $nivelRiesgo = self::calcularNivelRiesgo(
            $datos['porcentaje_afectados'],
            $datos['nombre_enfermedad']
        );

        self::create([
            'tipo' => match($nivelRiesgo) {
                'alto' => 'error',
                'medio' => 'warning',
                'bajo' => 'info'
            },
            'titulo' => '¡Alerta Sanitaria!',
            'mensaje' => "Se ha detectado {$datos['nombre_enfermedad']} en el lote {$datos['codigo_lote']}",
            'datos' => $datos,
            'icono' => 'stethoscope',
            'url' => route('produccion.alertas.index', ['tipo_alerta' => 'enfermedad']),
            'nombre_enfermedad' => $datos['nombre_enfermedad'],
            'cantidad_afectados' => $datos['cantidad_afectados'],
            'porcentaje_afectados' => $datos['porcentaje_afectados'],
            'nivel_riesgo' => $nivelRiesgo,
            'estado_tratamiento' => $datos['estado_tratamiento'] ?? 'Pendiente',
            'descripcion_tratamiento' => $datos['descripcion_tratamiento'] ?? null,
            'fecha_deteccion' => $datos['fecha_deteccion'] ?? now(),
            'fecha_inicio_tratamiento' => $datos['fecha_inicio_tratamiento'] ?? null
        ]);
    }

    protected static function calcularNivelRiesgo(float $porcentajeAfectados, string $nombreEnfermedad): string
    {
        // Lista de enfermedades consideradas de alto riesgo
        $enfermedadesAltoRiesgo = [
            'Columnaris',
            'Saprolegniasis',
            'Streptococcosis',
            'Vibriosis',
            'Furunculosis'
        ];

        // Si la enfermedad está en la lista de alto riesgo o el porcentaje es mayor al 30%
        if (in_array($nombreEnfermedad, $enfermedadesAltoRiesgo) || $porcentajeAfectados > 30) {
            return 'alto';
        }
        
        // Si el porcentaje está entre 10% y 30%
        if ($porcentajeAfectados > 10) {
            return 'medio';
        }

        // Si el porcentaje es menor o igual al 10%
        return 'bajo';
    }
    public static function crearAlertaProduccion(array $datos): void
    {
        self::create([
            'tipo' => 'error',
            'titulo' => 'Anomalía de Producción',
            'mensaje' => $datos['mensaje'],
            'datos' => $datos,
            'icono' => 'alert-triangle',
            'url' => route('produccion.alertas.index')
        ]);
    }
}
