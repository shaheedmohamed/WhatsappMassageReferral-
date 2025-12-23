<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('community_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained('communities')->onDelete('cascade');
            $table->foreignId('device_id')->constrained('whatsapp_devices')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['community_id', 'device_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_devices');
    }
};
