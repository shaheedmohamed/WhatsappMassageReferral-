<?php

namespace App\Http\Controllers;

use App\Models\WhatsappMessage;
use App\Models\WhatsappDevice;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $employee = auth()->user();
        
        // Get devices assigned to employee's community
        $communityDevices = $employee->community 
            ? $employee->community->devices->pluck('id')->toArray()
            : [];
        
        $stats = [
            'total_messages' => WhatsappMessage::whereIn('device_id', $communityDevices)->count(),
            'assigned_messages' => WhatsappMessage::whereIn('device_id', $communityDevices)->count(),
            'pending_messages' => WhatsappMessage::whereIn('device_id', $communityDevices)->where('replied', false)->count(),
            'completed_messages' => WhatsappMessage::whereIn('device_id', $communityDevices)->where('replied', true)->count(),
        ];
        
        $recentMessages = WhatsappMessage::whereIn('device_id', $communityDevices)
            ->with(['device'])
            ->latest('message_timestamp')
            ->take(10)
            ->get();
        
        return view('employee.dashboard', compact('stats', 'recentMessages'));
    }
    
    public function chats()
    {
        $employee = auth()->user();
        
        // Get devices assigned to employee's community
        $devices = $employee->community 
            ? $employee->community->devices()->where('status', 'connected')->get()
            : collect();
        
        return view('employee.chats', compact('devices'));
    }
}
