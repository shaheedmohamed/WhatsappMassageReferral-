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
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('whatsapp_messages', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('whatsapp_messages', 'transferred_from')) {
                $table->foreignId('transferred_from')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('whatsapp_messages', 'transfer_type')) {
                $table->enum('transfer_type', ['auto', 'manual'])->nullable();
            }
            if (!Schema::hasColumn('whatsapp_messages', 'group_type')) {
                $table->string('group_type')->nullable();
            }
            if (!Schema::hasColumn('whatsapp_messages', 'replied_at')) {
                $table->timestamp('replied_at')->nullable();
            }
            if (!Schema::hasColumn('whatsapp_messages', 'response_time_minutes')) {
                $table->integer('response_time_minutes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('whatsapp_messages', function (Blueprint $table) {
            //
        });
    }
};
