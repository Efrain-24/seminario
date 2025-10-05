<?php
namespace App\Observers;

use App\Models\Mortalidad;
use App\Events\MortalidadRegistrada;

class MortalidadObserver
{
    public function created(Mortalidad $m): void
    {
        MortalidadRegistrada::dispatch($m);
    }
}
