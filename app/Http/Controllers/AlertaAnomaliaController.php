<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lote;
use App\Services\AlertaAnomaliasService;

class AlertaAnomaliaController extends Controller
{
    public function index(Request $request)
    {
        $fcr        = (float) $request->input('fcr', 1.6);
        $tol        = (float) $request->input('tol', 20) / 100.0; // %
        $minDias    = (int) $request->input('min_dias', 5);
        $minFeedKg  = (float) $request->input('min_feed_kg', 1);

        $svc  = new AlertaAnomaliasService($fcr, $tol, $minDias, $minFeedKg);
        $loteId = $request->input('lote_id');

        $lotes = Lote::orderBy('codigo_lote')->get();
        $list  = [];

        $iter = $loteId ? $lotes->where('id', (int)$loteId) : $lotes;

        foreach ($iter as $lote) {
            if ($alerta = $svc->detectarBajoPeso($lote)) {
                $list[] = $alerta;
            }
        }

        // Ordenar por mayor déficit
        usort($list, fn($a, $b) => $b['deficit_pct'] <=> $a['deficit_pct']);

        return view('produccion.alertas.index', [
            'lotes'     => $lotes,
            'loteId'    => $loteId,
            'fcr'       => $fcr,
            'tol'       => $tol * 100,
            'minDias'   => $minDias,
            'minFeedKg' => $minFeedKg,
            'alertas'   => $list,
        ]);
    }
}
