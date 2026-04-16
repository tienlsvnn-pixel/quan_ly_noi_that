<?php

namespace App\Http\Controllers\QuanTri;

use App\Http\Controllers\BoDieuKhien;
use App\Models\DanhMuc;
use App\Models\SanPham;
use App\Models\BienDongKho;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SanPhamController extends BoDieuKhien
{
    public function index(): View
    {
        $search = request('q');

        $products = SanPham::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('quan_tri.san_pham.danh_sach', compact('products'));
    }

    public function create(): View
    {
        $categories = DanhMuc::where('is_active', true)->orderBy('name')->get();

        return view('quan_tri.san_pham.tao', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'material' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'sku.required' => 'Vui lòng nhập mã SKU.',
            'sku.unique' => 'Mã SKU đã tồn tại.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'stock.required' => 'Vui lòng nhập tồn kho.',
        ]);

        DB::transaction(function () use ($data, $request): void {
            $initialStock = (int) $data['stock'];

            $product = SanPham::create([
                ...$data,
                'stock' => 0,
                'slug' => Str::slug($data['name'].'-'.$data['sku']),
                'is_active' => $request->boolean('is_active', true),
            ]);

            if ($initialStock > 0) {
                $product->update(['stock' => $initialStock]);

                BienDongKho::create([
                    'product_id' => $product->id,
                    'type' => 'Nhập kho',
                    'quantity' => $initialStock,
                    'stock_before' => 0,
                    'stock_after' => $initialStock,
                    'movement_date' => now()->toDateString(),
                    'reference_code' => 'INIT-PRODUCT-'.$product->id,
                    'note' => 'Tồn kho khởi tạo khi thêm sản phẩm mới.',
                ]);
            }
        });

        return redirect()->route('admin.products.index')->with('status', 'Đã thêm sản phẩm mới.');
    }

    public function edit(SanPham $product): View
    {
        $categories = DanhMuc::where('is_active', true)->orderBy('name')->get();

        return view('quan_tri.san_pham.sua', compact('product', 'categories'));
    }

    public function update(Request $request, SanPham $product): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,'.$product->id],
            'price' => ['required', 'numeric', 'min:0'],
            'material' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $product->update([
            ...$data,
            'slug' => Str::slug($data['name'].'-'.$data['sku']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.products.index')->with('status', 'Đã cập nhật sản phẩm.');
    }

    public function destroy(SanPham $product): RedirectResponse
    {
        if (
            $product->orderItems()->exists()
            || $product->purchaseReceiptItems()->exists()
            || $product->stockMovements()->exists()
        ) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Không thể xóa sản phẩm đã phát sinh giao dịch kho/đơn hàng/phiếu nhập.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Đã xóa sản phẩm.');
    }
}
