<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
    protected $accessToken;
    protected $phoneNumberId;
    protected $apiVersion = 'v18.0';

    public function __construct()
    {
        $this->accessToken = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    public function sendMessage(string $to, string $message): array
    {
        try {
            $url = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            $response = Http::withToken($this->accessToken)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'body' => $message
                    ]
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $to,
                    'response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('WhatsApp API error', [
                'to' => $to,
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'error' => $response->json()
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

    public function forwardMessageToAdmin(string $fromNumber, string $fromName, string $messageBody): array
    {
        $adminNumber = config('services.whatsapp.admin_number');
        
        $formattedMessage = "📩 *رسالة جديدة من WhatsApp*\n\n";
        $formattedMessage .= "👤 *المرسل:* {$fromName}\n";
        $formattedMessage .= "📱 *الرقم:* {$fromNumber}\n";
        $formattedMessage .= "💬 *الرسالة:*\n{$messageBody}\n";
        $formattedMessage .= "\n⏰ *الوقت:* " . now()->format('Y-m-d H:i:s');

        return $this->sendMessage($adminNumber, $formattedMessage);
    }

    public function parseWebhookMessage(array $entry): ?array
    {
        try {
            if (!isset($entry['changes'][0]['value']['messages'][0])) {
                return null;
            }

            $change = $entry['changes'][0]['value'];
            $message = $change['messages'][0];
            $contact = $change['contacts'][0] ?? null;

            if ($message['type'] !== 'text') {
                Log::info('Non-text message received', ['type' => $message['type']]);
                return null;
            }

            return [
                'message_id' => $message['id'],
                'from_number' => $message['from'],
                'from_name' => $contact['profile']['name'] ?? 'Unknown',
                'message_body' => $message['text']['body'],
                'message_type' => $message['type'],
                'message_timestamp' => date('Y-m-d H:i:s', $message['timestamp']),
                'raw_data' => $entry
            ];

        } catch (Exception $e) {
            Log::error('Error parsing webhook message', [
                'error' => $e->getMessage(),
                'entry' => $entry
            ]);
            return null;
        }
    }

    public function verifyWebhook(string $mode, string $token, string $challenge): ?string
    {
        $verifyToken = config('services.whatsapp.verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('Webhook verified successfully');
            return $challenge;
        }

        Log::warning('Webhook verification failed', [
            'mode' => $mode,
            'token' => $token
        ]);

        return null;
    }
}
