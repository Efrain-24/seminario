<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\User;

class PasswordChangeController extends Controller
{
    /**
     * Mostrar formulario de cambio de contraseña
     */
    public function show(): View
    {
        return view('auth.change-password');
    }

    /**
     * Cambiar la contraseña del usuario
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',      // minúscula
                'regex:/[A-Z]/',      // mayúscula
                'regex:/[0-9]/',      // número
                'regex:/[@$!%*#?&._-]/' // carácter especial
            ],
        ], [
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial.'
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Verificar contraseña actual
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        // Actualizar contraseña y marcar como cambiada
        $user->password = Hash::make($request->password);
        $user->password_changed_at = now();
        $user->save();

        return redirect()->route('dashboard')
                        ->with('success', 'Contraseña cambiada exitosamente.');
    }
}
