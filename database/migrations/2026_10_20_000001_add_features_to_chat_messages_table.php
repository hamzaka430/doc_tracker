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
            $table->text('reactions')->nullable()->after('is_read');
            $table->unsignedBigInteger('reply_to_id')->nullable()->after('reactions');
            $table->text('reply_to_text')->nullable()->after('reply_to_id');
            $table->string('reply_to_sender', 50)->nullable()->after('reply_to_text');
            $table->boolean('is_edited')->default(false)->after('reply_to_sender');
            $table->boolean('is_deleted')->default(false)->after('is_edited');
            $table->boolean('is_pinned')->default(false)->after('is_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql_chat')->table('chat_messages', function (Blueprint $table) {
            $table->dropColumn([
                'reactions',
                'reply_to_id',
                'reply_to_text',
                'reply_to_sender',
                'is_edited',
                'is_deleted',
                'is_pinned',
            ]);
        });
    }
};
