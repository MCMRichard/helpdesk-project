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
        /** @var \App\Models\User $operator */
        $operator = User::factory()->create(['role' => 'operator']);
        $caller = Caller::factory()->create();
        $problemType = ProblemType::factory()->create();
        $equipment = Equipment::factory()->create();

        $this->actingAs($operator);

        $response = $this->post(route('problems.store'), [
            'caller_id' => $caller->caller_id,
            'problem_type_id' => $problemType->problem_type_id,
            'equipment_serial' => $equipment->serial_number,
            'notes' => 'Test problem description',
        ]);

        $response->assertRedirect(route('problems.index'));
        $this->assertDatabaseHas('problems', [
            'operator_id' => $operator->id,
            'status' => 'open',
            'caller_id' => $caller->caller_id,
            'problem_type_id' => $problemType->problem_type_id,
            'equipment_serial' => $equipment->serial_number,
            'notes' => 'Test problem description',
        ]);
    }

    public function test_specialist_can_resolve_problem()
    {
        /** @var \App\Models\User $specialist */
        $specialist = User::factory()->create(['role' => 'specialist']);
        $problem = Problem::factory()->create([
            'specialist_id' => $specialist->id,
            'status' => 'assigned',
        ]);

        $this->actingAs($specialist);

        $response = $this->post(route('problems.resolve', $problem->problem_number), [
            'resolution_notes' => 'Issue fixed by rebooting system',
        ]);

        $response->assertRedirect(route('problems.index'));
        $this->assertDatabaseHas('problems', [
            'problem_number' => $problem->problem_number,
            'status' => 'resolved',
            'specialist_id' => null,
        ]);
        $this->assertStringContainsString('Resolution: Issue fixed by rebooting system', Problem::find($problem->problem_number)->notes);
    }
}