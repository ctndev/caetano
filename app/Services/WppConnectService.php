<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WppConnectService
{
    private string $baseUrl;
    private string $secret;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.wppconnect.url', 'http://localhost:3001'), '/');
        $this->secret = config('services.wppconnect.secret', '');
    }

    public function getStatus(): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['X-Api-Secret' => $this->secret])
                ->get("{$this->baseUrl}/api/status");

            if ($response->ok()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::warning('WPPConnect status check failed', ['error' => $e->getMessage()]);
        }

        return ['status' => 'offline'];
    }

    public function getQrCode(): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['X-Api-Secret' => $this->secret])
                ->get("{$this->baseUrl}/api/qrcode");

            if ($response->ok()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::warning('WPPConnect QR code fetch failed', ['error' => $e->getMessage()]);
        }

        return ['qrcode' => null];
    }

    public function sendMessage(string $number, string $message): bool
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-Api-Secret' => $this->secret])
                ->post("{$this->baseUrl}/api/send", [
                    'number' => $number,
                    'message' => $message,
                ]);

            return $response->ok();
        } catch (\Exception $e) {
            Log::error('WPPConnect send message failed', [
                'number' => $number,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
