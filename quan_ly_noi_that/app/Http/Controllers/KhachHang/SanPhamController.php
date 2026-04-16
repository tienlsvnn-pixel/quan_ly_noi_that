<?php

namespace App\Http\Controllers\KhachHang;

use App\Http\Controllers\BoDieuKhien;
use App\Models\SanPham;
use Illuminate\View\View;

class SanPhamController extends BoDieuKhien
{
    public function index(): View
    {
        $search = request('q');

        $products = SanPham::query()
            ->where('is_active', true)
            ->when($search, function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%')
                        ->orWhere('material', 'like', '%'.$search.'%')
                        ->orWhere('color', 'like', '%'.$search.'%');
                });
            })
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('khach_hang.san_pham.danh_sach', compact('products'));
    }
}
