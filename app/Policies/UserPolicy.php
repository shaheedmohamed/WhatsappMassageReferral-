<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function view(User $user, User $model)
    {
        // Admin can view anyone
        if ($user->isAdmin()) {
            return true;
        }
        
        // Super admin can view their employees
        if ($user->isSuperAdmin() && $model->super_admin_id === $user->id) {
            return true;
        }
        
        // Users can view themselves
        return $user->id === $model->id;
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function update(User $user, User $model)
    {
        // Admin can update anyone
        if ($user->isAdmin()) {
            return true;
        }
        
        // Super admin can update their employees
        if ($user->isSuperAdmin() && $model->super_admin_id === $user->id) {
            return true;
        }
        
        // Users can update themselves
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        // Admin can delete anyone except themselves
        if ($user->isAdmin() && $user->id !== $model->id) {
            return true;
        }
        
        // Super admin can delete their employees
        if ($user->isSuperAdmin() && $model->super_admin_id === $user->id) {
            return true;
        }
        
        return false;
    }
}
