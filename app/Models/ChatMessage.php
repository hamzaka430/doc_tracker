<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ChatMessage extends Model
{
    use HasFactory;

    /**
     * Use the dedicated Aiven MySQL connection for all chat data.
     * This keeps chat queries off JawsDB (which has a 3600 query/hour limit).
     */
    protected $connection = 'mysql_chat';

    protected $fillable = [
        'sender_name',
        'message',
        'attachment_path',
        'is_read',
        'reactions',
        'reply_to_id',
        'reply_to_text',
        'reply_to_sender',
        'is_edited',
        'is_deleted',
        'is_pinned',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'is_pinned' => 'boolean',
        'reactions' => 'array',
    ];

    protected $appends = [
        'formatted_time',
        'date_label',
    ];

    /**
     * Get user-friendly time string (e.g. 5:45 PM).
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('g:i A') : '';
    }

    /**
     * Get user-friendly date group (e.g. Today, Yesterday, or Sep 10, 2026).
     */
    public function getDateLabelAttribute(): string
    {
        if (!$this->created_at) {
            return '';
        }

        if ($this->created_at->isToday()) {
            return 'Today';
        }

        if ($this->created_at->isYesterday()) {
            return 'Yesterday';
        }

        return $this->created_at->format('l, M j, Y');
    }
}
