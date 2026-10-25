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
        $conn->table('chat_messages', function (Blueprint $table) use ($conn) {
            if (!$conn->hasColumn('chat_messages', 'reactions')) {
                $table->text('reactions')->nullable()->after('is_read');
            }
            if (!$conn->hasColumn('chat_messages', 'reply_to_id')) {
                $table->unsignedBigInteger('reply_to_id')->nullable()->after('reactions');
            }
            if (!$conn->hasColumn('chat_messages', 'reply_to_text')) {
                $table->text('reply_to_text')->nullable()->after('reply_to_id');
            }
            if (!$conn->hasColumn('chat_messages', 'reply_to_sender')) {
                $table->string('reply_to_sender', 50)->nullable()->after('reply_to_text');
            }
            if (!$conn->hasColumn('chat_messages', 'is_edited')) {
                $table->boolean('is_edited')->default(false)->after('reply_to_sender');
            }
            if (!$conn->hasColumn('chat_messages', 'is_deleted')) {
                $table->boolean('is_deleted')->default(false)->after('is_edited');
            }
            if (!$conn->hasColumn('chat_messages', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('is_deleted');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $conn = Schema::connection('mysql_chat');
        $columnsToDrop = array_filter([
            'reactions',
            'reply_to_id',
            'reply_to_text',
            'reply_to_sender',
            'is_edited',
            'is_deleted',
            'is_pinned',
        ], fn ($col) => $conn->hasColumn('chat_messages', $col));

        if (!empty($columnsToDrop)) {
            $conn->table('chat_messages', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }
};
