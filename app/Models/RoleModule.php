<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'module',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
