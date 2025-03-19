<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    protected $primaryKey = 'problem_number';
    protected $fillable = [
        'caller_id', 'operator_id', 'specialist_id', 'problem_type_id',
        'equipment_serial', 'software_id', 'status', 'reported_time',
        'resolved_time', 'notes'
    ];

    protected $casts = [
        'reported_time' => 'datetime',
        'resolved_time' => 'datetime',
    ];

    // Relationship: Caller who reported the problem
    public function caller()
    {
        return $this->belongsTo(Caller::class, 'caller_id');
    }

    // Relationship: Operator who logged the problem
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    // Relationship: Specialist assigned to the problem
    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }

    // Relationship: Problem type
    public function problemType()
    {
        return $this->belongsTo(ProblemType::class, 'problem_type_id');
    }

    // Relationship: Equipment involved
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_serial', 'serial_number');
    }

    // Relationship: Software involved
    public function software()
    {
        return $this->belongsTo(Software::class, 'software_id');
    }

    // Dynamic attribute: Time taken to resolve (in minutes)
    public function getResolutionTimeAttribute()
    {
        if ($this->status === 'resolved' && $this->resolved_time) {
            return $this->reported_time->diffInMinutes($this->resolved_time);
        }
        return null;
    }
}