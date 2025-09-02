<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestBcvWebhook extends Command
{
    protected $signature = 'bcv:test-webhook {--url= : URL del webhook a probar} {--secret= : Secret para autenticación}';
    protected $description = 'Prueba el webhook del BCV para verificar que funcione correctamente';

    public function handle()
    {
        $webhookUrl = $this->option('url') ?: config('app.url') . '/webhook/bcv/update-rates';
        $secret = $this->option('secret') ?: config('services.bcv.webhook_secret');

        $this->info("🧪 Probando webhook BCV...");
        $this->line("URL: {$webhookUrl}");
        $this->line("Secret: " . ($secret ? 'Configurado' : 'No configurado'));

        try {
            $payload = [
                'currency' => 'all',
                'timestamp' => now()->toISOString(),
            ];

            if ($secret) {
                $payload['secret'] = $secret;
            }

            $this->line("📤 Enviando payload: " . json_encode($payload));

            $response = Http::timeout(60)->post($webhookUrl, $payload);

            $this->line("📥 Respuesta recibida:");
            $this->line("Status: " . $response->status());
            $this->line("Body: " . $response->body());

            if ($response->successful()) {
                $this->info("✅ Webhook funcionando correctamente!");
                
                $data = $response->json();
                if (isset($data['success']) && $data['success']) {
                    $this->info("✅ Tasas del BCV actualizadas exitosamente");
                    $this->line("Comando ejecutado: " . ($data['command'] ?? 'N/A'));
                    $this->line("Timestamp: " . ($data['timestamp'] ?? 'N/A'));
                }
            } else {
                $this->error("❌ Webhook falló con status: " . $response->status());
                $this->line("Error: " . $response->body());
            }

        } catch (\Exception $e) {
            $this->error("❌ Error al probar webhook: " . $e->getMessage());
            Log::error('Error probando webhook BCV', [
                'error' => $e->getMessage(),
                'webhook_url' => $webhookUrl
            ]);
        }
    }
}
