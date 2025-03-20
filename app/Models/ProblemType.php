<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemType extends Model
{
    use HasFactory;

    protected $primaryKey = 'problem_type_id';
    protected $fillable = ['name', 'parent_type_id'];

    // Relationship: Parent problem type (self-referential)
    public function parent()
    {
        return $this->belongsTo(ProblemType::class, 'parent_type_id');
    }

    // Relationship: Child problem types (self-referential)
    public function children()
    {
        return $this->hasMany(ProblemType::class, 'parent_type_id');
    }

    // Relationship: Problems of this type
    public function problems()
    {
        return $this->hasMany(Problem::class, 'problem_type_id');
    }

    // Relationship: Specialists with expertise in this type
    public function specialists()
    {
        return $this->belongsToMany(User::class, 'specialist_expertise', 'problem_type_id', 'specialist_id');
    }
}