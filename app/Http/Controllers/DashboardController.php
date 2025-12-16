<?php

namespace App\Http\Controllers;

use App\Models\WhatsappMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class DashboardController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {
        $query = WhatsappMessage::query()->recent();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('from_number', 'like', "%{$search}%")
                  ->orWhere('from_name', 'like', "%{$search}%")
                  ->orWhere('message_body', 'like', "%{$search}%");
            });
        }

        if ($request->has('replied')) {
            $query->where('replied', $request->input('replied') === 'yes');
        }

        $messages = $query->paginate(20);

        $stats = [
            'total' => WhatsappMessage::count(),
            'unreplied' => WhatsappMessage::unreplied()->count(),
            'today' => WhatsappMessage::whereDate('message_timestamp', today())->count(),
        ];

        return view('dashboard.index', compact('messages', 'stats'));
    }

    public function show($id)
    {
        $message = WhatsappMessage::findOrFail($id);
        $conversationMessages = WhatsappMessage::fromNumber($message->from_number)
            ->recent()
            ->get();

        return view('dashboard.show', compact('message', 'conversationMessages'));
    }

    public function reply(Request $request, $id)
    {
        try {
            $request->validate([
                'reply_message' => 'required|string|max:4096'
            ]);

            $message = WhatsappMessage::findOrFail($id);

            $result = $this->whatsappService->sendMessage(
                $message->from_number,
                $request->input('reply_message')
            );

            if ($result['success']) {
                $message->update([
                    'replied' => true,
                    'reply_message' => $request->input('reply_message'),
                    'replied_at' => now()
                ]);

                Log::info('Reply sent successfully', [
                    'message_id' => $message->id,
                    'to' => $message->from_number
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال الرد بنجاح'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'فشل إرسال الرد'
            ], 500);

        } catch (Exception $e) {
            Log::error('Reply error', [
                'message_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرد'
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'phone_number' => 'required|string',
                'message' => 'required|string|max:4096'
            ]);

            $result = $this->whatsappService->sendMessage(
                $request->input('phone_number'),
                $request->input('message')
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال الرسالة بنجاح'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'فشل إرسال الرسالة'
            ], 500);

        } catch (Exception $e) {
            Log::error('Send message error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة'
            ], 500);
        }
    }
}
