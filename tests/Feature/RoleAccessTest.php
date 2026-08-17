<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get('/dashboard/admin');

        $response->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->customer()->create();

        $response = $this->actingAs($customer)->get('/dashboard/admin');

        $response->assertForbidden(); // 403
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/dashboard/admin');

        $response->assertOk();
    }

    public function test_company_admin_and_owner_both_reach_company_dashboard(): void
    {
        $companyOwner = User::factory()->companyOwner()->create();
        $companyAdmin = User::factory()->companyAdmin()->create();

        $this->actingAs($companyOwner)->get('/dashboard/company')->assertOk();
        $this->actingAs($companyAdmin)->get('/dashboard/company')->assertOk();
    }

    public function test_expert_cannot_access_customer_dashboard(): void
    {
        $expert = User::factory()->expert()->create();

        $response = $this->actingAs($expert)->get('/dashboard/customer');

        $response->assertForbidden();
    }
}
