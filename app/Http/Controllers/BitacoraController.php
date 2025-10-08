<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

class BitacoraController extends Controller
{
    // Solo el admin puede ver la bitácora
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'No tienes permisos para acceder a la bitácora.');
        }
        $usuarios = \App\Models\User::orderBy('name')->get();
        $query = Bitacora::with('user')->orderBy('created_at', 'desc');
        if ($request->filled('usuario_id')) {
            $query->where('user_id', $request->usuario_id);
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        $registros = $query->paginate(50);
        // Adaptar los registros para enviar modulo y documento
        foreach ($registros as $registro) {
            // Ejemplo: puedes extraer el módulo y documento de los detalles o de la acción
            // Aquí se asume que los detalles tienen 'modulo:...' y 'documento:...'
            $modulo = null;
            $documento = null;
            $detallesArr = explode('|', $registro->detalles);
            foreach ($detallesArr as $item) {
                $item = trim($item);
                if (stripos($item, 'modulo:') !== false) {
                    $modulo = trim(explode(':', $item, 2)[1]);
                }
                if (stripos($item, 'documento:') !== false) {
                    $documento = trim(explode(':', $item, 2)[1]);
                }
            }
            $registro->modulo = $modulo ?? 'Desconocido';
            $registro->documento = $documento ?? 'Desconocido';
        }
        return view('bitacora.index', compact('registros', 'usuarios'));
    }
}
