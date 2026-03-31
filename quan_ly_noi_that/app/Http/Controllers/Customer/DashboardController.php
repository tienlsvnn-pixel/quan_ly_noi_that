<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user()->getOrCreateCustomerProfile();

        $ordersQuery = Order::where('customer_id', $customer->id);

        $totalOrders = (clone $ordersQuery)->count();
        $pendingOrders = (clone $ordersQuery)->whereIn('status', [Order::STATUS_NEW, Order::STATUS_PROCESSING])->count();
        $completedOrders = (clone $ordersQuery)->where('status', Order::STATUS_COMPLETED)->count();
        $recentOrders = (clone $ordersQuery)->latest('order_date')->take(5)->get();
        $featuredProducts = Product::where('is_active', true)->orderByDesc('created_at')->take(6)->get();

        return view('customer.dashboard', compact(
            'customer',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'recentOrders',
            'featuredProducts'
        ));
    }
}
