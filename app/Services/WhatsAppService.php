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

    public function getStatus($sessionId = null): array
    {
        try {
            $url = $sessionId 
                ? "{$this->nodeServiceUrl}/status/{$sessionId}"
                : "{$this->nodeServiceUrl}/status";
                
            $response = Http::timeout(5)->get($url);

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
    
    public function initializeSession($sessionId): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->nodeServiceUrl}/initialize", [
                'sessionId' => $sessionId
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'error' => 'Failed to initialize session'
            ];

        } catch (Exception $e) {
            Log::error('WhatsApp initialize session error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
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

    public function sendMessage(string $to, string $message, string $sessionId): array
    {
        try {
            $response = Http::timeout(30)->post("{$this->nodeServiceUrl}/send", [
                'sessionId' => $sessionId,
                'to' => $to,
                'message' => $message
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('WhatsApp message sent successfully', [
                    'to' => $to,
                    'sessionId' => $sessionId,
                    'response' => $data
                ]);

                return $data;
            }

            Log::error('WhatsApp send message error', [
                'to' => $to,
                'sessionId' => $sessionId,
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
                'sessionId' => $sessionId,
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

    public function getChats($sessionId = null): array
    {
        try {
            if (!$sessionId) {
                return [
                    'success' => false,
                    'error' => 'Session ID is required'
                ];
            }
            
            $response = Http::timeout(30)->get("{$this->nodeServiceUrl}/chats/{$sessionId}");

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

    public function getMessages(string $chatId, $sessionId = null, int $limit = 50): array
    {
        try {
            if (!$sessionId) {
                return [
                    'success' => false,
                    'error' => 'Session ID is required'
                ];
            }
            
            $response = Http::timeout(30)->get("{$this->nodeServiceUrl}/messages/{$sessionId}/{$chatId}", [
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
