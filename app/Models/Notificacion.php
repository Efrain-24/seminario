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
        'user_id'
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

    public function scopeNoResueltas($query)
    {
        return $query->where('resuelta', false);
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
        })->where('resuelta', false);
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

    public function marcarComoResuelta(): bool
    {
        return $this->update(['resuelta' => true]);
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
            'url' => route('produccion.inventario.index')
        ]);
    }

    public static function crearAlertaProduccion(array $datos): void
    {
        self::create([
            'tipo' => 'error',
            'titulo' => 'Anomalía de Producción',
            'mensaje' => $datos['mensaje'],
            'datos' => $datos,
            'icono' => 'alert-triangle',
            'url' => route('produccion.lotes')
        ]);
    }

    public static function crearNotificacionLimpieza(array $datos): self
    {
        return self::create([
            'tipo' => $datos['tipo'] ?? 'info',
            'titulo' => $datos['titulo'],
            'mensaje' => $datos['mensaje'],
            'datos' => $datos,
            'icono' => $datos['icono'] ?? 'droplets',
            'url' => $datos['url'] ?? null,
            'user_id' => $datos['user_id'] ?? null,
        ]);
    }

    public static function crearNotificacionStock(array $datos): self
    {
        return self::create([
            'tipo' => $datos['tipo'] ?? 'warning',
            'titulo' => $datos['titulo'],
            'mensaje' => $datos['mensaje'],
            'datos' => $datos,
            'icono' => $datos['icono'] ?? 'package-x',
            'url' => $datos['url'] ?? null,
            'user_id' => $datos['user_id'] ?? null,
        ]);
    }

    public static function crearNotificacionConsumo(array $datos): self
    {
        return self::create([
            'tipo' => $datos['tipo'] ?? 'warning',
            'titulo' => $datos['titulo'],
            'mensaje' => $datos['mensaje'],
            'datos' => $datos,
            'icono' => $datos['icono'] ?? 'trending-up',
            'url' => $datos['url'] ?? null,
            'user_id' => $datos['user_id'] ?? null,
        ]);
    }
}
