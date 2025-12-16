<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->string('from_number');
            $table->string('from_name')->nullable();
            $table->text('message_body');
            $table->string('message_type')->default('text');
            $table->timestamp('message_timestamp');
            $table->boolean('forwarded_to_admin')->default(false);
            $table->timestamp('forwarded_at')->nullable();
            $table->boolean('replied')->default(false);
            $table->text('reply_message')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
            
            $table->index('from_number');
            $table->index('message_timestamp');
            $table->index('forwarded_to_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
