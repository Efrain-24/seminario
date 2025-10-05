<?php
namespace App\Events;

use App\Models\Lote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DensidadActualizada
{
    use Dispatchable, SerializesModels;
    public function __construct(public Lote $lote) {}
}
