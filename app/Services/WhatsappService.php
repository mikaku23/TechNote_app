<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $apiUrl;
    protected string $idInstance;
    protected string $apiToken;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.green_api.api_url'), '/');
        $this->idInstance = config('services.green_api.id_instance');
        $this->apiToken = config('services.green_api.api_token');
    }

    protected function formatChatId(string $phone): string
    {
        $n = preg_replace('/[^0-9]/', '', $phone); // hapus karakter selain angka
        return $n . '@c.us';
    }

    public function sendMessage(string $phone, string $message): bool
    {
        $chatId = $this->formatChatId($phone);

        $url = "{$this->apiUrl}/waInstance{$this->idInstance}/sendMessage/{$this->apiToken}";

        $response = Http::asJson()->post($url, [
            'chatId' => $chatId,
            'message' => $message,
        ]);

        Log::info('GreenAPI WA', [
            'chatId' => $chatId,
            'status' => $response->status(),
            'body' => $response->json(),
        ]);

        return $response->successful() && isset($response->json()['idMessage']);
    }
}
