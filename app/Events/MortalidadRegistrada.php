<?php
namespace App\Events;

use App\Models\Mortalidad;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MortalidadRegistrada
{
    use Dispatchable, SerializesModels;
    public function __construct(public Mortalidad $mortalidad) {}
}
