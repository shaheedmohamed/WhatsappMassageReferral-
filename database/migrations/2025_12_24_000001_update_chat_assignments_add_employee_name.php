<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chat_assignments', function (Blueprint $table) {
            $table->string('employee_name')->nullable()->after('user_id');
            $table->timestamp('claimed_at')->nullable()->after('assigned_at');
            $table->timestamp('released_at')->nullable()->after('completed_at');
        });
        
        DB::statement("ALTER TABLE chat_assignments MODIFY COLUMN status ENUM('pending', 'in_progress', 'on_hold', 'completed', 'transferred') DEFAULT 'pending'");
    }

    public function down()
    {
        Schema::table('chat_assignments', function (Blueprint $table) {
            $table->dropColumn(['employee_name', 'claimed_at', 'released_at']);
        });
        
        DB::statement("ALTER TABLE chat_assignments MODIFY COLUMN status ENUM('active', 'completed', 'transferred') DEFAULT 'active'");
    }
};
