<?php
// app/Observers/AlimentacionObserver.php
namespace App\Observers;

use App\Models\Alimentacion;
use App\Models\InventarioItem;
use App\Models\Bodega;
use App\Services\InventarioService;

class AlimentacionObserver
{
    public function created(Alimentacion $a): void
    {
        $this->registrarSalida($a);
    }

    public function updated(Alimentacion $a): void
    {
        // revertimos movimiento anterior y aplicamos nuevo (simple: por ahora aplicamos delta)
        if ($a->wasChanged(['tipo_alimento_id', 'cantidad_kg', 'cantidad_libras', 'bodega_id', 'fecha'])) {
            // Por simplicidad, registra una salida por la diferencia. Puedes llevar historial si quieres.
            // Aquí haremos: revertir todo anterior no es trivial sin persistir el movimiento_id.
            // Recomendación: guarda movement_id en alimentacions para poder revertir exactamente.
        }
    }

    public function deleted(Alimentacion $a): void
    {
        // (opcional) revertir: requiere haber guardado el movement_id en la creación
    }

    protected function registrarSalida(Alimentacion $a): void
    {
        if (!$a->bodega_id) return;
        $tipo = $a->tipoAlimento()->first();
        if (!$tipo || !$tipo->inventario_item_id) return;

        $item = InventarioItem::find($tipo->inventario_item_id);
        $bodega = Bodega::find($a->bodega_id);
        if (!$item || !$bodega) return;

        $svc = app(InventarioService::class);

        // usar kg si tienes ese campo; si almacenas libras, cámbialo aquí
        $unidad = isset($a->cantidad_kg) ? 'kg' : 'lb';
        $cantidad = isset($a->cantidad_kg) ? (float)$a->cantidad_kg : (float)($a->cantidad_libras ?? $a->cantidad_lb);

        $svc->salida($item, $bodega, $cantidad, $unidad, 'Consumo por alimentación', $a);
    }
}
