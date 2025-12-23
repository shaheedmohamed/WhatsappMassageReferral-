<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminManagementController extends Controller
{
    public function index()
    {
        $superAdmins = User::where('role', 'super_admin')
            ->with(['ownedCommunities', 'employees', 'assignedDevices'])
            ->get();
        
        $totalCommunities = $superAdmins->sum(function($sa) {
            return $sa->ownedCommunities->count();
        });
        
        $totalEmployees = $superAdmins->sum(function($sa) {
            return $sa->employees->count();
        });
        
        $activeSuperAdmins = $superAdmins->where('is_active', true)->count();
        
        return view('admin.super-admins.index', compact(
            'superAdmins',
            'totalCommunities',
            'totalEmployees',
            'activeSuperAdmins'
        ));
    }
    
    public function show(User $superAdmin)
    {
        if (!$superAdmin->isSuperAdmin()) {
            abort(404);
        }
        
        $superAdmin->load([
            'ownedCommunities.employees',
            'ownedCommunities.devices',
            'employees.community',
            'assignedDevices'
        ]);
        
        return view('admin.super-admins.show', compact('superAdmin'));
    }
}
