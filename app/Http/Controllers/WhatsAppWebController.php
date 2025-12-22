<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use App\Models\WhatsappDevice;
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
        // Return status of all connected devices
        $devices = WhatsappDevice::where('status', 'connected')
            ->select('id', 'name', 'phone_number', 'session_id', 'status')
            ->get();
        
        return response()->json([
            'success' => true,
            'ready' => $devices->isNotEmpty(),
            'devices' => $devices,
            'totalDevices' => $devices->count()
        ]);
    }

    public function chats()
    {
        // Check if any device is connected
        $hasConnectedDevice = WhatsappDevice::where('status', 'connected')->exists();
        
        if (!$hasConnectedDevice) {
            return redirect()->route('admin.devices.index')
                ->with('error', 'يجب توصيل جهاز واتساب أولاً');
        }

        return view('whatsapp.chats');
    }

    public function getChats()
    {
        try {
            $devices = WhatsappDevice::where('status', 'connected')
                ->select('id', 'device_name', 'phone_number', 'session_id')
                ->get();
            
            if ($devices->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'error' => 'لا توجد أجهزة متصلة',
                    'chats' => [],
                    'devices' => []
                ]);
            }
            
            $allChats = [];
            
            // Format devices for frontend
            $formattedDevices = $devices->map(function($device) {
                return [
                    'id' => $device->id,
                    'name' => $device->device_name,
                    'phone_number' => $device->phone_number,
                    'session_id' => $device->session_id
                ];
            });
            
            foreach ($devices as $device) {
                $chats = $this->whatsapp->getChats($device->session_id);
                
                if (isset($chats['success']) && $chats['success'] && isset($chats['chats'])) {
                    foreach ($chats['chats'] as $chat) {
                        $chat['deviceId'] = $device->id;
                        $chat['deviceNumber'] = $device->phone_number ?? $device->device_name;
                        $chat['deviceName'] = $device->device_name;
                        $allChats[] = $chat;
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'chats' => $allChats,
                'devices' => $formattedDevices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'خطأ في الاتصال بخدمة واتساب: ' . $e->getMessage(),
                'chats' => [],
                'devices' => []
            ]);
        }
    }

    public function getMessages(Request $request, $chatId)
    {
        $deviceId = $request->get('device_id');
        
        if (!$deviceId) {
            return response()->json([
                'success' => false,
                'error' => 'Device ID is required'
            ]);
        }
        
        $device = WhatsappDevice::find($deviceId);
        
        if (!$device) {
            return response()->json([
                'success' => false,
                'error' => 'Device not found'
            ]);
        }
        
        $messages = $this->whatsapp->getMessages($chatId, $device->session_id);
        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string',
            'device_id' => 'required|string'
        ]);

        $device = WhatsappDevice::find($request->device_id);
        
        if (!$device) {
            return response()->json([
                'success' => false,
                'error' => 'Device not found'
            ]);
        }
        
        $result = $this->whatsapp->sendMessage(
            $request->to, 
            $request->message, 
            $device->session_id
        );
        
        if ($result['success'] ?? false) {
            auth()->user()->updateActivity();
        }
        
        return response()->json($result);
    }

    public function logout()
    {
        $result = $this->whatsapp->logout();
        return response()->json($result);
    }
}
