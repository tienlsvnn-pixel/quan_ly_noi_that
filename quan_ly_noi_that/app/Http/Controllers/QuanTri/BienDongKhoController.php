<?php

namespace App\Http\Controllers\QuanTri;

use App\Http\Controllers\BoDieuKhien;
use App\Models\SanPham;
use App\Models\BienDongKho;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BienDongKhoController extends BoDieuKhien
{
    public function index(): View
    {
        $search = request('q');

        $stockMovements = BienDongKho::with('product')
            ->when($search, function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('reference_code', 'like', '%'.$search.'%')
                        ->orWhereHas('product', function ($productQuery) use ($search) {
                            $productQuery->where('name', 'like', '%'.$search.'%')
                                ->orWhere('sku', 'like', '%'.$search.'%');
                        });
                    });
                })
            ->latest('movement_date')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('quan_tri.bien_dong_kho.danh_sach', compact('stockMovements'));
    }

    public function create(): View
    {
        $products = SanPham::orderBy('name')->get();

        return view('quan_tri.bien_dong_kho.tao', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:Nhập kho,Xuất kho'],
            'quantity' => ['required', 'integer', 'min:1'],
            'movement_date' => ['required', 'date'],
            'reference_code' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ], [
            'product_id.required' => 'Vui lòng chọn sản phẩm.',
            'type.required' => 'Vui lòng chọn loại phiếu kho.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
        ]);

        DB::transaction(function () use ($data) {
            $product = SanPham::lockForUpdate()->findOrFail($data['product_id']);
            $stockBefore = (int) $product->stock;
            $quantity = (int) $data['quantity'];

            if ($data['type'] === 'Xuất kho' && $quantity > $stockBefore) {
                throw ValidationException::withMessages([
                    'quantity' => 'Số lượng xuất vượt quá tồn kho hiện tại.',
                ]);
            }

            $stockAfter = $data['type'] === 'Nhập kho'
                ? $stockBefore + $quantity
                : $stockBefore - $quantity;

            $product->update(['stock' => $stockAfter]);

            BienDongKho::create([
                'product_id' => $product->id,
                'type' => $data['type'],
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'movement_date' => $data['movement_date'],
                'reference_code' => $data['reference_code'] ?? null,
                'note' => $data['note'] ?? null,
            ]);
        });

        return redirect()->route('admin.stock-movements.index')->with('status', 'Đã lưu phiếu kho và cập nhật tồn kho.');
    }
}
