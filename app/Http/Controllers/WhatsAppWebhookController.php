<?php

namespace App\Http\Controllers;

use App\Models\WhatsappMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppWebhookController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $result = $this->whatsappService->verifyWebhook($mode, $token, $challenge);

        if ($result) {
            return response($result, 200)->header('Content-Type', 'text/plain');
        }

        return response()->json(['error' => 'Verification failed'], 403);
    }

    public function webhook(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('Webhook received', ['data' => $data]);

            if (!isset($data['entry'])) {
                return response()->json(['status' => 'no entry'], 200);
            }

            foreach ($data['entry'] as $entry) {
                $parsedMessage = $this->whatsappService->parseWebhookMessage($entry);

                if (!$parsedMessage) {
                    continue;
                }

                $existingMessage = WhatsappMessage::where('message_id', $parsedMessage['message_id'])->first();
                
                if ($existingMessage) {
                    Log::info('Duplicate message ignored', ['message_id' => $parsedMessage['message_id']]);
                    continue;
                }

                $message = WhatsappMessage::create($parsedMessage);

                $forwardResult = $this->whatsappService->forwardMessageToAdmin(
                    $message->from_number,
                    $message->from_name,
                    $message->message_body
                );

                if ($forwardResult['success']) {
                    $message->update([
                        'forwarded_to_admin' => true,
                        'forwarded_at' => now()
                    ]);

                    Log::info('Message forwarded to admin', ['message_id' => $message->id]);
                } else {
                    Log::error('Failed to forward message to admin', [
                        'message_id' => $message->id,
                        'error' => $forwardResult['error']
                    ]);
                }
            }

            return response()->json(['status' => 'success'], 200);

        } catch (Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error'], 200);
        }
    }
}
