<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\PurchaseReceipt;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalCategories = Category::count();
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalOrders = Order::count();
        $totalSuppliers = Supplier::count();
        $totalPurchaseReceipts = PurchaseReceipt::count();

        $recentOrders = Order::with('customer')->latest('order_date')->take(5)->get();
        $topProducts = Product::orderByDesc('stock')->take(4)->get();
        $latestCustomers = Customer::latest()->take(3)->get();
        $latestSuppliers = Supplier::latest()->take(3)->get();

        return view('admin.dashboard', compact(
            'totalCategories',
            'totalProducts',
            'totalCustomers',
            'totalOrders',
            'totalSuppliers',
            'totalPurchaseReceipts',
            'recentOrders',
            'topProducts',
            'latestCustomers',
            'latestSuppliers'
        ));
    }
}
