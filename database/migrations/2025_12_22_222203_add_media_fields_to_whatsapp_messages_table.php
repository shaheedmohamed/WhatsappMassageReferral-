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
            if (!Schema::hasColumn('whatsapp_messages', 'message_type')) {
                $table->string('message_type')->default('text')->after('body');
            }
            if (!Schema::hasColumn('whatsapp_messages', 'media_url')) {
                $table->text('media_url')->nullable()->after('message_type');
            }
            if (!Schema::hasColumn('whatsapp_messages', 'media_mime_type')) {
                $table->string('media_mime_type')->nullable()->after('media_url');
            }
            if (!Schema::hasColumn('whatsapp_messages', 'media_filename')) {
                $table->string('media_filename')->nullable()->after('media_mime_type');
            }
            if (!Schema::hasColumn('whatsapp_messages', 'media_size')) {
                $table->integer('media_size')->nullable()->after('media_filename');
            }
            if (!Schema::hasColumn('whatsapp_messages', 'caption')) {
                $table->text('caption')->nullable()->after('media_size');
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
