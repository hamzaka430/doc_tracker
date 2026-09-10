<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ChatSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Check if a 4-digit PIN is configured.
     */
    public static function isPinConfigured(): bool
    {
        $setting = static::where('key', 'chat_pin')->first();
        return $setting && !empty($setting->value);
    }

    /**
     * Set the 4-digit PIN (hashed).
     */
    public static function setPin(string $pin): void
    {
        static::updateOrCreate(
            ['key' => 'chat_pin'],
            ['value' => Hash::make($pin)]
        );
    }

    /**
     * Verify provided PIN against the stored hash.
     */
    public static function verifyPin(string $pin): bool
    {
        $setting = static::where('key', 'chat_pin')->first();
        if (!$setting || empty($setting->value)) {
            return false;
        }

        return Hash::check($pin, $setting->value);
    }

    /**
     * Get the two participant names (default: User 1 and User 2).
     */
    public static function getParticipantNames(): array
    {
        $s1 = static::where('key', 'chat_name_1')->first();
        $s2 = static::where('key', 'chat_name_2')->first();

        return [
            'name1' => $s1 && !empty($s1->value) ? $s1->value : 'User 1',
            'name2' => $s2 && !empty($s2->value) ? $s2->value : 'User 2',
        ];
    }

    /**
     * Update participant names.
     */
    public static function setParticipantNames(string $name1, string $name2): void
    {
        static::updateOrCreate(['key' => 'chat_name_1'], ['value' => trim($name1)]);
        static::updateOrCreate(['key' => 'chat_name_2'], ['value' => trim($name2)]);
    }
}
