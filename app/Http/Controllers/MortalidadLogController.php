<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mortalidad;
use App\Models\Lote;

class MortalidadLogController extends Controller
{
    public function show($lote_id)
    {
        $lote = Lote::findOrFail($lote_id);
        $mortalidades = Mortalidad::where('lote_id', $lote_id)->orderByDesc('fecha')->get();
        return view('mortalidades.log', compact('lote', 'mortalidades'));
    }
}
