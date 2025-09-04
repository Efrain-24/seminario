<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class RedirectIfTemporaryPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();
            
            // Si el usuario tiene una contraseña temporal y no está en rutas excluidas
            if ($user->hasTemporaryPassword() && 
                !$request->routeIs('password.change') && 
                !$request->routeIs('password.update') &&
                !$request->routeIs('logout')) {
                return redirect()->route('password.change')
                    ->with('warning', 'Debes cambiar tu contraseña temporal antes de continuar.');
            }
        }

        return $next($request);
    }
}
