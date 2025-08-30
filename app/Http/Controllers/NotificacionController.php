<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Jobs\GenerarNotificacionesAutomaticas;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Obtener notificaciones no leídas del usuario actual
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 20); // Por defecto 20, pero puede ser menos para el panel
        
        $notificaciones = Notificacion::noLeidas()
            ->paraUsuario(Auth::id())
            ->vigentes()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $total = Notificacion::noLeidas()
            ->paraUsuario(Auth::id())
            ->vigentes()
            ->count();

        return response()->json([
            'notificaciones' => $notificaciones,
            'total' => $total
        ]);
    }

        /**
     * Marcar una notificación como resuelta (eliminarla)
     */
    public function marcarComoResuelta($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->resuelta = true;
        $notificacion->save();
        
        // Log de la acción
        Log::info("📝 Notificación marcada como resuelta manualmente", [
            'notificacion_id' => $id,
            'titulo' => $notificacion->titulo,
            'tipo_alerta' => data_get($notificacion->datos, 'tipo_alerta')
        ]);
        
        return response()->json(['success' => true, 'message' => 'Notificación marcada como resuelta']);
    }

    /**
     * Obtener todas las notificaciones para la página completa
     */
    public function todas()
    {
        $notificaciones = Notificacion::paraUsuario(Auth::id())
            ->where('resuelta', false)
            ->vigentes()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notificaciones.todas', compact('notificaciones'));
    }

    /**
     * Contar notificaciones no leídas
     */
    public function count(): JsonResponse
    {
                $count = Notificacion::paraUsuario(Auth::id())
                        ->where('resuelta', false)
                        ->where(function ($q) {
                                $q->whereNull('fecha_vencimiento')
                                    ->orWhere('fecha_vencimiento', '>', now());
                        })
                        ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Marcar una notificación como leída
     */
    public function marcarComoLeida(Notificacion $notificacion): JsonResponse
    {
        // Verificar que el usuario pueda marcar esta notificación
        if ($notificacion->user_id && $notificacion->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $notificacion->marcarComoLeida();

        return response()->json(['message' => 'Notificación marcada como leída']);
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function marcarTodasComoLeidas(): JsonResponse
    {
        Notificacion::noLeidas()
            ->paraUsuario(Auth::id())
            ->update(['leida' => true]);

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);
    }

    /**
     * Eliminar una notificación
     */
    public function destroy(Notificacion $notificacion): JsonResponse
    {
        // Verificar que el usuario pueda eliminar esta notificación
        if ($notificacion->user_id && $notificacion->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $notificacion->delete();

        return response()->json(['message' => 'Notificación eliminada']);
    }

    /**
     * Forzar la generación de notificaciones reales
     */
    public function generarReales()
    {
        try {
            // Ejecutar el comando de generación de notificaciones reales
            Artisan::call('notificaciones:generar-reales');
            $output = Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Notificaciones reales generadas correctamente',
                'output' => trim($output)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Programar notificaciones automáticas
     */
    public function programarAutomaticas()
    {
        try {
            GenerarNotificacionesAutomaticas::dispatch();
            
            return response()->json([
                'success' => true,
                'message' => 'Notificaciones automáticas programadas correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al programar notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }
}
