<?php

namespace Tests\Feature\XacThuc;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\CaKiemThu;

class DatLaiMatKhauTest extends CaKiemThu
{
    use RefreshDatabase;

    public function test_guest_can_view_forgot_password_page(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertOk();
    }

    public function test_user_can_reset_password_with_valid_email(): void
    {
        $user = User::factory()->create([
            'email' => 'recover@example.com',
            'password' => Hash::make('OldPass@123'),
        ]);

        $requestResetResponse = $this->post(route('password.email'), [
            'email' => 'recover@example.com',
        ]);

        $requestResetResponse->assertRedirect();
        $requestResetResponse->assertSessionHas('status', 'Xác thực email thành công. Vui lòng đặt mật khẩu mới.');

        $location = $requestResetResponse->headers->get('Location');
        $this->assertNotNull($location);

        $path = (string) parse_url($location, PHP_URL_PATH);
        $query = [];

        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

        $token = basename($path);

        $this->assertNotSame('', $token);
        $this->assertSame('recover@example.com', $query['email'] ?? null);

        $resetResponse = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'recover@example.com',
            'password' => 'NewPass@123',
            'password_confirmation' => 'NewPass@123',
        ]);

        $resetResponse->assertRedirect(route('login'));
        $resetResponse->assertSessionHas('status', 'Khôi phục mật khẩu thành công. Bạn có thể đăng nhập ngay.');
        $this->assertTrue(Hash::check('NewPass@123', $user->fresh()->password));
    }

    public function test_forgot_password_rejects_unknown_email(): void
    {
        $response = $this
            ->from(route('password.request'))
            ->post(route('password.email'), [
                'email' => 'unknown@example.com',
            ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHasErrors('email');
    }

    public function test_password_reset_fails_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'invalid-token@example.com',
            'password' => Hash::make('OldPass@123'),
        ]);

        $response = $this
            ->from(route('password.reset', [
                'token' => 'invalid-token',
                'email' => 'invalid-token@example.com',
            ]))
            ->post(route('password.update'), [
                'token' => 'invalid-token',
                'email' => 'invalid-token@example.com',
                'password' => 'NewPass@123',
                'password_confirmation' => 'NewPass@123',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('OldPass@123', $user->fresh()->password));
    }
}
