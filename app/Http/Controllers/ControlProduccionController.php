<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use App\Services\BiomasaService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ControlProduccionController extends Controller
{
    public function __construct(private BiomasaService $svc = new BiomasaService()) {}

    public function index()
    {
        $lotes = Lote::orderBy('codigo_lote')->get();
        $rows = $lotes->map(fn($l) => [
            'lote'            => $l,
            'peso_promedio_g' => $this->svc->estimarPesoPromedioActual($l),
            'biomasa_kg'      => $this->svc->estimarBiomasaKg($l),
        ]);
        return view('produccion.control.index', compact('rows'));
    }

    public function show(Lote $lote)
    {
        // Para el selector de estanque
        $lotes = Lote::orderBy('codigo_lote')->get(['id', 'codigo_lote']);

        $hoy         = now()->toDateString();
        $peso_hoy    = $this->svc->estimarPesoPromedioActual($lote);
        $biomasa_hoy = $this->svc->estimarBiomasaKg($lote);

        return view('produccion.control.show', compact('lote', 'lotes', 'hoy', 'peso_hoy', 'biomasa_hoy'));
    }


    public function predecirHastaFecha(Request $req, Lote $lote)
    {
        $req->validate(['fecha_objetivo' => ['required', 'date', 'after_or_equal:today']]);
        $res = $this->svc->predecirHastaFecha($lote, Carbon::parse($req->fecha_objetivo));
        return back()->with('prediccion_fecha', $res);
    }

    public function predecirParaPeso(Request $req, Lote $lote)
    {
        $req->validate(['peso_objetivo_g' => ['required', 'numeric', 'min:1']]);
        $res = $this->svc->predecirDiaParaPesoObjetivo($lote, (float)$req->peso_objetivo_g);
        return back()->with('prediccion_peso', $res);
    }
}
