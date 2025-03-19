<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        DB::table('users')->updateOrCreate(
            ['id' => 1],
            ['name' => 'Admin', 'email' => 'admin@manzaneque.com', 'password' => Hash::make('password'), 'role' => 'admin', 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('users')->updateOrCreate(
            ['id' => 2],
            ['name' => 'Specialist1', 'email' => 'specialist1@manzaneque.com', 'password' => Hash::make('password'), 'role' => 'specialist', 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('users')->updateOrCreate(
            ['id' => 3],
            ['name' => 'Operator1', 'email' => 'operator1@manzaneque.com', 'password' => Hash::make('password'), 'role' => 'operator', 'created_at' => now(), 'updated_at' => now()]
        );

        // Callers
        DB::table('callers')->updateOrCreate(
            ['caller_id' => 1],
            ['name' => 'John Doe', 'job_title' => 'Broker', 'department' => 'Sales', 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('callers')->updateOrCreate(
            ['caller_id' => 2],
            ['name' => 'Jane Smith', 'job_title' => 'Manager', 'department' => 'IT', 'created_at' => now(), 'updated_at' => now()]
        );

        // Equipment
        DB::table('equipment')->updateOrCreate(
            ['serial_number' => 'EQ123'],
            ['type' => 'Laptop', 'make' => 'Dell', 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('equipment')->updateOrCreate(
            ['serial_number' => 'EQ456'],
            ['type' => 'Printer', 'make' => 'HP', 'created_at' => now(), 'updated_at' => now()]
        );

        // Software
        DB::table('software')->updateOrCreate(
            ['software_id' => 1],
            ['name' => 'Windows', 'version' => '10', 'license_status' => 'Active', 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('software')->updateOrCreate(
            ['software_id' => 2],
            ['name' => 'Office', 'version' => '365', 'license_status' => 'Active', 'created_at' => now(), 'updated_at' => now()]
        );

        // Problem Types
        DB::table('problem_types')->updateOrCreate(
            ['problem_type_id' => 1],
            ['name' => 'Hardware', 'parent_type_id' => null, 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('problem_types')->updateOrCreate(
            ['problem_type_id' => 2],
            ['name' => 'Software', 'parent_type_id' => null, 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('problem_types')->updateOrCreate(
            ['problem_type_id' => 3],
            ['name' => 'Printer Failure', 'parent_type_id' => 1, 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('problem_types')->updateOrCreate(
            ['problem_type_id' => 4],
            ['name' => 'App Crash', 'parent_type_id' => 2, 'created_at' => now(), 'updated_at' => now()]
        );

        // Specialist Expertise
        DB::table('specialist_expertise')->updateOrCreate(
            ['specialist_id' => 2, 'problem_type_id' => 1],
            ['created_at' => now(), 'updated_at' => now()]
        );
        DB::table('specialist_expertise')->updateOrCreate(
            ['specialist_id' => 2, 'problem_type_id' => 3],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Problems
        DB::table('problems')->updateOrCreate(
            ['caller_id' => 1, 'operator_id' => 3, 'problem_type_id' => 3, 'equipment_serial' => 'EQ456'],
            ['status' => 'open', 'reported_time' => now(), 'notes' => 'Printer not working', 'created_at' => now(), 'updated_at' => now()]
        );
    }
}