<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SapError extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'sap_tcode',
        'description',
        'image_path',
    ];

    /**
     * Belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
