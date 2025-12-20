<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    protected $nodeServiceUrl;

    public function __construct()
    {
        $this->nodeServiceUrl = config('services.whatsapp.node_service_url', 'http://localhost:3000');
    }

    public function getStatus(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->nodeServiceUrl}/status");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'ready' => false,
                'error' => 'Failed to connect to WhatsApp service'
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp service connection error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'ready' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getQRCode(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->nodeServiceUrl}/qr");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'error' => 'Failed to get QR code'
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp QR code error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendMessage(string $to, string $message): array
    {
        try {
            $response = Http::timeout(30)->post("{$this->nodeServiceUrl}/send-message", [
                'to' => $to,
                'message' => $message
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('WhatsApp message sent successfully', [
                    'to' => $to,
                    'response' => $data
                ]);

                return $data;
            }

            Log::error('WhatsApp send message error', [
                'to' => $to,
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'error' => $response->json()['error'] ?? 'Failed to send message'
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp send message exception', [
                'to' => $to,
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function logout(): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->nodeServiceUrl}/logout");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'error' => 'Failed to logout'
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp logout error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getChats(): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->nodeServiceUrl}/chats");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'error' => 'Failed to get chats'
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp get chats error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getMessages(string $chatId, int $limit = 50): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->nodeServiceUrl}/messages/{$chatId}", [
                'limit' => $limit
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'error' => 'Failed to get messages'
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp get messages error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
