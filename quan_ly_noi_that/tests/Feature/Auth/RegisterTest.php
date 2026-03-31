<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_register_page(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_guest_can_register_new_account(): void
    {
        $response = $this->post(route('register.submit'), [
            'name' => 'Nguyễn Văn A',
            'email' => 'dangky@example.com',
            'password' => 'StrongPass@123',
            'password_confirmation' => 'StrongPass@123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Đăng ký tài khoản thành công. Bạn có thể đăng nhập ngay.');
        $user = User::where('email', 'dangky@example.com')->first();

        $this->assertNotNull($user);
        $this->assertDatabaseHas('users', [
            'email' => 'dangky@example.com',
            'role' => User::ROLE_CUSTOMER,
        ]);
        $this->assertDatabaseHas('customers', [
            'user_id' => $user?->id,
            'email' => 'dangky@example.com',
            'name' => 'Nguyễn Văn A',
        ]);
    }

    public function test_register_requires_unique_email_and_password_confirmation(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->from(route('register'))->post(route('register.submit'), [
            'name' => 'Nguyễn Văn B',
            'email' => 'existing@example.com',
            'password' => 'StrongPass@123',
            'password_confirmation' => 'WrongPass@123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors(['email', 'password']);
    }
}
