<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caller extends Model
{
    use HasFactory;

    protected $primaryKey = 'caller_id'; // Match the table's PK
    protected $fillable = ['name', 'job_title', 'department']; // Mass assignable fields

    // Relationship: A caller can have many problems
    public function problems()
    {
        return $this->hasMany(Problem::class, 'caller_id');
    }
}