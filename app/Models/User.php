<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * The two master admin emails whose data is always shared.
     */
    const MASTER_ADMIN_EMAILS = ['admin@rahat.me', 'admin@hamzaka.me'];

    /**
     * Check if this user is a Master Admin.
     */
    public function isMasterAdmin(): bool
    {
        return in_array($this->email, self::MASTER_ADMIN_EMAILS);
    }

    /**
     * Get IDs of all master admin accounts.
     */
    public static function getMasterAdminIds(): array
    {
        return static::whereIn('email', self::MASTER_ADMIN_EMAILS)->pluck('id')->toArray();
    }

    /**
     * Get the user IDs whose data this user can SEE.
     *
     * Master admins → all master admin IDs (shared org data)
     * Everyone else → only their own ID
     */
    public function getVisibleUserIds(): array
    {
        if ($this->isMasterAdmin()) {
            return self::getMasterAdminIds();
        }

        return [$this->id];
    }

    /**
     * Check if this user can MODIFY a row owned by $modelUserId.
     *
     * Returns true if $modelUserId is within this user's visible scope.
     */
    public function canModifyData(int $modelUserId): bool
    {
        return in_array($modelUserId, $this->getVisibleUserIds());
    }
}
