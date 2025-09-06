<?php
namespace App\Observers;

use App\Models\Lote;
use App\Events\DensidadActualizada;

class LoteObserver
{
    public function saved(Lote $lote): void
    {
        DensidadActualizada::dispatch($lote);
    }
}
