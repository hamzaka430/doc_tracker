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
    ];

    protected $casts = [
        'is_read' => 'boolean',
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

        return $this->created_at->format('M j, Y');
    }
}
