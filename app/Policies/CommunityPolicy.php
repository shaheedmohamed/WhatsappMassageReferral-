<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Community;

class CommunityPolicy
{
    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }

    public function view(User $user, Community $community)
    {
        return $user->isAdmin() || $community->super_admin_id === $user->id;
    }

    public function create(User $user)
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Community $community)
    {
        return $user->isAdmin() || $community->super_admin_id === $user->id;
    }

    public function delete(User $user, Community $community)
    {
        return $user->isAdmin() || $community->super_admin_id === $user->id;
    }
}
