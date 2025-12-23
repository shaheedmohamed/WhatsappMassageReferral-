<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, expand enum to include all old and new values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'agent', 'super_admin', 'employee') DEFAULT 'employee'");
        
        // Update existing 'agent' roles to 'employee'
        DB::table('users')->where('role', 'agent')->update(['role' => 'employee']);
        
        // Now remove 'agent' from enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'super_admin', 'employee') DEFAULT 'employee'");
        
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('super_admin_id')->nullable()->after('role')->constrained('users')->onDelete('set null');
            $table->foreignId('community_id')->nullable()->after('super_admin_id')->constrained('communities')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['super_admin_id']);
            $table->dropForeign(['community_id']);
            $table->dropColumn(['super_admin_id', 'community_id']);
        });
        
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'agent') DEFAULT 'agent'");
    }
};
