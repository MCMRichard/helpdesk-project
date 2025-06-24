<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_authenticated_users_can_access_dashboard()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));

        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role' => 'operator']);
        $this->actingAs($user);
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_only_admin_can_access_user_management()
    {
        /** @var \App\Models\User $operator */
        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator);
        $response = $this->get(route('admin.users'));
        $response->assertStatus(403);

        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get(route('admin.users'));
        $response->assertStatus(200);
    }
}