<?php

namespace Tests\Unit;

use App\Models\Problem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_checks()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        /** @var \App\Models\User $operator */
        $operator = User::factory()->create(['role' => 'operator']);
        /** @var \App\Models\User $specialist */
        $specialist = User::factory()->create(['role' => 'specialist']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isOperator());
        $this->assertFalse($admin->isSpecialist());

        $this->assertTrue($operator->isOperator());
        $this->assertFalse($operator->isAdmin());
        $this->assertFalse($operator->isSpecialist());

        $this->assertTrue($specialist->isSpecialist());
        $this->assertFalse($specialist->isAdmin());
        $this->assertFalse($specialist->isOperator());
    }

    public function test_problem_resolution_time()
    {
        $problem = Problem::factory()->create([
            'status' => 'resolved',
            'reported_time' => now()->subHours(2),
            'resolved_time' => now(),
        ]);

        $this->assertEquals(120, $problem->resolution_time); // 2 hours = 120 minutes
    }
}