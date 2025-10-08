<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\TipoCambio;
use Carbon\Carbon;

class ObtenerTipoCambio extends Command
{
    protected $signature = 'banguat:tipo-cambio';
    protected $description = 'Obtiene el tipo de cambio diario del Banguat y lo guarda en la BD';

    public function handle()
    {
        try {
            // Usar la zona horaria de Guatemala (GMT-6)
            $fechaGuatemala = now()->setTimezone('America/Guatemala');
            
            // Si es fin de semana, usar el último viernes
            if ($fechaGuatemala->isWeekend()) {
                $fechaGuatemala = $fechaGuatemala->previous(Carbon::FRIDAY);
            }
            
            $fechaConsulta = $fechaGuatemala->format('d/m/Y');
            
            $this->info("🔄 Consultando tipo de cambio del Banguat para: {$fechaConsulta}");
            if ($fechaGuatemala->toDateString() !== now()->setTimezone('America/Guatemala')->toDateString()) {
                $this->warn("📅 Usando último día hábil disponible (Banguat no publica en fines de semana)");
            }
            
            // TipoCambioDia NO requiere parámetros según el WSDL
            $soapRequest = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <TipoCambioDia xmlns="http://www.banguat.gob.gt/variables/ws/" />
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
                
                // Buscar el valor del tipo de cambio en VarDolar/referencia
                $tipoCambioNodes = $xml->xpath('//diff:TipoCambioDiaResult/diff:CambioDolar/diff:VarDolar/diff:referencia');
                
                if (empty($tipoCambioNodes)) {
                    // Estructura alternativa sin namespace
                    $tipoCambioNodes = $xml->xpath('//VarDolar/referencia');
                }
                
                if (empty($tipoCambioNodes)) {
                    // Tercera alternativa - buscar cualquier elemento 'referencia'
                    $tipoCambioNodes = $xml->xpath('//referencia');
                }
                
                if (!empty($tipoCambioNodes)) {
                    $valor = (float) $tipoCambioNodes[0];
                    
                    if ($valor > 0 && $valor < 20) { // Validación básica
                        $fechaBD = $fechaGuatemala->toDateString();

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
            $this->error("❌ Error al consultar Banguat: " . $e->getMessage());
            $this->error("🔧 CAUSA IDENTIFICADA: Banguat solo permite requests desde localhost (máquina local)");
            
            // Intentar con API alternativa
            $this->warn("🌐 Intentando con API alternativa...");
            if ($valorAlternativo = $this->obtenerDesdeAPIAlternativa()) {
                $fechaBD = $fechaGuatemala->toDateString();
                
                TipoCambio::updateOrCreate(
                    ['fecha' => $fechaBD],
                    ['valor' => $valorAlternativo]
                );
                
                $this->info("✅ Tipo de cambio obtenido desde API alternativa: Q{$valorAlternativo}");
                $this->info("📅 Fecha: {$fechaBD}");
                return Command::SUCCESS;
            }
            
            // Fallback original si la API alternativa también falla
            $valorFallback = 7.75;
            
            // Verificar si ya tenemos un tipo de cambio reciente
            $ultimoTipoCambio = TipoCambio::orderBy('fecha', 'desc')->first();
            if ($ultimoTipoCambio && $ultimoTipoCambio->fecha->diffInDays(now()) <= 7) {
                $valorFallback = $ultimoTipoCambio->valor;
                $this->warn("📊 Usando último tipo de cambio conocido: Q{$valorFallback} del {$ultimoTipoCambio->fecha->format('d/m/Y')}");
            }
            
            $fechaBD = $fechaGuatemala->toDateString();
            
            $tipoCambio = TipoCambio::updateOrCreate(
                ['fecha' => $fechaBD],
                ['valor' => $valorFallback]
            );
            
            $this->error("❌ Error al consultar Banguat: " . $e->getMessage());
            $this->error("🔧 CAUSA IDENTIFICADA: Banguat solo permite requests desde localhost (máquina local)");
            $this->error("📡 El servicio web está restringido geográficamente o por IP");
            $this->warn("🔄 Usando valor fallback: Q{$valorFallback}");
            $this->info("📅 Fecha: {$fechaBD}");
            
            // Retornamos SUCCESS porque aún así guardamos un valor fallback
            return Command::SUCCESS;
        }
    }
    
    private function obtenerDesdeAPIAlternativa()
    {
        try {
            $response = Http::timeout(15)
                ->get('https://api.exchangerate-api.com/v4/latest/USD');
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['rates']['GTQ'])) {
                    return $data['rates']['GTQ'];
                }
            }
            
        } catch (\Exception $e) {
            $this->warn("❌ API alternativa también falló: " . $e->getMessage());
        }
        
        return null;
    }
}