<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppWebController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function index()
    {
        $status = $this->whatsapp->getStatus();
        
        if (!$status['ready']) {
            return view('whatsapp.connect', ['status' => $status]);
        }

        return redirect()->route('whatsapp.chats');
    }

    public function connect()
    {
        $status = $this->whatsapp->getStatus();
        return view('whatsapp.connect', ['status' => $status]);
    }

    public function getQRCode()
    {
        $qr = $this->whatsapp->getQRCode();
        return response()->json($qr);
    }

    public function getStatus()
    {
        $status = $this->whatsapp->getStatus();
        return response()->json($status);
    }

    public function chats()
    {
        $status = $this->whatsapp->getStatus();
        
        if (!$status['ready']) {
            return redirect()->route('whatsapp.index');
        }

        return view('whatsapp.chats');
    }

    public function getChats()
    {
        $chats = $this->whatsapp->getChats();
        return response()->json($chats);
    }

    public function getMessages($chatId)
    {
        $messages = $this->whatsapp->getMessages($chatId);
        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string'
        ]);

        $result = $this->whatsapp->sendMessage($request->to, $request->message);
        return response()->json($result);
    }

    public function logout()
    {
        $result = $this->whatsapp->logout();
        return response()->json($result);
    }
}
