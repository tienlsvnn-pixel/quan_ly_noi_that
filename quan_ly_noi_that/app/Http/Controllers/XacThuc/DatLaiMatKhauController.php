<?php

namespace App\Http\Controllers\XacThuc;

use App\Http\Controllers\BoDieuKhien;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DatLaiMatKhauController extends BoDieuKhien
{
    public function showForgotPasswordForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'customer.dashboard');
        }

        return view('xac_thuc.quen_mat_khau');
    }

    public function sendResetToken(Request $request): RedirectResponse
    {
        $credentials = $request->validate(
            [
                'email' => ['required', 'email'],
            ],
            [
                'email.required' => 'Vui lòng nhập địa chỉ email.',
                'email.email' => 'Địa chỉ email không đúng định dạng.',
            ]
        );

        $user = User::query()->where('email', $credentials['email'])->first();

        if (!$user) {
            return back()
                ->withErrors([
                    'email' => 'Email này không tồn tại trong hệ thống.',
                ])
                ->withInput();
        }

        $token = Password::broker()->createToken($user);

        return redirect()
            ->route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ])
            ->with('status', 'Xác thực email thành công. Vui lòng đặt mật khẩu mới.');
    }

    public function showResetPasswordForm(Request $request, string $token): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'customer.dashboard');
        }

        $email = (string) $request->query('email');

        if ($email === '') {
            return redirect()
                ->route('password.request')
                ->withErrors([
                    'email' => 'Không tìm thấy thông tin tài khoản để khôi phục.',
                ]);
        }

        return view('xac_thuc.dat_lai_mat_khau', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate(
            [
                'token' => ['required', 'string'],
                'email' => ['required', 'email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'email.required' => 'Vui lòng nhập địa chỉ email.',
                'email.email' => 'Địa chỉ email không đúng định dạng.',
                'password.required' => 'Vui lòng nhập mật khẩu mới.',
                'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ]
        );

        $status = Password::broker()->reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => (string) $request->input('password_confirmation'),
                'token' => $data['token'],
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withErrors([
                    'email' => 'Liên kết khôi phục không hợp lệ hoặc đã hết hạn.',
                ])
                ->withInput($request->only('email'));
        }

        return redirect()
            ->route('login')
            ->with('status', 'Khôi phục mật khẩu thành công. Bạn có thể đăng nhập ngay.');
    }
}
