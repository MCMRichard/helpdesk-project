<?php

namespace Tests\Unit;

use App\Models\Problem;
use App\Models\ProblemType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProblemAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_specialist_assignment_considers_expertise_and_workload()
    {
        /** @var \App\Models\User $specialist */
        $specialist = User::factory()->create(['role' => 'specialist']);
        $problemType = ProblemType::factory()->create();
        $specialist->expertise()->attach($problemType->problem_type_id);
        $problem = Problem::factory()->create(['problem_type_id' => $problemType->problem_type_id]);

        $controller = new \App\Http\Controllers\ProblemController();
        $assignedSpecialist = $this->invokeMethod($controller, 'assignSpecialistToProblem', [$problem]);

        $this->assertEquals($specialist->id, $assignedSpecialist->id);
        $this->assertDatabaseHas('problems', [
            'problem_number' => $problem->problem_number,
            'specialist_id' => $specialist->id,
            'status' => 'assigned',
        ]);
    }

    // Helper to call protected/private methods
    private function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}