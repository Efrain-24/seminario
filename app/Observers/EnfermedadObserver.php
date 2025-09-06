<?php
namespace App\Observers;

use App\Models\Enfermedad;
use App\Events\EnfermedadRegistrada;

class EnfermedadObserver
{
    public function created(Enfermedad $e): void
    {
        EnfermedadRegistrada::dispatch($e);
    }
}
