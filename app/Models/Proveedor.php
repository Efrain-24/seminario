<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    
    protected $fillable = [
        // Información básica
        'nombre',
        'nit',
        'codigo',
        'tipo',
        'categoria',
        'estado',
        
        // Contacto
        'telefono_principal',
        'telefono_secundario',
        'email',
        'sitio_web',
        
        // Dirección
        'direccion',
        'departamento',
        'municipio',
        'zona',
        'codigo_postal',
        
        // Información comercial
        'limite_credito',
        'dias_credito',
        'forma_pago_preferida',
        'moneda_preferida',
        
        // Calificación
        'calificacion',
        'total_evaluaciones',
        'tiempo_entrega_promedio',
        'porcentaje_cumplimiento',
        
        // Información financiera
        'saldo_actual',
        'total_compras_mes',
        'total_compras_historico',
        'fecha_ultima_compra',
        'fecha_ultimo_pago',
        
        // Contacto comercial
        'contacto_comercial_nombre',
        'contacto_comercial_telefono',
        'contacto_comercial_email',
        'contacto_comercial_cargo',
        
        // Información adicional
        'especialidades',
        'condiciones_especiales',
        'notas',
        'requiere_orden_compra',
        'acepta_devoluciones',
        
        // Certificaciones
        'certificaciones',
        'documentos',
        
        // Auditoría
        'registrado_por',
        'actualizado_por'
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2',
        'dias_credito' => 'integer',
        'calificacion' => 'decimal:2',
        'total_evaluaciones' => 'integer',
        'tiempo_entrega_promedio' => 'decimal:2',
        'porcentaje_cumplimiento' => 'decimal:2',
        'saldo_actual' => 'decimal:2',
        'total_compras_mes' => 'decimal:2',
        'total_compras_historico' => 'decimal:2',
        'fecha_ultima_compra' => 'date',
        'fecha_ultimo_pago' => 'date',
        'requiere_orden_compra' => 'boolean',
        'acepta_devoluciones' => 'boolean',
        'certificaciones' => 'array',
        'documentos' => 'array',
        'fecha_registro' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relaciones
    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    // Aquí se pueden agregar relaciones con órdenes de compra, compras, etc.
    // public function ordenes(): HasMany
    // {
    //     return $this->hasMany(OrdenCompra::class, 'proveedor_id');
    // }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeConCredito($query)
    {
        return $query->where('dias_credito', '>', 0);
    }

    public function scopeConSaldoPendiente($query)
    {
        return $query->where('saldo_actual', '>', 0);
    }

    // Mutators
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucwords(strtolower(trim($value)));
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? strtolower(trim($value)) : null;
    }

    public function setNitAttribute($value)
    {
        // Limpiar NIT de caracteres especiales
        $this->attributes['nit'] = $value ? preg_replace('/[^0-9A-Za-z]/', '', $value) : null;
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        $nombre = $this->nombre;
        if ($this->nit) {
            $nombre .= " (NIT: {$this->nit})";
        }
        return $nombre;
    }

    public function getCodigoFormateadoAttribute()
    {
        return strtoupper($this->codigo);
    }

    public function getTelefonoFormateoAttribute()
    {
        if (!$this->telefono_principal) {
            return 'Sin teléfono';
        }
        
        $telefono = $this->telefono_principal;
        if ($this->telefono_secundario) {
            $telefono .= " / {$this->telefono_secundario}";
        }
        
        return $telefono;
    }

    public function getDireccionCompletaAttribute()
    {
        $direccion = [];
        
        if ($this->direccion) {
            $direccion[] = $this->direccion;
        }
        
        if ($this->zona) {
            $direccion[] = "Zona {$this->zona}";
        }
        
        if ($this->municipio && $this->departamento) {
            $direccion[] = "{$this->municipio}, {$this->departamento}";
        } elseif ($this->municipio) {
            $direccion[] = $this->municipio;
        } elseif ($this->departamento) {
            $direccion[] = $this->departamento;
        }
        
        return !empty($direccion) ? implode(', ', $direccion) : 'Dirección no especificada';
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'activo' => ['text' => 'Activo', 'class' => 'bg-green-100 text-green-800'],
            'inactivo' => ['text' => 'Inactivo', 'class' => 'bg-gray-100 text-gray-800'],
            'suspendido' => ['text' => 'Suspendido', 'class' => 'bg-red-100 text-red-800']
        ];
        
        return $badges[$this->estado] ?? ['text' => 'Desconocido', 'class' => 'bg-gray-100 text-gray-800'];
    }

    public function getCategoriaBadgeAttribute()
    {
        $badges = [
            'alimentos' => ['text' => 'Alimentos', 'class' => 'bg-blue-100 text-blue-800'],
            'insumos' => ['text' => 'Insumos', 'class' => 'bg-purple-100 text-purple-800'],
            'equipos' => ['text' => 'Equipos', 'class' => 'bg-indigo-100 text-indigo-800'],
            'servicios' => ['text' => 'Servicios', 'class' => 'bg-yellow-100 text-yellow-800'],
            'medicamentos' => ['text' => 'Medicamentos', 'class' => 'bg-red-100 text-red-800'],
            'mixto' => ['text' => 'Mixto', 'class' => 'bg-gray-100 text-gray-800']
        ];
        
        return $badges[$this->categoria] ?? ['text' => 'Otros', 'class' => 'bg-gray-100 text-gray-800'];
    }

    public function getCalificacionEstrellas()
    {
        if (!$this->calificacion) {
            return 'Sin calificar';
        }
        
        $estrellas = '';
        $rating = (float) $this->calificacion;
        
        for ($i = 1; $i <= 5; $i++) {
            if ($rating >= $i) {
                $estrellas .= '★';
            } elseif ($rating >= $i - 0.5) {
                $estrellas .= '☆';
            } else {
                $estrellas .= '☆';
            }
        }
        
        return $estrellas . " ({$rating}/5.0)";
    }

    public function getLimiteCreditoFormateadoAttribute()
    {
        if (!$this->limite_credito) {
            return 'Sin límite establecido';
        }
        
        return $this->moneda_preferida . ' ' . number_format($this->limite_credito, 2);
    }

    public function getSaldoFormateadoAttribute()
    {
        $prefijo = $this->saldo_actual > 0 ? '+' : '';
        return $prefijo . $this->moneda_preferida . ' ' . number_format($this->saldo_actual, 2);
    }

    // Métodos utilitarios
    public function esActivo(): bool
    {
        return $this->estado === 'activo';
    }

    public function tieneLimiteCredito(): bool
    {
        return !is_null($this->limite_credito) && $this->limite_credito > 0;
    }

    public function tieneCredito(): bool
    {
        return $this->dias_credito > 0;
    }

    public function tieneSaldoPendiente(): bool
    {
        return $this->saldo_actual > 0;
    }

    public function esComprador(): bool
    {
        return $this->total_compras_historico > 0;
    }

    public function diasSinCompras(): ?int
    {
        if (!$this->fecha_ultima_compra) {
            return null;
        }
        
        return Carbon::now()->diffInDays($this->fecha_ultima_compra);
    }

    public function actualizarCalificacion(float $nuevaCalificacion): void
    {
        $totalEvaluaciones = $this->total_evaluaciones;
        $calificacionActual = $this->calificacion ?? 0;
        
        // Calcular nueva calificación promedio
        $nuevaCalificacionPromedio = (($calificacionActual * $totalEvaluaciones) + $nuevaCalificacion) / ($totalEvaluaciones + 1);
        
        $this->update([
            'calificacion' => round($nuevaCalificacionPromedio, 2),
            'total_evaluaciones' => $totalEvaluaciones + 1
        ]);
    }

    public function actualizarSaldo(float $monto, string $tipo = 'suma'): void
    {
        $nuevoSaldo = $tipo === 'suma' 
            ? $this->saldo_actual + $monto 
            : $this->saldo_actual - $monto;
            
        $this->update(['saldo_actual' => $nuevoSaldo]);
    }

    public function registrarCompra(float $monto): void
    {
        $this->update([
            'total_compras_mes' => $this->total_compras_mes + $monto,
            'total_compras_historico' => $this->total_compras_historico + $monto,
            'fecha_ultima_compra' => now()->toDateString()
        ]);
    }

    public function generarCodigoUnico(): string
    {
        // Prefijo según tipo
        $prefijos = [
            'empresa' => 'EMP',
            'persona' => 'PER', 
            'cooperativa' => 'COO'
        ];
        
        $prefijo = $prefijos[$this->tipo] ?? 'PRV';
        
        // Sufijo según categoría
        $sufijos = [
            'alimentos' => 'ALM',
            'insumos' => 'INS',
            'equipos' => 'EQP',
            'servicios' => 'SRV',
            'medicamentos' => 'MED',
            'mixto' => 'MIX'
        ];
        
        $sufijo = $sufijos[$this->categoria] ?? 'GEN';
        
        // Generar número único
        do {
            $numero = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $codigo = "{$prefijo}-{$sufijo}-{$numero}";
        } while (self::where('codigo', $codigo)->exists());
        
        return $codigo;
    }

    // Boot method para generar código automáticamente
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($proveedor) {
            if (empty($proveedor->codigo)) {
                $proveedor->codigo = $proveedor->generarCodigoUnico();
            }
            
            $proveedor->registrado_por = Auth::id();
            $proveedor->fecha_registro = now();
        });
        
        static::updating(function ($proveedor) {
            $proveedor->actualizado_por = Auth::id();
            $proveedor->fecha_actualizacion = now();
        });
    }
}