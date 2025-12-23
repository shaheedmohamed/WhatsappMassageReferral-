<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->integer('work_hours')->default(0);
            $table->integer('messages_replied')->default(0);
            $table->integer('messages_auto_transferred')->default(0);
            $table->integer('messages_manual_transferred')->default(0);
            $table->decimal('avg_response_time', 8, 2)->default(0);
            $table->json('messages_by_group')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_work_logs');
    }
};
