<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

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

    public function isEditable()
    {
        if ($this->status !== 'submitted') {
            return true;
        }

        if (!$this->submission_date) {
            return true;
        }

        $dateStr = is_string($this->submission_date) ? $this->submission_date : $this->submission_date->format('Y-m-d');
        $timeStr = $this->submission_time ?? '00:00:00';
        $timeStr = is_string($timeStr) ? $timeStr : $timeStr->format('H:i:s');

        try {
            $submissionDateTime = \Carbon\Carbon::parse($dateStr . ' ' . $timeStr);
            return now()->lessThanOrEqualTo($submissionDateTime->addHours(6));
        } catch (\Exception $e) {
            return false;
        }
    }
}
