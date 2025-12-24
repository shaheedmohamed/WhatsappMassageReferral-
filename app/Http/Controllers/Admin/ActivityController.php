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

        // Get unique chats with their latest message and assignment
        $chatsQuery = DB::table('whatsapp_messages')
            ->select(
                'from_number',
                'device_id',
                DB::raw('MAX(id) as latest_message_id'),
                DB::raw('MAX(message_timestamp) as last_message_time'),
                DB::raw('COUNT(*) as message_count')
            )
            ->groupBy('from_number', 'device_id');

        if ($deviceId) {
            $chatsQuery->where('device_id', $deviceId);
        }

        // Get all chats first (without pagination for filtering)
        $allChats = $chatsQuery->orderBy('last_message_time', 'desc')->get();

        // Get full message details for each chat
        $messagesCollection = $allChats->map(function($chat) {
            $message = WhatsappMessage::with(['device', 'assignedUser'])
                ->find($chat->latest_message_id);
            
            if ($message) {
                $message->message_count = $chat->message_count;
                $message->last_message_time = $chat->last_message_time;
                
                // Get assignment for this chat
                $assignment = ChatAssignment::where('chat_number', $chat->from_number)
                    ->where('device_id', $chat->device_id)
                    ->whereIn('status', ['in_progress', 'on_hold'])
                    ->with('user')
                    ->first();
                
                if ($assignment) {
                    $message->assignment = $assignment;
                    $message->assigned_employee = $assignment->user;
                }
            }
            
            return $message;
        })->filter();

        // Apply filters
        if ($filter === 'pending') {
            $messagesCollection = $messagesCollection->filter(fn($m) => !$m->replied);
        } elseif ($filter === 'replied') {
            $messagesCollection = $messagesCollection->filter(fn($m) => $m->replied);
        }

        if ($userId) {
            $messagesCollection = $messagesCollection->filter(fn($m) => $m->assigned_user_id == $userId);
        }

        // Manual pagination
        $perPage = 50;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedItems = $messagesCollection->slice($offset, $perPage)->values();
        
        $messages = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $messagesCollection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $devices = WhatsappDevice::where('status', 'connected')->get();
        $users = User::where('role', 'employee')->where('is_active', true)->get();

        // Enhanced statistics
        $stats = [
            'total_chats' => WhatsappMessage::distinct('from_number')->count('from_number'),
            'pending_chats' => WhatsappMessage::where('replied', false)->distinct('from_number')->count('from_number'),
            'replied_chats' => WhatsappMessage::where('replied', true)->distinct('from_number')->count('from_number'),
            'active_agents' => User::where('role', 'employee')->where('status', 'online')->count(),
            'total_agents' => User::where('role', 'employee')->where('is_active', true)->count(),
            'in_progress_chats' => ChatAssignment::where('status', 'in_progress')->count(),
            'on_hold_chats' => ChatAssignment::where('status', 'on_hold')->count(),
            'completed_today' => ChatAssignment::where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
        ];

        // Get all agents with their current workload
        $allAgents = User::where('role', 'employee')
            ->where('is_active', true)
            ->withCount([
                'chatAssignments as active_chats' => function($query) {
                    $query->where('status', 'in_progress');
                },
                'chatAssignments as hold_chats' => function($query) {
                    $query->where('status', 'on_hold');
                },
                'chatAssignments as completed_today' => function($query) {
                    $query->where('status', 'completed')
                          ->whereDate('completed_at', today());
                }
            ])
            ->get()
            ->map(function($agent) {
                $agent->is_available = $agent->status === 'online' && $agent->active_chats < 5;
                $agent->workload_status = $agent->active_chats == 0 ? 'free' : 
                    ($agent->active_chats < 3 ? 'light' : 
                    ($agent->active_chats < 5 ? 'moderate' : 'busy'));
                return $agent;
            });

        // Get active assignments with detailed info
        $activeAssignments = ChatAssignment::with(['user', 'device'])
            ->whereIn('status', ['in_progress', 'on_hold'])
            ->orderBy('assigned_at', 'desc')
            ->get()
            ->groupBy('user_id');

        // Get recent activity (last 10 actions)
        $recentActivity = ChatAssignment::with(['user', 'device'])
            ->whereNotNull('completed_at')
            ->orWhereNotNull('claimed_at')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.activity.index', compact(
            'messages', 
            'devices', 
            'users', 
            'stats', 
            'allAgents',
            'activeAssignments',
            'recentActivity',
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

    public function viewChat(Request $request)
    {
        $chatId = $request->get('chat_id');
        $deviceId = $request->get('device_id');
        $chatNumber = $request->get('chat_number');

        if (!$chatId || !$deviceId) {
            return back()->with('error', 'معلومات المحادثة غير مكتملة');
        }

        // Get chat assignment info
        $assignment = ChatAssignment::with(['user', 'device'])
            ->where('chat_id', $chatId)
            ->where('device_id', $deviceId)
            ->first();

        // Get all messages for this chat
        $messages = WhatsappMessage::where('device_id', $deviceId)
            ->where(function($query) use ($chatNumber) {
                $query->where('from_number', $chatNumber)
                      ->orWhere('to_number', $chatNumber);
            })
            ->orderBy('message_timestamp', 'asc')
            ->get();

        return view('admin.activity.chat', compact('assignment', 'messages', 'chatNumber'));
    }
}
