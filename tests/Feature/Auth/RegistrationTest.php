<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_is_disabled(): void
    {
        // L'inscription publique est desactivee pour des raisons de securite
        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_registration_post_is_disabled(): void
    {
        // L'inscription publique est desactivee pour des raisons de securite
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(404);
    }
}
