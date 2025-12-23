<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Community;
use App\Models\WhatsappDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $superAdmin = auth()->user();
        
        $stats = [
            'communities' => $superAdmin->ownedCommunities()->count(),
            'employees' => $superAdmin->employees()->count(),
            'devices' => $superAdmin->assignedDevices()->count(),
            'active_employees' => $superAdmin->employees()->where('is_active', true)->count(),
        ];
        
        $recentCommunities = $superAdmin->ownedCommunities()
            ->with(['employees', 'devices'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('super-admin.dashboard', compact('stats', 'recentCommunities'));
    }
    
    public function communities()
    {
        $communities = auth()->user()->ownedCommunities()
            ->with(['employees', 'devices'])
            ->paginate(15);
        
        return view('super-admin.communities.index', compact('communities'));
    }
    
    public function createCommunity()
    {
        $availableDevices = auth()->user()->assignedDevices;
        return view('super-admin.communities.create', compact('availableDevices'));
    }
    
    public function storeCommunity(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'devices' => 'nullable|array',
            'devices.*' => 'exists:whatsapp_devices,id',
        ]);
        
        $community = auth()->user()->ownedCommunities()->create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);
        
        if ($request->has('devices')) {
            $community->devices()->sync($request->devices);
        }
        
        return redirect()->route('super-admin.communities.index')
            ->with('success', 'تم إنشاء المجتمع بنجاح');
    }
    
    public function editCommunity(Community $community)
    {
        $this->authorize('update', $community);
        
        $availableDevices = auth()->user()->assignedDevices;
        $assignedDevices = $community->devices->pluck('id')->toArray();
        
        return view('super-admin.communities.edit', compact('community', 'availableDevices', 'assignedDevices'));
    }
    
    public function updateCommunity(Request $request, Community $community)
    {
        $this->authorize('update', $community);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'devices' => 'nullable|array',
            'devices.*' => 'exists:whatsapp_devices,id',
            'is_active' => 'boolean',
        ]);
        
        $community->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);
        
        if ($request->has('devices')) {
            $community->devices()->sync($request->devices);
        }
        
        return redirect()->route('super-admin.communities.index')
            ->with('success', 'تم تحديث المجتمع بنجاح');
    }
    
    public function employees()
    {
        $employees = auth()->user()->employees()
            ->with('community')
            ->paginate(15);
        
        $communities = auth()->user()->ownedCommunities;
        
        return view('super-admin.employees.index', compact('employees', 'communities'));
    }
    
    public function createEmployee()
    {
        $communities = auth()->user()->ownedCommunities;
        return view('super-admin.employees.create', compact('communities'));
    }
    
    public function storeEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'community_id' => 'required|exists:communities,id',
        ]);
        
        // Verify community belongs to this super admin
        $community = auth()->user()->ownedCommunities()->findOrFail($request->community_id);
        
        $employee = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employee',
            'super_admin_id' => auth()->id(),
            'community_id' => $request->community_id,
            'is_active' => true,
        ]);
        
        return redirect()->route('super-admin.employees.index')
            ->with('success', 'تم إضافة الموظف بنجاح');
    }
    
    public function showEmployee(User $employee)
    {
        $this->authorize('view', $employee);
        
        $employee->load(['community', 'assignedMessages', 'workLogs']);
        
        return view('super-admin.employees.show', compact('employee'));
    }
    
    public function editEmployee(User $employee)
    {
        $this->authorize('update', $employee);
        
        $communities = auth()->user()->ownedCommunities;
        
        return view('super-admin.employees.edit', compact('employee', 'communities'));
    }
    
    public function updateEmployee(Request $request, User $employee)
    {
        $this->authorize('update', $employee);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:8',
            'community_id' => 'required|exists:communities,id',
            'is_active' => 'boolean',
        ]);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'community_id' => $request->community_id,
            'is_active' => $request->boolean('is_active', true),
        ];
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $employee->update($data);
        
        return redirect()->route('super-admin.employees.index')
            ->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }
}
