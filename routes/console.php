<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\GenerarNotificacionesAutomaticas;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar la generación automática de notificaciones
Artisan::command('notificaciones:auto', function () {
    $this->info('🔄 Generando notificaciones automáticas...');
    GenerarNotificacionesAutomaticas::dispatch();
    $this->info('✅ Job de notificaciones automáticas programado');
})->purpose('Generar notificaciones automáticas basadas en alertas del sistema');

// Programar tareas automáticas
Schedule::job(new GenerarNotificacionesAutomaticas())->everyTenMinutes()->name('notificaciones-auto');
Schedule::command('notificaciones:generar-reales')->hourly()->name('notificaciones-reales-hourly');

Schedule::command('banguat:tipo-cambio')->dailyAt('08:00');
