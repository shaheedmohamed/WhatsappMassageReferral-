<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WhatsappDevice;
use App\Models\WhatsappMessage;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        
        $totalDevices = $user->whatsappDevices()->count();
        $connectedDevices = $user->whatsappDevices()->where('status', 'connected')->count();
        
        $deviceIds = $user->whatsappDevices()->pluck('id');
        $todayMessages = WhatsappMessage::whereIn('device_id', $deviceIds)
            ->whereDate('message_timestamp', today())
            ->count();
        
        $unrepliedMessages = WhatsappMessage::whereIn('device_id', $deviceIds)
            ->where('replied', false)
            ->count();
        
        $devices = $user->whatsappDevices()->where('status', 'connected')->latest()->take(5)->get();
        $recentMessages = WhatsappMessage::whereIn('device_id', $deviceIds)
            ->latest('message_timestamp')
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalDevices',
            'connectedDevices',
            'todayMessages',
            'unrepliedMessages',
            'devices',
            'recentMessages'
        ));
    }
}
