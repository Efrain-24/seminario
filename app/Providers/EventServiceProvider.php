<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\MortalidadRegistrada::class => [
            \App\Listeners\EvaluarMortalidadElevada::class,
        ],
        \App\Events\DensidadActualizada::class => [
            \App\Listeners\EvaluarDensidadCritica::class,
        ],
        \App\Events\EnfermedadRegistrada::class => [
            \App\Listeners\EvaluarRegistroEnfermedad::class,
        ],
    ];
}
