<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProduccionController extends Controller
{
    public function index()
    {
        return view('produccion.index');
    }

    public function gestionLotes()
    {
        return view('produccion.lotes');
    }

    public function gestionUnidades()
    {
        return view('produccion.unidades');
    }

    public function gestionTraslados()
    {
        return view('produccion.traslados');
    }

    public function seguimientoLotes()
    {
        return view('produccion.seguimiento_lotes');
    }

    public function seguimientoUnidades()
    {
        return view('produccion.seguimiento_unidades');
    }
}
