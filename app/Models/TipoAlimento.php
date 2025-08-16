<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoAlimento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'marca',
        'categoria',
        'proteina',
        'grasa',
        'fibra',
        'humedad',
        'ceniza',
        'presentacion',
        'peso_presentacion',
        'costo_por_kg',
        'descripcion',
        'activo'
    ];

    protected $casts = [
        'proteina' => 'decimal:2',
        'grasa' => 'decimal:2',
        'fibra' => 'decimal:2',
        'humedad' => 'decimal:2',
        'ceniza' => 'decimal:2',
        'peso_presentacion' => 'decimal:2',
        'costo_por_kg' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function alimentaciones()
    {
        return $this->hasMany(Alimentacion::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeDeCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Accesorios
    public function getNombreCompletoAttribute()
    {
        return $this->marca ? "{$this->marca} - {$this->nombre}" : $this->nombre;
    }

    public function getComposicionNutricionalAttribute()
    {
        $composicion = [];
        
        if ($this->proteina) $composicion[] = "Proteína: {$this->proteina}%";
        if ($this->grasa) $composicion[] = "Grasa: {$this->grasa}%";
        if ($this->fibra) $composicion[] = "Fibra: {$this->fibra}%";
        
        return implode(' | ', $composicion);
    }

    // Constantes para las opciones
    public static function getCategorias()
    {
        return [
            'concentrado' => 'Concentrado',
            'pellet' => 'Pellet',
            'hojuela' => 'Hojuela',
            'artesanal' => 'Artesanal',
            'vivo' => 'Alimento Vivo',
            'suplemento' => 'Suplemento'
        ];
    }

    public static function getPresentaciones()
    {
        return [
            'sacos' => 'Sacos',
            'bolsas' => 'Bolsas',
            'granel' => 'Granel',
            'unidades' => 'Unidades'
        ];
    }
}
