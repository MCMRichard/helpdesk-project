<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $primaryKey = 'serial_number'; // String PK
    public $incrementing = false; // Not auto-incrementing
    protected $keyType = 'string'; // Key is a string
    protected $fillable = ['serial_number', 'type', 'make'];

    // Relationship: Equipment can be linked to many problems
    public function problems()
    {
        return $this->hasMany(Problem::class, 'equipment_serial', 'serial_number');
    }
}