<?php

namespace App\Http\Controllers\KhachHang;

use App\Http\Controllers\BoDieuKhien;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\SanPham;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DonHangController extends BoDieuKhien
{
    public function index(Request $request): View
    {
        $customer = $request->user()->getOrCreateKhachHangProfile();

        $orders = DonHang::with('items')
            ->where('customer_id', $customer->id)
            ->latest('order_date')
            ->paginate(10);

        return view('khach_hang.don_hang.danh_sach', compact('orders'));
    }

    public function create(): View
    {
        $products = SanPham::where('is_active', true)->orderBy('name')->get();
        $preferredSanPhamId = request('product_id');

        return view('khach_hang.don_hang.tao', compact('products', 'preferredSanPhamId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = $request->user()->getOrCreateKhachHangProfile();

        $filteredItems = collect($request->input('items', []))
            ->filter(fn (array $item) => filled($item['product_id'] ?? null))
            ->values()
            ->all();

        $request->merge(['items' => $filteredItems]);

        $data = $request->validate([
            'note' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Vui lòng chọn ít nhất một sản phẩm.',
        ]);

        $order = DB::transaction(function () use ($customer, $data) {
            $products = SanPham::whereIn('id', collect($data['items'])->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $totalAmount = collect($data['items'])->sum(function (array $item) use ($products) {
                $product = $products[$item['product_id']];

                return $product->price * $item['quantity'];
            });

            $order = DonHang::create([
                'customer_id' => $customer->id,
                'code' => Str::uuid()->toString(),
                'order_date' => now()->toDateString(),
                'status' => DonHang::STATUS_NEW,
                'stock_applied' => false,
                'total_amount' => $totalAmount,
                'note' => $data['note'] ?? null,
            ]);

            $order->update([
                'code' => $this->generateDonHangCode($order->id),
            ]);

            foreach ($data['items'] as $item) {
                $product = $products[$item['product_id']];

                ChiTietDonHang::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'line_total' => $product->price * $item['quantity'],
                ]);
            }

            return $order;
        });

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('status', 'Đặt đơn hàng thành công. Chúng tôi sẽ xử lý sớm nhất.');
    }

    public function show(Request $request, DonHang $order): View
    {
        $customer = $request->user()->getOrCreateKhachHangProfile();

        abort_if($order->customer_id !== $customer->id, 403, 'Bạn không có quyền xem đơn hàng này.');

        $order->load('items');

        return view('khach_hang.don_hang.chi_tiet', compact('order'));
    }

    private function generateDonHangCode(int $orderId): string
    {
        return 'DH'.str_pad((string) $orderId, 6, '0', STR_PAD_LEFT);
    }
}
