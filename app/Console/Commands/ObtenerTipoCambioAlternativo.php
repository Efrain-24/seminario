<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\TipoCambio;
use Carbon\Carbon;

class ObtenerTipoCambioAlternativo extends Command
{
    protected $signature = 'tipo-cambio:alternativo';
    protected $description = 'Obtiene el tipo de cambio desde APIs alternativas cuando Banguat no está disponible';

    public function handle()
    {
        $this->info("🌐 Consultando tipo de cambio desde APIs alternativas...");
        
        // 1. Intentar con exchangerate-api.com (gratuita, 1500 requests/mes)
        if ($tipoCambio = $this->obtenerDesdeExchangeRateAPI()) {
            $this->guardarTipoCambio($tipoCambio);
            return Command::SUCCESS;
        }
        
        // 2. Intentar con fixer.io (100 requests gratis/mes)
        if ($tipoCambio = $this->obtenerDesdeFixer()) {
            $this->guardarTipoCambio($tipoCambio);
            return Command::SUCCESS;
        }
        
        // 3. Fallback al sistema actual
        $this->warn("⚠️ No se pudo obtener desde APIs alternativas, usando fallback local");
        return $this->usarFallback();
    }
    
    private function obtenerDesdeExchangeRateAPI()
    {
        try {
            $this->info("📡 Consultando exchangerate-api.com...");
            
            // API gratuita, no requiere key
            $response = Http::timeout(15)
                ->get('https://api.exchangerate-api.com/v4/latest/USD');
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['rates']['GTQ'])) {
                    $usdToGtq = $data['rates']['GTQ'];
                    $this->info("✅ Éxito desde exchangerate-api.com: $1 USD = Q{$usdToGtq}");
                    return $usdToGtq;
                }
            }
            
        } catch (\Exception $e) {
            $this->warn("❌ Error en exchangerate-api.com: " . $e->getMessage());
        }
        
        return null;
    }
    
    private function obtenerDesdeFixer()
    {
        try {
            $this->info("📡 Consultando fixer.io...");
            
            // Fixer requiere API key, usando endpoint gratuito limitado
            $response = Http::timeout(15)
                ->get('http://data.fixer.io/api/latest', [
                    'access_key' => 'demo', // Cambiar por tu API key real
                    'base' => 'USD',
                    'symbols' => 'GTQ'
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['rates']['GTQ'])) {
                    $usdToGtq = $data['rates']['GTQ'];
                    $this->info("✅ Éxito desde fixer.io: $1 USD = Q{$usdToGtq}");
                    return $usdToGtq;
                }
            }
            
        } catch (\Exception $e) {
            $this->warn("❌ Error en fixer.io: " . $e->getMessage());
        }
        
        return null;
    }
    
    private function guardarTipoCambio($valor)
    {
        $fechaBD = now()->setTimezone('America/Guatemala')->toDateString();
        
        $tipoCambio = TipoCambio::updateOrCreate(
            ['fecha' => $fechaBD],
            ['valor' => $valor]
        );
        
        $this->info("💾 Tipo de cambio guardado: Q{$valor} por US$1.00");
        $this->info("📅 Fecha: {$fechaBD}");
    }
    
    private function usarFallback()
    {
        $valorFallback = 7.75;
        $ultimoTipoCambio = TipoCambio::orderBy('fecha', 'desc')->first();
        
        if ($ultimoTipoCambio && $ultimoTipoCambio->fecha->diffInDays(now()) <= 7) {
            $valorFallback = $ultimoTipoCambio->valor;
            $this->warn("📊 Usando último tipo de cambio conocido: Q{$valorFallback} del {$ultimoTipoCambio->fecha->format('d/m/Y')}");
        }
        
        $fechaBD = now()->setTimezone('America/Guatemala')->toDateString();
        
        TipoCambio::updateOrCreate(
            ['fecha' => $fechaBD],
            ['valor' => $valorFallback]
        );
        
        $this->warn("🔄 Usando valor fallback: Q{$valorFallback}");
        $this->info("📅 Fecha: {$fechaBD}");
        
        return Command::SUCCESS;
    }
}