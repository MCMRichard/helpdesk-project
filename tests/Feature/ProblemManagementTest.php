<?php

namespace Tests\Feature;

use App\Models\Caller;
use App\Models\Equipment;
use App\Models\Problem;
use App\Models\ProblemType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProblemManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_specialist_cannot_assign_problems()
    {
        /** @var \App\Models\User $specialist */
        $specialist = User::factory()->create(['role' => 'specialist']);
        $problem = Problem::factory()->create(['status' => 'open']);
        $this->actingAs($specialist);

        $response = $this->post(route('problems.assign', $problem->problem_number));
        $response->assertForbidden(); // Specialists shouldn't assign
    }

    public function test_operator_can_assign_specialist()
    {
        /** @var \App\Models\User $operator */
        $operator = User::factory()->create(['role' => 'operator']);
        /** @var \App\Models\User $specialist */
        $specialist = User::factory()->create(['role' => 'specialist']);
        $problemType = ProblemType::factory()->create();
        $specialist->expertise()->attach($problemType->problem_type_id);
        $problem = Problem::factory()->create([
            'problem_type_id' => $problemType->problem_type_id,
            'status' => 'open',
        ]);

        $this->actingAs($operator);
        $response = $this->post(route('problems.assign', $problem->problem_number));
        $response->assertRedirect();
        $this->assertDatabaseHas('problems', [
            'problem_number' => $problem->problem_number,
            'specialist_id' => $specialist->id,
            'status' => 'assigned',
        ]);
    }

    public function test_cannot_assign_specialist_at_max_workload()
    {
        /** @var \App\Models\User $operator */
        $operator = User::factory()->create(['role' => 'operator']);
        /** @var \App\Models\User $specialist */
        $specialist = User::factory()->create(['role' => 'specialist']);
        $problemType = ProblemType::factory()->create();
        $specialist->expertise()->attach($problemType->problem_type_id);
        Problem::factory()->count(10)->create([
            'specialist_id' => $specialist->id,
            'status' => 'assigned',
            'problem_type_id' => $problemType->problem_type_id,
        ]);
        $problem = Problem::factory()->create([
            'problem_type_id' => $problemType->problem_type_id,
            'status' => 'open',
        ]);

        $this->actingAs($operator);
        $response = $this->post(route('problems.assign', $problem->problem_number));
        $response->assertRedirect();
        $response->assertSessionHas('error', 'All specialists are at maximum workload');
        $this->assertDatabaseHas('problems', [
            'problem_number' => $problem->problem_number,
            'specialist_id' => null,
            'status' => 'open',
        ]);
    }

    public function test_admin_can_mark_problem_unsolvable()
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $problem = Problem::factory()->create(['status' => 'open']);
        $this->actingAs($admin);

        $response = $this->post(route('problems.unsolvable', $problem->problem_number), [
            'unsolvable_reason' => 'Hardware obsolete',
        ]);

        $response->assertRedirect(route('problems.index'));
        $this->assertDatabaseHas('problems', [
            'problem_number' => $problem->problem_number,
            'status' => 'unsolvable',
            'unsolvable_reason' => 'Hardware obsolete',
        ]);
    }

    public function test_operator_can_edit_problem()
    {
        /** @var \App\Models\User $operator */
        $operator = User::factory()->create(['role' => 'operator']);
        $problem = Problem::factory()->create(['operator_id' => $operator->id]);
        $newCaller = Caller::factory()->create();
        $this->actingAs($operator);

        $response = $this->put(route('problems.update', $problem->problem_number), [
            'caller_id' => $newCaller->caller_id,
            'problem_type_id' => $problem->problem_type_id,
            'equipment_serial' => $problem->equipment_serial,
            'notes' => 'Updated notes',
        ]);

        $response->assertRedirect(route('problems.index'));
        $this->assertDatabaseHas('problems', [
            'problem_number' => $problem->problem_number,
            'caller_id' => $newCaller->caller_id,
            'notes' => "Updated notes\nEdited by {$operator->name} on " . now()->format('Y-m-d H:i'),
        ]);
    }

    public function test_caller_must_exist_in_register()
    {
        /** @var \App\Models\User $operator */
        $operator = User::factory()->create(['role' => 'operator']);
        $problemType = ProblemType::factory()->create();
        $this->actingAs($operator);

        $response = $this->post(route('problems.store'), [
            'caller_id' => 999, // Non-existent caller
            'problem_type_id' => $problemType->problem_type_id,
            'notes' => 'Invalid caller test',
        ]);

        $response->assertSessionHasErrors('caller_id');
        $this->assertDatabaseMissing('problems', ['notes' => 'Invalid caller test']);
    }

    public function test_problem_type_hierarchy_assigns_specialist()
    {
        /** @var \App\Models\User $operator */
        $operator = User::factory()->create(['role' => 'operator']);
        /** @var \App\Models\User $specialist */
        $specialist = User::factory()->create(['role' => 'specialist']);
        $parentType = ProblemType::factory()->create();
        $childType = ProblemType::factory()->create(['parent_type_id' => $parentType->problem_type_id]);
        $specialist->expertise()->attach($parentType->problem_type_id); // Expert in parent type
        $problem = Problem::factory()->create([
            'problem_type_id' => $childType->problem_type_id,
            'status' => 'open',
        ]);

        $this->actingAs($operator);
        $response = $this->post(route('problems.assign', $problem->problem_number));
        $response->assertRedirect();
        $this->assertDatabaseHas('problems', [
            'problem_number' => $problem->problem_number,
            'specialist_id' => $specialist->id,
            'status' => 'assigned',
        ]);
    }

    public function test_equipment_must_exist_in_register()
    {
        /** @var \App\Models\User $operator */
        $operator = User::factory()->create(['role' => 'operator']);
        $caller = Caller::factory()->create();
        $problemType = ProblemType::factory()->create();
        $this->actingAs($operator);

        $response = $this->post(route('problems.store'), [
            'caller_id' => $caller->caller_id,
            'problem_type_id' => $problemType->problem_type_id,
            'equipment_serial' => 'INVALID123', // Non-existent serial
            'notes' => 'Invalid equipment test',
        ]);

        $response->assertSessionHasErrors('equipment_serial');
        $this->assertDatabaseMissing('problems', ['notes' => 'Invalid equipment test']);
    }
}