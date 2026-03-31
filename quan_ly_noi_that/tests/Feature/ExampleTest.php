<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_redirects_root_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_admin_is_redirected_to_admin_dashboard_from_root(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get('/');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_customer_is_redirected_to_customer_dashboard_from_root(): void
    {
        $customer = User::factory()->customer()->create();

        $response = $this->actingAs($customer)->get('/');

        $response->assertRedirect(route('customer.dashboard'));
    }
}
