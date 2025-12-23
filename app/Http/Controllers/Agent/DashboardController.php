<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WhatsappMessage;
use App\Models\WhatsappDevice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get assigned devices for this agent
        $assignedDeviceIds = $user->assigned_devices ? json_decode($user->assigned_devices, true) : [];
        
        if (empty($assignedDeviceIds)) {
            return view('agent.dashboard', [
                'totalMessages' => 0,
                'todayMessages' => 0,
                'assignedChats' => 0,
                'pendingMessages' => 0,
                'devices' => [],
                'recentMessages' => [],
                'topChats' => [],
                'responseTime' => 0,
                'hasDevices' => false
            ]);
        }
        
        // Get devices assigned to this agent
        $devices = WhatsappDevice::whereIn('id', $assignedDeviceIds)
            ->where('status', 'connected')
            ->get();
        
        // Total messages from assigned devices
        $totalMessages = WhatsappMessage::whereIn('device_id', $assignedDeviceIds)->count();
        
        // Today's messages
        $todayMessages = WhatsappMessage::whereIn('device_id', $assignedDeviceIds)
            ->whereDate('created_at', today())
            ->count();
        
        // Assigned chats (unique chat IDs)
        $assignedChats = WhatsappMessage::whereIn('device_id', $assignedDeviceIds)
            ->distinct('chat_id')
            ->count('chat_id');
        
        // Pending messages (unread or not responded)
        $pendingMessages = WhatsappMessage::whereIn('device_id', $assignedDeviceIds)
            ->where('is_from_me', false)
            ->whereNull('read_at')
            ->count();
        
        // Recent messages (last 10)
        $recentMessages = WhatsappMessage::whereIn('device_id', $assignedDeviceIds)
            ->with('device')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Top chats by message count
        $topChats = WhatsappMessage::whereIn('device_id', $assignedDeviceIds)
            ->select('chat_id', 'chat_name', DB::raw('COUNT(*) as message_count'))
            ->groupBy('chat_id', 'chat_name')
            ->orderBy('message_count', 'desc')
            ->limit(5)
            ->get();
        
        // Average response time (in minutes)
        $responseTime = $this->calculateAverageResponseTime($assignedDeviceIds);
        
        return view('agent.dashboard', [
            'totalMessages' => $totalMessages,
            'todayMessages' => $todayMessages,
            'assignedChats' => $assignedChats,
            'pendingMessages' => $pendingMessages,
            'devices' => $devices,
            'recentMessages' => $recentMessages,
            'topChats' => $topChats,
            'responseTime' => $responseTime,
            'hasDevices' => true
        ]);
    }
    
    private function calculateAverageResponseTime($deviceIds)
    {
        // Get pairs of incoming and outgoing messages
        $conversations = WhatsappMessage::whereIn('device_id', $deviceIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('chat_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('chat_id');
        
        $totalResponseTime = 0;
        $responseCount = 0;
        
        foreach ($conversations as $chatMessages) {
            $lastIncoming = null;
            
            foreach ($chatMessages as $message) {
                if (!$message->is_from_me) {
                    $lastIncoming = $message;
                } elseif ($lastIncoming) {
                    $responseTime = $message->created_at->diffInMinutes($lastIncoming->created_at);
                    $totalResponseTime += $responseTime;
                    $responseCount++;
                    $lastIncoming = null;
                }
            }
        }
        
        return $responseCount > 0 ? round($totalResponseTime / $responseCount, 1) : 0;
    }
}
