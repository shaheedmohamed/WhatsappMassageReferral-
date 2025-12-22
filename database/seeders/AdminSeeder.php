<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Shaheed Admin',
            'email' => 'shaheed@admin.com',
            'password' => Hash::make('Shaheed/1234'),
            'email_verified_at' => now(),
        ]);
    }
}
