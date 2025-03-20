<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    use HasFactory;

    protected $primaryKey = 'software_id';
    protected $fillable = ['name', 'version', 'license_status'];

    // Relationship: Software can be linked to many problems
    public function problems()
    {
        return $this->hasMany(Problem::class, 'software_id');
    }
}