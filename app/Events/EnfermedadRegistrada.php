<?php
namespace App\Events;

use App\Models\Enfermedad;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnfermedadRegistrada
{
    use Dispatchable, SerializesModels;
    public function __construct(public Enfermedad $enfermedad) {}
}
