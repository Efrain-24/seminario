<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\CosechaParcial;
use App\Models\TipoCambio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['cosechaParcial.lote'])
            ->orderBy('fecha_venta', 'desc')
            ->paginate(15);

        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $cosechas = CosechaParcial::with('lote')
            ->whereDoesntHave('ventas')
            ->orderBy('fecha', 'desc')
            ->get();

        $tipoCambio = TipoCambio::latest()->first();

        return view('ventas.create', compact('cosechas', 'tipoCambio'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cosecha_parcial_id' => 'required|exists:cosechas_parciales,id',
            'cliente' => 'required|string|max:255',
            'telefono_cliente' => 'nullable|string|max:20',
            'email_cliente' => 'nullable|email|max:255',
            'fecha_venta' => 'required|date',
            'cantidad_kg' => 'required|numeric|min:0.01',
            'precio_kg' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque,credito',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        // Calcular totales
        $total = $validated['cantidad_kg'] * $validated['precio_kg'];
        $tipoCambio = TipoCambio::latest()->first();
        $totalUsd = $tipoCambio ? $total / $tipoCambio->venta : $total / 8.0;

        $validated['total'] = $total;
        $validated['tipo_cambio'] = $tipoCambio ? $tipoCambio->venta : 8.0;
        $validated['total_usd'] = $totalUsd;
        $validated['estado'] = 'pendiente';

        $venta = Venta::create($validated);

        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Venta registrada exitosamente');
    }

    public function show(Venta $venta)
    {
        $venta->load(['cosechaParcial.lote']);
        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        $cosechas = CosechaParcial::with('lote')
            ->where(function($query) use ($venta) {
                $query->whereDoesntHave('ventas')
                      ->orWhere('id', $venta->cosecha_parcial_id);
            })
            ->orderBy('fecha', 'desc')
            ->get();

        $tipoCambio = TipoCambio::latest()->first();

        return view('ventas.edit', compact('venta', 'cosechas', 'tipoCambio'));
    }

    public function update(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'cosecha_parcial_id' => 'required|exists:cosechas_parciales,id',
            'cliente' => 'required|string|max:255',
            'telefono_cliente' => 'nullable|string|max:20',
            'email_cliente' => 'nullable|email|max:255',
            'fecha_venta' => 'required|date',
            'cantidad_kg' => 'required|numeric|min:0.01',
            'precio_kg' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque,credito',
            'estado' => 'required|in:pendiente,completada,cancelada',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        // Recalcular totales
        $total = $validated['cantidad_kg'] * $validated['precio_kg'];
        $tipoCambio = TipoCambio::latest()->first();
        $totalUsd = $tipoCambio ? $total / $tipoCambio->venta : $total / 8.0;

        $validated['total'] = $total;
        $validated['tipo_cambio'] = $tipoCambio ? $tipoCambio->venta : 8.0;
        $validated['total_usd'] = $totalUsd;

        $venta->update($validated);

        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Venta actualizada exitosamente');
    }

    public function destroy(Venta $venta)
    {
        $venta->delete();

        return redirect()->route('ventas.index')
            ->with('success', 'Venta eliminada exitosamente');
    }

    public function completar(Venta $venta)
    {
        $venta->update(['estado' => 'completada']);

        // Sugerir descarga del ticket cuando se completa la venta
        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Venta marcada como completada.')
            ->with('ticket_disponible', true);
    }

    public function cancelar(Venta $venta)
    {
        $venta->update(['estado' => 'cancelada']);

        return redirect()->route('ventas.show', $venta)
            ->with('success', 'Venta cancelada');
    }

    /**
     * Generar y descargar ticket de venta en PDF
     */
    public function generarTicket(Venta $venta)
    {
        // Cargar las relaciones necesarias
        $venta->load(['cosechaParcial.lote']);
        
        // Generar el PDF
        $pdf = PDF::loadView('ventas.ticket', compact('venta'));
        
        // Configurar el PDF para ticket (tamaño carta)
        $pdf->setPaper('letter', 'portrait');
        
        // Nombre del archivo
        $nombreArchivo = 'ticket-venta-' . $venta->codigo_venta . '.pdf';
        
        // Retornar el PDF para descarga
        return $pdf->download($nombreArchivo);
    }

    /**
     * Ver ticket de venta en el navegador
     */
    public function verTicket(Venta $venta)
    {
        // Cargar las relaciones necesarias
        $venta->load(['cosechaParcial.lote']);
        
        // Generar el PDF
        $pdf = PDF::loadView('ventas.ticket', compact('venta'));
        $pdf->setPaper('letter', 'portrait');
        
        // Mostrar en el navegador
        return $pdf->stream('ticket-venta-' . $venta->codigo_venta . '.pdf');
    }

    public function panel()
    {
        $mesActual = Carbon::now();
        
        // Estadísticas del mes actual
        $cosechasEsteMes = CosechaParcial::whereMonth('fecha', $mesActual->month)
            ->whereYear('fecha', $mesActual->year)
            ->count();

        $ventasEsteMes = Venta::whereMonth('fecha_venta', $mesActual->month)
            ->whereYear('fecha_venta', $mesActual->year)
            ->sum('total');

        $clientesActivos = Venta::whereMonth('fecha_venta', $mesActual->month)
            ->whereYear('fecha_venta', $mesActual->year)
            ->distinct('cliente')
            ->count('cliente');

        return view('ventas.panel', compact(
            'cosechasEsteMes',
            'ventasEsteMes', 
            'clientesActivos'
        ));
    }
}