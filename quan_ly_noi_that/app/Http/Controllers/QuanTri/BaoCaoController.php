<?php

namespace App\Http\Controllers\QuanTri;

use App\Http\Controllers\BoDieuKhien;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\BienDongKho;
use Illuminate\View\View;

class BaoCaoController extends BoDieuKhien
{
    public function index(): View
    {
        $from = request('from');
        $to = request('to');

        $ordersQuery = DonHang::query()
            ->when($from, fn ($query) => $query->whereDate('order_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('order_date', '<=', $to));

        $revenue = (clone $ordersQuery)->sum('total_amount');
        $completedRevenue = (clone $ordersQuery)->where('status', DonHang::STATUS_COMPLETED)->sum('total_amount');
        $processingDonHangs = (clone $ordersQuery)->where('status', DonHang::STATUS_PROCESSING)->count();
        $newDonHangs = (clone $ordersQuery)->where('status', DonHang::STATUS_NEW)->count();

        $monthlyRevenue = (clone $ordersQuery)
            ->orderBy('order_date', 'desc')
            ->get()
            ->groupBy(fn ($order) => $order->order_date->format('m/Y'))
            ->map(fn ($orders, $period) => [
                'period' => $period,
                'total' => $orders->sum('total_amount'),
            ])
            ->take(6)
            ->reverse()
            ->values();

        $lowStockSanPhams = SanPham::where('stock', '<=', 5)->orderBy('stock')->take(8)->get();

        $stockSummary = BienDongKho::query()
            ->when($from, fn ($query) => $query->whereDate('movement_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('movement_date', '<=', $to))
            ->get()
            ->groupBy('type')
            ->map(fn ($movements) => $movements->sum('quantity'));

        return view('quan_tri.bao_cao.danh_sach', compact(
            'revenue',
            'completedRevenue',
            'processingDonHangs',
            'newDonHangs',
            'monthlyRevenue',
            'lowStockSanPhams',
            'stockSummary',
            'from',
            'to'
        ));
    }
}
