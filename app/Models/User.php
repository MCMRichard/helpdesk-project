<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Helper methods to check roles
    public function isOperator()
    {
        return $this->role === 'operator';
    }

    public function isSpecialist()
    {
        return $this->role === 'specialist';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Relationship: Problems logged by this user as an operator
    public function loggedProblems()
    {
        return $this->hasMany(Problem::class, 'operator_id');
    }

    // Relationship: Problems assigned to this user as a specialist
    public function assignedProblems()
    {
        return $this->hasMany(Problem::class, 'specialist_id');
    }

    // Relationship: Problem types this specialist is expert in
    public function expertise()
    {
        return $this->belongsToMany(ProblemType::class, 'specialist_expertise', 'specialist_id', 'problem_type_id');
    }

    // Dynamic attribute: Current workload (count of assigned, unresolved problems)
    public function getWorkloadAttribute()
    {
        return $this->assignedProblems()->whereIn('status', ['open', 'assigned'])->count();
    }

    public function activeAssignments()
    {
        return $this->hasMany(Problem::class, 'specialist_id')
                    ->whereIn('status', ['open', 'assigned']);
    }

    public function assignmentHistory()
    {
        return $this->hasMany(ProblemAssignmentHistory::class, 'specialist_id');
    }
}