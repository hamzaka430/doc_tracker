<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'batch_no',
        'stage',
        'type',
        'status',
        'line_clearance',
        'review',
        'confirmation',
        'remarks',
        'submission_date',
        'submission_time'
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

    protected $casts = [
        'line_clearance' => 'boolean',
        'review' => 'boolean',
        'confirmation' => 'boolean',
        'submission_date' => 'date'
    ];

    public static function getStages()
    {
        return [
            'In Process',
            'QA Sign',
            'Prd Sign',
            'Return',
            'Transfer Note',
            'Hold',
            'Completed',
            'Corrections',
            'Hamza - On Review',
            'Rahat - On Review'
        ];
    }

    public static function getTypes()
    {
        return [
            'Injection',
            'Suspension',
            'Tablet',
            'Capsule'
        ];
    }

    public function isReadyForSubmission()
    {
        return $this->line_clearance &&
               $this->review &&
               $this->confirmation;
    }

    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }
}
