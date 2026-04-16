<?php

namespace App\Http\Controllers\QuanTri;

use App\Http\Controllers\BoDieuKhien;
use App\Models\DanhMuc;
use App\Models\KhachHang;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\PhieuNhap;
use App\Models\NhaCungCap;
use Illuminate\View\View;

class BangDieuKhienController extends BoDieuKhien
{
    public function index(): View
    {
        $totalCategories = DanhMuc::count();
        $totalSanPhams = SanPham::count();
        $totalKhachHangs = KhachHang::count();
        $totalDonHangs = DonHang::count();
        $totalNhaCungCaps = NhaCungCap::count();
        $totalPhieuNhaps = PhieuNhap::count();

        $recentDonHangs = DonHang::with('customer')->latest('order_date')->take(5)->get();
        $topSanPhams = SanPham::orderByDesc('stock')->take(4)->get();
        $latestKhachHangs = KhachHang::latest()->take(3)->get();
        $latestNhaCungCaps = NhaCungCap::latest()->take(3)->get();

        return view('quan_tri.bang_dieu_khien', compact(
            'totalCategories',
            'totalSanPhams',
            'totalKhachHangs',
            'totalDonHangs',
            'totalNhaCungCaps',
            'totalPhieuNhaps',
            'recentDonHangs',
            'topSanPhams',
            'latestKhachHangs',
            'latestNhaCungCaps'
        ));
    }
}
