<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WhatsappDevice;
use App\Services\WhatsAppService;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->middleware('auth');
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        $devices = auth()->user()->whatsappDevices()->latest()->get();
        return view('admin.devices.index', compact('devices'));
    }

    public function create()
    {
        return view('admin.devices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_name' => 'required|string|max:255',
        ]);

        $device = auth()->user()->whatsappDevices()->create([
            'device_name' => $request->device_name,
            'session_id' => 'session_' . Str::random(16),
            'status' => 'disconnected',
        ]);

        return redirect()->route('admin.devices.show', $device->id)
            ->with('success', 'تم إنشاء الجهاز بنجاح');
    }

    public function show($id)
    {
        $device = auth()->user()->whatsappDevices()->findOrFail($id);
        return view('admin.devices.show', compact('device'));
    }

    public function getQr($id)
    {
        $device = auth()->user()->whatsappDevices()->findOrFail($id);
        
        $response = $this->whatsappService->getQrCode($device->session_id);
        
        if ($response['success'] && isset($response['qr'])) {
            $device->update([
                'qr_code' => $response['qr'],
                'status' => 'connecting',
            ]);
        }
        
        return response()->json($response);
    }

    public function getStatus($id)
    {
        $device = auth()->user()->whatsappDevices()->findOrFail($id);
        
        $response = $this->whatsappService->getStatus($device->session_id);
        
        if ($response['success'] && $response['status'] === 'ready') {
            $device->update([
                'status' => 'connected',
                'phone_number' => $response['phone'] ?? null,
                'last_connected_at' => now(),
            ]);
        }
        
        return response()->json($response);
    }

    public function disconnect($id)
    {
        $device = auth()->user()->whatsappDevices()->findOrFail($id);
        
        $response = $this->whatsappService->logout($device->session_id);
        
        $device->update([
            'status' => 'disconnected',
            'qr_code' => null,
        ]);
        
        return redirect()->route('admin.devices.index')
            ->with('success', 'تم قطع الاتصال بنجاح');
    }

    public function destroy($id)
    {
        $device = auth()->user()->whatsappDevices()->findOrFail($id);
        
        if ($device->status === 'connected') {
            $this->whatsappService->logout($device->session_id);
        }
        
        $device->delete();
        
        return redirect()->route('admin.devices.index')
            ->with('success', 'تم حذف الجهاز بنجاح');
    }
}
