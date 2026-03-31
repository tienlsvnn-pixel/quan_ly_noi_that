<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_visiting_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->customer()->create();

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@homestore.vn',
            'password' => Hash::make('123456'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'admin@homestore.vn',
            'password' => '123456',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_login_is_redirected_to_customer_dashboard(): void
    {
        $customer = User::factory()->customer()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('12345678'),
        ]);

        $response = $this->post(route('login.submit'), [
            'email' => 'customer@example.com',
            'password' => '12345678',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticatedAs($customer);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@homestore.vn',
            'password' => Hash::make('123456'),
        ]);

        $response = $this->from(route('login'))->post(route('login.submit'), [
            'email' => 'admin@homestore.vn',
            'password' => '000000',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_many_failed_attempts(): void
    {
        User::factory()->create([
            'email' => 'admin@homestore.vn',
            'password' => Hash::make('123456'),
        ]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from(route('login'))->post(route('login.submit'), [
                'email' => 'admin@homestore.vn',
                'password' => '000000',
            ])->assertRedirect(route('login'));
        }

        $response = $this->from(route('login'))->post(route('login.submit'), [
            'email' => 'admin@homestore.vn',
            'password' => '000000',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout_via_get_route(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('logout'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        $this->assertGuest();
    }
}