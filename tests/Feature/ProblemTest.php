<?php

namespace Tests\Feature;

use App\Models\Caller;
use App\Models\Equipment;
use App\Models\Problem;
use App\Models\ProblemType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProblemTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_log_problem()
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $operator */
        $operator = User::factory()->create(['role' => 'operator']);
        $caller = Caller::factory()->create();
        $problemType = ProblemType::factory()->create();
        $equipment = Equipment::factory()->create(['serial_number' => 'EQ123']);

        $this->actingAs($operator);

        $response = $this->post(route('problems.store'), [
            'caller_id' => $caller->caller_id,
            'problem_type_id' => $problemType->problem_type_id,
            'equipment_serial' => $equipment->serial_number,
            'notes' => 'Test problem',
        ]);

        $response->assertRedirect(route('problems.index'));
        $this->assertDatabaseHas('problems', [
            'operator_id' => $operator->id,
            'status' => 'open',
        ]);
    }

    public function test_specialist_can_resolve_problem()
    {
        /** @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable $specialist */
        $specialist = User::factory()->create(['role' => 'specialist']);
        $problem = Problem::factory()->create([
            'specialist_id' => $specialist->id,
            'status' => 'assigned',
        ]);

        $this->actingAs($specialist);

        $response = $this->post(route('problems.resolve', $problem->problem_number), [
            'resolution_notes' => 'Fixed the issue',
        ]);

        $response->assertRedirect(route('problems.index'));
        $this->assertDatabaseHas('problems', [
            'problem_number' => $problem->problem_number,
            'status' => 'resolved',
        ]);
    }

    public function test_equipment_status_is_set()
    {
        $equipment = Equipment::factory()->create(['status' => 'under_repair']);
        $this->assertEquals('under_repair', $equipment->status);
    }

    public function test_caller_factory_works()
    {
        $caller = Caller::factory()->create();
        $this->assertInstanceOf(Caller::class, $caller);
    }
}