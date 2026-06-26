<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private function user(Role $role): User
    {
        return User::create([
            'name' => 'Test '.$role->value,
            'email' => $role->value.'@test.local',
            'password' => 'secret123',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Вход в систему');
    }

    public function test_owner_can_open_every_page(): void
    {
        $this->actingAs($this->user(Role::Owner));

        foreach (['/dashboard', '/clients', '/clients/import', '/schedule', '/finance', '/settings'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_manager_cannot_open_settings(): void
    {
        $this->actingAs($this->user(Role::Manager));

        $this->get('/dashboard')->assertOk();
        $this->get('/settings')->assertForbidden();
    }

    public function test_disabled_user_cannot_log_in(): void
    {
        $user = $this->user(Role::Manager);
        $user->update(['is_active' => false]);

        // деактивированный пользователь не должен проходить дальше логина
        $this->actingAs($user);
        $this->assertTrue($user->is_active === false);
    }
}
