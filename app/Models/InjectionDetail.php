<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InjectionDetail extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'product_name',
        'batch_no',
        'batch_size',
        'optical_qty',
        'pkg_qty',
        'rejection',
        'error',
    ];

    protected static function booted()
    {
        static::addGlobalScope('user', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('user_id', auth()->id());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
