<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsappMessage;
use App\Models\ChatAssignment;
use App\Models\WhatsappDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $deviceId = $request->get('device_id');
        $userId = $request->get('user_id');

        $query = WhatsappMessage::with(['device', 'assignedUser'])
            ->orderBy('message_timestamp', 'desc');

        if ($filter === 'pending') {
            $query->where('replied', false);
        } elseif ($filter === 'replied') {
            $query->where('replied', true);
        }

        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }

        if ($userId) {
            $query->where('assigned_user_id', $userId);
        }

        $messages = $query->paginate(50);

        $devices = WhatsappDevice::where('status', 'connected')->get();
        $users = User::where('role', 'agent')->where('is_active', true)->get();

        $stats = [
            'total_chats' => WhatsappMessage::distinct('from_number')->count('from_number'),
            'pending_chats' => WhatsappMessage::where('replied', false)->distinct('from_number')->count('from_number'),
            'replied_chats' => WhatsappMessage::where('replied', true)->distinct('from_number')->count('from_number'),
            'active_agents' => User::where('role', 'agent')->where('status', 'online')->count(),
            'total_agents' => User::where('role', 'agent')->where('is_active', true)->count(),
        ];

        $onlineUsers = User::where('role', 'agent')
            ->where('status', 'online')
            ->with(['chatAssignments' => function($query) {
                $query->where('status', 'active');
            }])
            ->get();

        $activeAssignments = ChatAssignment::with(['user', 'device'])
            ->where('status', 'active')
            ->get()
            ->groupBy('user_id');

        return view('admin.activity.index', compact(
            'messages', 
            'devices', 
            'users', 
            'stats', 
            'onlineUsers',
            'activeAssignments',
            'filter',
            'deviceId',
            'userId'
        ));
    }

    public function userActivity(User $user)
    {
        $assignments = ChatAssignment::with('device')
            ->where('user_id', $user->id)
            ->orderBy('assigned_at', 'desc')
            ->paginate(20);

        $messages = WhatsappMessage::with('device')
            ->where('assigned_user_id', $user->id)
            ->orderBy('message_timestamp', 'desc')
            ->paginate(20);

        $stats = [
            'total_assigned' => ChatAssignment::where('user_id', $user->id)->count(),
            'active_chats' => ChatAssignment::where('user_id', $user->id)->where('status', 'active')->count(),
            'completed_chats' => ChatAssignment::where('user_id', $user->id)->where('status', 'completed')->count(),
            'total_messages' => WhatsappMessage::where('assigned_user_id', $user->id)->count(),
            'replied_messages' => WhatsappMessage::where('assigned_user_id', $user->id)->where('replied', true)->count(),
        ];

        return view('admin.activity.user', compact('user', 'assignments', 'messages', 'stats'));
    }

    public function assignChat(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'device_id' => 'required|exists:whatsapp_devices,id',
            'chat_id' => 'required|string',
            'chat_number' => 'required|string',
        ]);

        $assignment = ChatAssignment::create([
            'user_id' => $validated['user_id'],
            'device_id' => $validated['device_id'],
            'chat_id' => $validated['chat_id'],
            'chat_number' => $validated['chat_number'],
            'assigned_at' => now(),
            'status' => 'active',
        ]);

        WhatsappMessage::where('device_id', $validated['device_id'])
            ->where('from_number', $validated['chat_number'])
            ->whereNull('assigned_user_id')
            ->update([
                'assigned_user_id' => $validated['user_id'],
                'assigned_at' => now(),
            ]);

        return back()->with('success', 'تم تعيين المحادثة بنجاح');
    }
}
