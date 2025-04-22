<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemAssignmentHistory extends Model
{
    use HasFactory;

    protected $table = 'problem_assignment_history';

    protected $fillable = [
        'problem_id',
        'specialist_id',
        'assigned_at',
        'unassigned_at',
        'reason',
    ];

    public function problem()
    {
        return $this->belongsTo(Problem::class, 'problem_id', 'problem_number');
    }

    public function specialist()
    {
        return $this->belongsTo(User::class, 'specialist_id');
    }
}