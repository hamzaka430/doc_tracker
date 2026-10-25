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
        $conn = Schema::connection('mysql_chat');
        $indexes = [
            'idx_chat_updated_at' => function (Blueprint $table) {
                $table->index('updated_at', 'idx_chat_updated_at');
            },
            'idx_chat_pinned' => function (Blueprint $table) {
                $table->index(['is_pinned', 'is_deleted'], 'idx_chat_pinned');
            },
            'idx_chat_sender_read' => function (Blueprint $table) {
                $table->index(['sender_name', 'is_read'], 'idx_chat_sender_read');
            },
        ];

        foreach ($indexes as $indexName => $callback) {
            try {
                $conn->table('chat_messages', $callback);
            } catch (\Throwable $e) {
                if (!str_contains($e->getMessage(), 'Duplicate key name') && !str_contains($e->getMessage(), '1061')) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $conn = Schema::connection('mysql_chat');
        $indexNames = ['idx_chat_updated_at', 'idx_chat_pinned', 'idx_chat_sender_read'];

        foreach ($indexNames as $indexName) {
            try {
                $conn->table('chat_messages', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            } catch (\Throwable $e) {
                // Ignore if index doesn't exist
            }
        }
    }
};
