<?php

namespace App\Http\Controllers\XacThuc;

use App\Http\Controllers\BoDieuKhien;
use App\Models\KhachHang;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DangKyController extends BoDieuKhien
{
    public function showRegisterForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'customer.dashboard');
        }

        return view('xac_thuc.dang_ky');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        DB::transaction(function () use ($data): void {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => User::ROLE_CUSTOMER,
            ]);

            KhachHang::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'email' => $data['email'],
            ]);
        });

        return redirect()
            ->route('login')
            ->with('status', 'Đăng ký tài khoản thành công. Bạn có thể đăng nhập ngay.');
    }
}
