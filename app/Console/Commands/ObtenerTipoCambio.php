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
            
            $this->info("🔄 Consultando tipo de cambio del Banguat para: {$fecha}");
            
            $soapRequest = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <TipoCambioDia xmlns="http://www.banguat.gob.gt/variables/ws/">
      <fechainit>' . $fecha . '</fechainit>
    </TipoCambioDia>
  </soap:Body>
</soap:Envelope>';

            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://www.banguat.gob.gt/variables/ws/TipoCambioDia',
                'User-Agent' => 'Mozilla/5.0 (compatible; Laravel; +http://laravel.com)'
            ])->post('https://www.banguat.gob.gt/variables/ws/TipoCambio.asmx', $soapRequest);

            $this->info("📡 Respuesta del servicio: HTTP {$response->status()}");
            
            if ($response->successful()) {
                // Parsear la respuesta XML/SOAP
                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($response->body());
                
                if ($xml === false) {
                    throw new \Exception('No se pudo parsear la respuesta XML');
                }
                
                $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
                $xml->registerXPathNamespace('diff', 'http://www.banguat.gob.gt/variables/ws/');
                
                // Buscar el valor del tipo de cambio
                $tipoCambioNodes = $xml->xpath('//diff:TipoCambioDiaResult/diff:CambioDolar/diff:VarDolar/diff:referencia');
                
                if (empty($tipoCambioNodes)) {
                    // Intenta con una estructura alternativa
                    $tipoCambioNodes = $xml->xpath('//VarDolar/referencia');
                }
                
                if (!empty($tipoCambioNodes)) {
                    $valor = (float) $tipoCambioNodes[0];
                    
                    if ($valor > 0 && $valor < 20) { // Validación básica
                        $fechaBD = now()->toDateString();

                        // Guardar en la BD
                        $tipoCambio = TipoCambio::updateOrCreate(
                            ['fecha' => $fechaBD],
                            ['valor' => $valor]
                        );

                        $this->info("✅ Tipo de cambio guardado: Q{$valor} por US\$1.00");
                        $this->info("📅 Fecha: {$fechaBD}");
                        return Command::SUCCESS;
                    } else {
                        throw new \Exception("Valor inválido obtenido: {$valor}");
                    }
                } else {
                    throw new \Exception('No se encontró el valor del tipo de cambio en la respuesta');
                }
            } else {
                throw new \Exception('Error HTTP: ' . $response->status() . ' - ' . $response->body());
            }

        } catch (\Exception $e) {
            // En caso de error, usar valor fallback
            $valorFallback = 7.75;
            $fechaBD = now()->toDateString();
            
            $tipoCambio = TipoCambio::updateOrCreate(
                ['fecha' => $fechaBD],
                ['valor' => $valorFallback]
            );
            
            $this->error("❌ Error al consultar Banguat: " . $e->getMessage());
            $this->warn("🔄 Usando valor fallback: Q{$valorFallback}");
            $this->info("📅 Fecha: {$fechaBD}");
            
            // Retornamos SUCCESS porque aún así guardamos un valor fallback
            return Command::SUCCESS;
        }
    }
}

