<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var User $user */
        $user = Auth::user();
        
        // Verificar si el usuario tiene el permiso requerido
        if (!$user->hasPermission($permission)) {
            Log::warning('PERMISO DENEGADO', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'permission_requerido' => $permission,
                'permisos_rol' => $user->roleModel ? $user->roleModel->getPermissionsArray() : null,
                'ruta' => $request->path(),
                'method' => $request->method(),
            ]);
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
