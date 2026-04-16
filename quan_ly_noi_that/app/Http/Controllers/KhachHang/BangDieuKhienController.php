<?php

namespace App\Http\Controllers\KhachHang;

use App\Http\Controllers\BoDieuKhien;
use App\Models\DonHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BangDieuKhienController extends BoDieuKhien
{
    public function index(Request $request): View
    {
        $customer = $request->user()->getOrCreateKhachHangProfile();

        $ordersQuery = DonHang::where('customer_id', $customer->id);

        $totalDonHangs = (clone $ordersQuery)->count();
        $pendingDonHangs = (clone $ordersQuery)->whereIn('status', [DonHang::STATUS_NEW, DonHang::STATUS_PROCESSING])->count();
        $completedDonHangs = (clone $ordersQuery)->where('status', DonHang::STATUS_COMPLETED)->count();
        $recentDonHangs = (clone $ordersQuery)->latest('order_date')->take(5)->get();
        $featuredSanPhams = SanPham::where('is_active', true)->orderByDesc('created_at')->take(6)->get();

        return view('khach_hang.bang_dieu_khien', compact(
            'customer',
            'totalDonHangs',
            'pendingDonHangs',
            'completedDonHangs',
            'recentDonHangs',
            'featuredSanPhams'
        ));
    }
}
