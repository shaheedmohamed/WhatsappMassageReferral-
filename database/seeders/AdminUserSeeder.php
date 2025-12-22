<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin/1234'),
                'role' => 'admin',
                'is_active' => true,
                'status' => 'offline',
            ]
        );

        User::updateOrCreate(
            ['email' => 'agent@agent.com'],
            [
                'name' => 'Agent Demo',
                'password' => Hash::make('password'),
                'role' => 'agent',
                'permissions' => ['reply_to_chats', 'view_all_chats', 'send_messages'],
                'is_active' => true,
                'status' => 'offline',
            ]
        );
    }
}
