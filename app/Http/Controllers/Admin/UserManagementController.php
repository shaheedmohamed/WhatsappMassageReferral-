<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with(['chatAssignments' => function($query) {
            $query->where('status', 'active');
        }])
        ->withCount(['assignedMessages', 'chatAssignments'])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin,employee',
            'permissions' => 'nullable|array',
            'is_active' => 'boolean',
            'assigned_devices' => 'nullable|array',
            'assigned_devices.*' => 'exists:whatsapp_devices,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');
        
        $user = User::create($validated);
        
        // Assign devices to super admin
        if ($user->isSuperAdmin() && $request->has('assigned_devices')) {
            $user->assignedDevices()->sync($request->assigned_devices);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,super_admin,employee',
            'permissions' => 'nullable|array',
            'assigned_devices' => 'nullable|array',
            'assigned_devices.*' => 'exists:whatsapp_devices,id',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active');
        
        // Remove assigned_devices from validated array as we'll handle it separately
        $assignedDevices = $validated['assigned_devices'] ?? [];
        unset($validated['assigned_devices']);

        $user->update($validated);
        
        // Sync devices for super admin
        if ($user->isSuperAdmin()) {
            $user->assignedDevices()->sync($assignedDevices);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'تم تحديث حالة المستخدم');
    }
}
