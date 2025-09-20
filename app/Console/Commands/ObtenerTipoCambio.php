<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\TipoCambio;

class ObtenerTipoCambio extends Command
{
    protected $signature = 'banguat:tipo-cambio';
    protected $description = 'Obtiene el tipo de cambio diario del Banguat y lo guarda en la BD';

    public function handle()
    {
        try {
            // URL del servicio web del Banguat
            $fecha = now()->format('d/m/Y');
            $soapRequest = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <TipoCambioDia xmlns="http://www.banguat.gob.gt/variables/ws/">
      <fechainit>' . $fecha . '</fechainit>
    </TipoCambioDia>
  </soap:Body>
</soap:Envelope>';

            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://www.banguat.gob.gt/variables/ws/TipoCambioDia',
            ])->post('https://www.banguat.gob.gt/variables/ws/TipoCambio.asmx', $soapRequest);

            if ($response->successful()) {
                // Parsear la respuesta XML/SOAP
                $xml = simplexml_load_string($response->body());
                $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
                $xml->registerXPathNamespace('diff', 'http://www.banguat.gob.gt/variables/ws/');
                
                // Buscar el valor del tipo de cambio
                $tipoCambioNodes = $xml->xpath('//diff:TipoCambioDiaResult/diff:CambioDolar/diff:VarDolar');
                
                if (!empty($tipoCambioNodes)) {
                    $valor = (float) $tipoCambioNodes[0]->referencia;
                    $fechaBD = now()->toDateString();

                    // Guardar en la BD si no existe ya
                    $tipoCambio = TipoCambio::updateOrCreate(
                        ['fecha' => $fechaBD],
                        ['valor' => $valor]
                    );

                    $this->info("✅ Tipo de cambio guardado: Q{$valor} por US\$1.00");
                    $this->info("📅 Fecha: {$fechaBD} (Zona horaria: " . config('app.timezone') . ")");
                    return Command::SUCCESS;
                } else {
                    // Fallback: usar un valor aproximado si el servicio falla
                    $valorFallback = 7.75; // Valor aproximado histórico
                    $fechaBD = now()->toDateString();
                    
                    TipoCambio::updateOrCreate(
                        ['fecha' => $fechaBD],
                        ['valor' => $valorFallback]
                    );
                    
                    $this->warn("⚠️  No se pudo obtener tipo de cambio del Banguat. Usando valor aproximado: Q{$valorFallback}");
                    $this->info("📅 Fecha: {$fechaBD} (Zona horaria: " . config('app.timezone') . ")");
                    return Command::SUCCESS;
                }
            } else {
                throw new \Exception('Error en la respuesta del servicio: ' . $response->status());
            }

        } catch (\Exception $e) {
            // En caso de error, usar valor fallback
            $valorFallback = 7.75;
            $fechaBD = now()->toDateString();
            
            TipoCambio::updateOrCreate(
                ['fecha' => $fechaBD],
                ['valor' => $valorFallback]
            );
            
            $this->error("❌ Error al consultar Banguat: " . $e->getMessage());
            $this->warn("🔄 Usando valor fallback: Q{$valorFallback}");
            $this->info("📅 Fecha: {$fechaBD} (Zona horaria: " . config('app.timezone') . ")");
            return Command::FAILURE;
        }
    }
}

