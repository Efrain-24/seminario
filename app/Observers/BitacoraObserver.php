<?php
namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Bitacora;

class BitacoraObserver
{
    public function created($model)
    {
        $this->log('creado', $model);
    }
    public function updated($model)
    {
        // Capturar solo los campos cambiados
        $cambios = $model->getChanges();
        $original = $model->getOriginal();
        unset($cambios['updated_at']);
        $detallesCambios = collect($cambios)
            ->reject(function($valor, $campo){
                return in_array($campo, ['password','remember_token']);
            })
            ->map(function($nuevo, $campo) use ($original){
                $viejo = $original[$campo] ?? null;
                if ($viejo === $nuevo) return null;
                return ucfirst(str_replace('_',' ', $campo)).": '".($viejo===null? 'N/A':$viejo)."' => '".($nuevo===null?'N/A':$nuevo)."'";
            })
            ->filter()
            ->implode(' | ');

        $this->log('actualizado', $model, $detallesCambios ?: null);
    }
    public function deleted($model)
    {
        $this->log('eliminado', $model);
    }
    public function restored($model)
    {
        if (method_exists($model, 'trashed')) {
            $this->log('restaurado', $model);
        }
    }
    protected function log($accion, $model)
    {
        $user = Auth::user();
        $atributos = collect($model->getAttributes())
            ->reject(function($valor, $campo){
                return in_array($campo, ['password','remember_token']);
            })
            ->map(function($valor, $campo) {
                return ucfirst(str_replace('_', ' ', $campo)) . ': ' . (is_null($valor) ? 'N/A' : (is_scalar($valor) ? $valor : json_encode($valor)));
            })
            ->implode(' | ');

        $infoCambios = func_num_args() > 2 ? func_get_arg(2) : null; // detalles específicos de cambios
        $detalles = $infoCambios ? ($infoCambios.' || ESTADO ACTUAL => '.$atributos) : $atributos;

        Bitacora::create([
            'user_id' => $user ? $user->id : null,
            'accion' => $accion . ' ' . class_basename($model),
            'detalles' => $detalles,
        ]);
    }
}
