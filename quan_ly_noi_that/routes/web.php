<?php

use App\Http\Controllers\QuanTri\DanhMucController;
use App\Http\Controllers\QuanTri\KhachHangController;
use App\Http\Controllers\QuanTri\BangDieuKhienController;
use App\Http\Controllers\QuanTri\DonHangController;
use App\Http\Controllers\QuanTri\PhieuNhapController;
use App\Http\Controllers\QuanTri\SanPhamController;
use App\Http\Controllers\QuanTri\BaoCaoController;
use App\Http\Controllers\QuanTri\BienDongKhoController;
use App\Http\Controllers\QuanTri\NhaCungCapController;
use App\Http\Controllers\XacThuc\DangNhapController;
use App\Http\Controllers\XacThuc\DatLaiMatKhauController;
use App\Http\Controllers\XacThuc\DangKyController;
use App\Http\Controllers\KhachHang\BangDieuKhienController as KhachHangBangDieuKhienController;
use App\Http\Controllers\KhachHang\DonHangController as KhachHangDonHangController;
use App\Http\Controllers\KhachHang\SanPhamController as KhachHangSanPhamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('customer.dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [DangNhapController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DangNhapController::class, 'login'])->name('login.submit');
    Route::get('/register', [DangKyController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [DangKyController::class, 'register'])->name('register.submit');
    Route::get('/forgot-password', [DatLaiMatKhauController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [DatLaiMatKhauController::class, 'sendResetToken'])->name('password.email');
    Route::get('/reset-password/{token}', [DatLaiMatKhauController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [DatLaiMatKhauController::class, 'resetPassword'])->name('password.update');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [BangDieuKhienController::class, 'index'])->name('dashboard');

    Route::resource('categories', DanhMucController::class)->except(['show']);
    Route::resource('products', SanPhamController::class)->except(['show']);
    Route::resource('customers', KhachHangController::class)->except(['show']);
    Route::resource('suppliers', NhaCungCapController::class)->except(['show']);
    Route::resource('orders', DonHangController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::resource('purchase-receipts', PhieuNhapController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::resource('stock-movements', BienDongKhoController::class)->only(['index', 'create', 'store']);
    Route::get('reports', [BaoCaoController::class, 'index'])->name('reports.index');
});

Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/', [KhachHangBangDieuKhienController::class, 'index'])->name('dashboard');
    Route::get('/products', [KhachHangSanPhamController::class, 'index'])->name('products.index');
    Route::get('/orders', [KhachHangDonHangController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [KhachHangDonHangController::class, 'create'])->name('orders.create');
    Route::post('/orders', [KhachHangDonHangController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [KhachHangDonHangController::class, 'show'])->name('orders.show');
});

Route::middleware('auth')
    ->match(['get', 'post'], '/logout', [DangNhapController::class, 'logout'])
    ->name('logout');
