<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $search = request('q');

        $orders = Order::with(['customer', 'items'])
            ->when($search, function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('code', 'like', '%'.$search.'%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', '%'.$search.'%');
                        });
                });
            })
            ->latest('order_date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('admin.orders.create', compact('customers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $filteredItems = collect($request->input('items', []))
            ->filter(fn (array $item) => filled($item['product_id'] ?? null))
            ->values()
            ->all();

        $request->merge(['items' => $filteredItems]);

        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'status' => ['required', Rule::in(Order::STATUSES)],
            'note' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'customer_id.required' => 'Vui lòng chọn khách hàng.',
            'items.required' => 'Vui lòng thêm ít nhất một sản phẩm vào đơn hàng.',
        ]);

        $order = DB::transaction(function () use ($data) {
            $products = Product::whereIn('id', collect($data['items'])->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $totalAmount = collect($data['items'])->sum(function (array $item) use ($products) {
                $product = $products[$item['product_id']];

                return $product->price * $item['quantity'];
            });

            $order = Order::create([
                'customer_id' => $data['customer_id'],
                'code' => Str::uuid()->toString(),
                'order_date' => $data['order_date'],
                'status' => $data['status'],
                'stock_applied' => false,
                'total_amount' => $totalAmount,
                'note' => $data['note'] ?? null,
            ]);

            $order->update([
                'code' => $this->generateOrderCode($order->id),
            ]);

            foreach ($data['items'] as $item) {
                $product = $products[$item['product_id']];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'line_total' => $product->price * $item['quantity'],
                ]);
            }

            if ($order->isCompleted()) {
                $this->applyOrderStock($order->fresh('items.product'));
            }

            return $order;
        });

        return redirect()->route('admin.orders.show', $order)->with('status', 'Đã tạo đơn hàng mới.');
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
            'note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $order) {
            $order->loadMissing('items.product');
            $oldStatus = $order->status;

            $order->update($data);

            if ($oldStatus !== Order::STATUS_COMPLETED && $order->isCompleted()) {
                $this->applyOrderStock($order);
            }

            if ($oldStatus === Order::STATUS_COMPLETED && !$order->isCompleted()) {
                $this->restoreOrderStock($order);
            }
        });

        return redirect()->route('admin.orders.show', $order)->with('status', 'Đã cập nhật trạng thái đơn hàng.');
    }

    private function applyOrderStock(Order $order): void
    {
        if ($order->stock_applied) {
            return;
        }

        foreach ($order->items as $item) {
            if (!$item->product) {
                continue;
            }

            $product = Product::lockForUpdate()->findOrFail($item->product->id);

            if ($product->stock < $item->quantity) {
                throw ValidationException::withMessages([
                    'status' => "Không đủ tồn kho để hoàn thành đơn {$order->code} cho sản phẩm {$product->name}.",
                ]);
            }

            $stockBefore = (int) $product->stock;
            $stockAfter = $stockBefore - $item->quantity;

            $product->update(['stock' => $stockAfter]);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'Xuất kho',
                'quantity' => $item->quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'movement_date' => $order->order_date,
                'reference_code' => $order->code,
                'note' => 'Xuất kho tự động khi đơn hàng hoàn thành.',
            ]);
        }

        $order->update(['stock_applied' => true]);
    }

    private function restoreOrderStock(Order $order): void
    {
        if (!$order->stock_applied) {
            return;
        }

        foreach ($order->items as $item) {
            if (!$item->product) {
                continue;
            }

            $product = Product::lockForUpdate()->findOrFail($item->product->id);
            $stockBefore = (int) $product->stock;
            $stockAfter = $stockBefore + $item->quantity;

            $product->update(['stock' => $stockAfter]);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'Nhập kho',
                'quantity' => $item->quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'movement_date' => now()->toDateString(),
                'reference_code' => $order->code.'-RETURN',
                'note' => 'Hoàn tồn kho do đơn hàng được chuyển khỏi trạng thái hoàn thành.',
            ]);
        }

        $order->update(['stock_applied' => false]);
    }

    private function generateOrderCode(int $orderId): string
    {
        return 'DH'.str_pad((string) $orderId, 6, '0', STR_PAD_LEFT);
    }
}
