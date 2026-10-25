<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mysql_chat';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql_chat')->table('chat_messages', function (Blueprint $table) {
            $table->index('updated_at', 'idx_chat_updated_at');
            $table->index(['is_pinned', 'is_deleted'], 'idx_chat_pinned');
            $table->index(['sender_name', 'is_read'], 'idx_chat_sender_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_chat')->table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('idx_chat_updated_at');
            $table->dropIndex('idx_chat_pinned');
            $table->dropIndex('idx_chat_sender_read');
        });
    }
};
