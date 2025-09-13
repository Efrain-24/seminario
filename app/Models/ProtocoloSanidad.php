<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtocoloSanidad extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_implementacion',
        'responsable',
        'actividades',
        'version',
        'estado',
        'protocolo_base_id',
    ];

    protected $casts = [
        'actividades' => 'array',
    ];

    // Scope para obtener solo protocolos vigentes
    public function scopeVigentes($query)
    {
        return $query->where('estado', 'vigente');
    }

    // Relación con el protocolo base (protocolo original)
    public function protocoloBase()
    {
        return $this->belongsTo(ProtocoloSanidad::class, 'protocolo_base_id');
    }

    // Relación con las versiones derivadas
    public function versiones()
    {
        return $this->hasMany(ProtocoloSanidad::class, 'protocolo_base_id');
    }

    // Método para crear nueva versión
    public function crearNuevaVersion($data)
    {
        // Marcar la versión actual como obsoleta
        $this->update(['estado' => 'obsoleta']);
        
        // Obtener el protocolo base (si existe) o usar el actual como base
        $protocoloBaseId = $this->protocolo_base_id ?? $this->id;
        
        // Obtener la siguiente versión
        $siguienteVersion = ProtocoloSanidad::where('protocolo_base_id', $protocoloBaseId)
                                          ->orWhere('id', $protocoloBaseId)
                                          ->max('version') + 1;
        
        // Crear nueva versión
        return ProtocoloSanidad::create([
            'nombre' => $data['nombre'] ?? $this->nombre,
            'descripcion' => $data['descripcion'] ?? $this->descripcion,
            'fecha_implementacion' => $data['fecha_implementacion'] ?? now()->toDateString(),
            'responsable' => $data['responsable'] ?? $this->responsable,
            'actividades' => $data['actividades'] ?? $this->actividades,
            'version' => $siguienteVersion,
            'estado' => 'vigente',
            'protocolo_base_id' => $protocoloBaseId,
        ]);
    }

    // Obtener nombre completo with versión
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' (v' . $this->version . ')';
    }
}
