<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('super_admin_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('super_admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('device_id')->constrained('whatsapp_devices')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['super_admin_id', 'device_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('super_admin_devices');
    }
};
