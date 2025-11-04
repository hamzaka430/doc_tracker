<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
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

    protected $casts = [
        'line_clearance' => 'boolean',
        'review' => 'boolean',
        'confirmation' => 'boolean',
        'submission_date' => 'date',
        'submission_time' => 'datetime:H:i:s'
    ];

    public static function getStages()
    {
        return [
            'On Process',
            'QA Sign',
            'Production',
            'Return',
            'Transfer Note',
            'Hold',
            'Specific Person',
            'Completed'
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
