<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_username(): void
    {
        $user = User::factory()->customer()->create([
            'username' => 'sara99',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'login'    => 'sara99',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard.customer'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_contact_number(): void
    {
        $user = User::factory()->expert()->create([
            'contact_number' => '09351234567',
            'password'       => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'login'    => '09351234567',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard.expert'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->customer()->create([
            'username' => 'sara99',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'login'    => 'sara99',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }
}
