<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            abort(403, 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            // Si ya está verificado, autenticar y redirigir
            Auth::login($user);
            return redirect()->route('dashboard')
                ->with('success', 'Tu email ya estaba verificado. ¡Bienvenido!');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        // Autenticar al usuario después de la verificación
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', '¡Email verificado exitosamente! Bienvenido al sistema.');
    }
}
