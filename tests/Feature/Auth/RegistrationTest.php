<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'username'        => 'ali_test',
            'contact_number'  => '09121234567',
            'role'            => 1,
            'first_name'      => 'علی',
            'password'        => 'Password123',
        ]);

        $response->assertRedirect(route('dashboard.customer'));

        $this->assertDatabaseHas('users', [
            'username' => 'ali_test',
            'role'     => 1,
        ]);

        $this->assertAuthenticated();
    }

    public function test_registration_fails_with_duplicate_username(): void
    {
        User::factory()->customer()->create(['username' => 'ali_test']);

        $response = $this->post('/register', [
            'username'        => 'ali_test',
            'contact_number'  => '09121234568',
            'role'            => 1,
            'first_name'      => 'علی',
            'password'        => 'Password123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_cannot_register_as_admin_or_company(): void
    {
        $response = $this->post('/register', [
            'username'        => 'sneaky_admin',
            'contact_number'  => '09121111111',
            'role'            => 0,
            'first_name'      => 'تست',
            'password'        => 'Password123',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['username' => 'sneaky_admin']);
    }
}
