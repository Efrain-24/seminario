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
        \App\Events\StockBajoDetectado::class => [
            \App\Listeners\CrearNotificacionStock::class,
        ],
        \App\Events\LimpiezaCompletada::class => [
            \App\Listeners\CrearNotificacionLimpieza::class,
        ],
        \App\Events\ConsumoCriticoDetectado::class => [
            \App\Listeners\CrearNotificacionConsumo::class,
        ],
    ];
}
