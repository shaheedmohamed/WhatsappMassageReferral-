<?php

namespace App\Http\Controllers;

use App\Models\ChatAssignment;
use App\Models\WhatsappDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatAssignmentController extends Controller
{
    public function checkAssignment(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string',
            'device_id' => 'required|exists:whatsapp_devices,id',
        ]);

        $assignment = ChatAssignment::where('chat_id', $request->chat_id)
            ->where('device_id', $request->device_id)
            ->whereIn('status', ['in_progress', 'on_hold'])
            ->first();

        if ($assignment) {
            return response()->json([
                'success' => true,
                'assigned' => true,
                'assignment' => [
                    'employee_name' => $assignment->employee_name,
                    'status' => $assignment->status,
                    'status_text' => $assignment->status_text,
                    'is_current_user' => $assignment->user_id == Auth::id(),
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'assigned' => false,
        ]);
    }

    public function claimChat(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string',
            'device_id' => 'required|exists:whatsapp_devices,id',
            'chat_number' => 'required|string',
        ]);

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $existingAssignment = ChatAssignment::where('chat_id', $request->chat_id)
                ->where('device_id', $request->device_id)
                ->whereIn('status', ['in_progress', 'on_hold'])
                ->first();

            if ($existingAssignment && $existingAssignment->user_id != $user->id) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'هذه المحادثة قيد التعامل من قبل ' . $existingAssignment->employee_name,
                ], 409);
            }

            if ($existingAssignment && $existingAssignment->user_id == $user->id) {
                $existingAssignment->update([
                    'status' => 'in_progress',
                    'claimed_at' => now(),
                ]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'تم استئناف المحادثة بنجاح',
                    'assignment' => $existingAssignment,
                ]);
            }

            $assignment = ChatAssignment::create([
                'user_id' => $user->id,
                'device_id' => $request->device_id,
                'chat_id' => $request->chat_id,
                'chat_number' => $request->chat_number,
                'employee_name' => $user->name,
                'assigned_at' => now(),
                'claimed_at' => now(),
                'status' => 'in_progress',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تولي المحادثة بنجاح',
                'assignment' => $assignment,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تولي المحادثة',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string',
            'device_id' => 'required|exists:whatsapp_devices,id',
            'status' => 'required|in:completed,on_hold',
        ]);

        $user = Auth::user();

        $assignment = ChatAssignment::where('chat_id', $request->chat_id)
            ->where('device_id', $request->device_id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['in_progress', 'on_hold'])
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على المحادثة المخصصة لك',
            ], 404);
        }

        $updateData = ['status' => $request->status];

        if ($request->status === 'completed') {
            $updateData['completed_at'] = now();
        } elseif ($request->status === 'on_hold') {
            $updateData['released_at'] = now();
        }

        $assignment->update($updateData);

        $statusMessage = $request->status === 'completed' 
            ? 'تم إنهاء المحادثة بنجاح' 
            : 'تم وضع المحادثة في الانتظار';

        return response()->json([
            'success' => true,
            'message' => $statusMessage,
            'assignment' => $assignment,
        ]);
    }

    public function releaseChat(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|string',
            'device_id' => 'required|exists:whatsapp_devices,id',
        ]);

        $user = Auth::user();

        $assignment = ChatAssignment::where('chat_id', $request->chat_id)
            ->where('device_id', $request->device_id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['in_progress', 'on_hold'])
            ->first();

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على المحادثة',
            ], 404);
        }

        $assignment->update([
            'status' => 'on_hold',
            'released_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم وضع المحادثة في الانتظار',
            'assignment' => $assignment,
        ]);
    }

    public function getAssignments()
    {
        $user = Auth::user();
        
        $communityDevices = $user->community 
            ? $user->community->devices->pluck('id')->toArray()
            : [];

        $assignments = ChatAssignment::with(['user', 'device'])
            ->whereIn('device_id', $communityDevices)
            ->whereIn('status', ['in_progress', 'on_hold'])
            ->get()
            ->map(function ($assignment) {
                return [
                    'chat_id' => $assignment->chat_id,
                    'device_id' => $assignment->device_id,
                    'employee_name' => $assignment->employee_name,
                    'status' => $assignment->status,
                    'status_text' => $assignment->status_text,
                    'is_current_user' => $assignment->user_id == Auth::id(),
                ];
            });

        return response()->json([
            'success' => true,
            'assignments' => $assignments,
        ]);
    }
}
