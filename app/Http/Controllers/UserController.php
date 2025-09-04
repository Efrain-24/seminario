<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Notifications\UserCreated;
use App\Notifications\PasswordReset;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Obtener roles válidos de la base de datos
        $validRoles = \App\Models\Role::where('is_active', true)->pluck('name')->toArray();
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'in:' . implode(',', $validRoles)],
        ]);

        // Generar contraseña temporal
        $temporaryPassword = User::generateTemporaryPassword();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($temporaryPassword),
            'role' => $request->role,
            'password_changed_at' => null, // Marcar como contraseña temporal
        ]);

        // Enviar correo con contraseña temporal y enlace de verificación
        $user->notify(new UserCreated($user, $temporaryPassword));

        return redirect()->route('users.index')
                        ->with('success', 'Usuario creado exitosamente. Se ha enviado un correo con la contraseña temporal.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Obtener roles válidos de la base de datos
        $validRoles = \App\Models\Role::where('is_active', true)->pluck('name')->toArray();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:' . implode(',', $validRoles)],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'estado' => $request->estado,
        ]);

        // Solo actualizar contraseña si se proporciona
        if ($request->filled('password')) {
            $request->validate([
                'password' => [
                    'required',
                    'confirmed',
                    'min:8',
                    'regex:/[a-z]/',      // minúscula
                    'regex:/[A-Z]/',      // mayúscula
                    'regex:/[0-9]/',      // número
                    'regex:/[@$!%*#?&._-]/' // carácter especial
                ]
            ], [
                'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial.'
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')
                        ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Evitar que el usuario se elimine a sí mismo
        if ($user->id === Auth::user()->id) {
            return redirect()->route('users.index')
                            ->with('error', 'No puedes inactivar tu propia cuenta desde aquí.');
        }

        $user->estado = 'inactivo';
        $user->save();

        return redirect()->route('users.index')
                        ->with('success', 'Usuario inactivado exitosamente.');
    }

    /**
     * Reiniciar la contraseña del usuario
     */
    public function resetPassword(User $user): RedirectResponse
    {
        // Evitar que el usuario reinicie su propia contraseña desde aquí
        if ($user->id === Auth::user()->id) {
            return redirect()->route('users.show', $user)
                            ->with('error', 'No puedes reiniciar tu propia contraseña desde aquí.');
        }

        // Generar nueva contraseña temporal
        $temporaryPassword = User::generateTemporaryPassword();

        // Actualizar la contraseña y marcar como temporal
        $user->update([
            'password' => Hash::make($temporaryPassword),
            'password_changed_at' => null,
        ]);

        // Enviar correo con nueva contraseña temporal
        $user->notify(new PasswordReset($temporaryPassword));

        return redirect()->route('users.show', $user)
                        ->with('success', 'Contraseña reiniciada exitosamente. Se ha enviado un correo con la nueva contraseña temporal.');
    }
}
