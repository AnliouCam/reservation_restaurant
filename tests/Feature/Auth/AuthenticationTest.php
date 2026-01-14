<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        // Note: les users sans role admin sont rediriges vers /reservations
        $user = User::factory()->reception()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('reservations.index'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    // ===========================================
    // Tests des roles (admin/reception)
    // ===========================================

    public function test_admin_is_redirected_to_dashboard_after_login(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_reception_is_redirected_to_reservations_after_login(): void
    {
        $reception = User::factory()->reception()->create();

        $response = $this->post('/login', [
            'email' => $reception->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('reservations.index'));
    }

    public function test_reception_cannot_access_dashboard(): void
    {
        $reception = User::factory()->reception()->create();

        $response = $this->actingAs($reception)->get('/dashboard');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
