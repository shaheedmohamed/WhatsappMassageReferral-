<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained('whatsapp_devices')->onDelete('cascade');
            $table->string('chat_id');
            $table->string('chat_number');
            $table->timestamp('assigned_at');
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['active', 'completed', 'transferred'])->default('active');
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['chat_id', 'device_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_assignments');
    }
};
