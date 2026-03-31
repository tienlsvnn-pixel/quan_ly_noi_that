<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $from = request('from');
        $to = request('to');

        $ordersQuery = Order::query()
            ->when($from, fn ($query) => $query->whereDate('order_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('order_date', '<=', $to));

        $revenue = (clone $ordersQuery)->sum('total_amount');
        $completedRevenue = (clone $ordersQuery)->where('status', Order::STATUS_COMPLETED)->sum('total_amount');
        $processingOrders = (clone $ordersQuery)->where('status', Order::STATUS_PROCESSING)->count();
        $newOrders = (clone $ordersQuery)->where('status', Order::STATUS_NEW)->count();

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

        $lowStockProducts = Product::where('stock', '<=', 5)->orderBy('stock')->take(8)->get();

        $stockSummary = StockMovement::query()
            ->when($from, fn ($query) => $query->whereDate('movement_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('movement_date', '<=', $to))
            ->get()
            ->groupBy('type')
            ->map(fn ($movements) => $movements->sum('quantity'));

        return view('admin.reports.index', compact(
            'revenue',
            'completedRevenue',
            'processingOrders',
            'newOrders',
            'monthlyRevenue',
            'lowStockProducts',
            'stockSummary',
            'from',
            'to'
        ));
    }
}
