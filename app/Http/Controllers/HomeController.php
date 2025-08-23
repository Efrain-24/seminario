<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Manejar la página de inicio.
     * Redirige a aplicaciones si está autenticado, sino muestra welcome.
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('aplicaciones');
        }
        
        return view('welcome');
    }
}
