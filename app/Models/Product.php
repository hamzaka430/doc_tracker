<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'batch_no',
        'stage',
        'status',
        'pre_line_clearance',
        'in_process',
        'post_line_clearance',
        'remarks',
        'submission_date',
        'submission_time'
    ];

    protected $casts = [
        'pre_line_clearance' => 'boolean',
        'in_process' => 'boolean',
        'post_line_clearance' => 'boolean',
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

    public function isReadyForSubmission()
    {
        return $this->pre_line_clearance && 
               $this->in_process && 
               $this->post_line_clearance;
    }

    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }
}
