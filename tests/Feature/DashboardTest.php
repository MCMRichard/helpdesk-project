<?php

namespace Tests\Feature;

use App\Models\Problem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_metrics_are_accurate()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role' => 'operator']);
        Problem::factory()->create(['status' => 'open']);
        Problem::factory()->create(['status' => 'assigned']);
        Problem::factory()->create(['status' => 'resolved', 'reported_time' => now()->subHour(), 'resolved_time' => now()]);

        $this->actingAs($user);
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSeeText('Open Problems');
        $response->assertSeeText('1'); // 1 open
        $response->assertSeeText('Assigned Problems');
        $response->assertSeeText('1'); // 1 assigned
        $response->assertSeeText('Resolved Problems');
        $response->assertSeeText('1'); // 1 resolved
        $response->assertSeeText('Average Resolution Time');
        $response->assertSeeText('60.00 minutes'); // 1 hour
    }
}