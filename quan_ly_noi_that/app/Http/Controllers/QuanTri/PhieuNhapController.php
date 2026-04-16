<?php

namespace App\Http\Controllers\QuanTri;

use App\Http\Controllers\BoDieuKhien;
use App\Models\SanPham;
use App\Models\PhieuNhap;
use App\Models\ChiTietPhieuNhap;
use App\Models\BienDongKho;
use App\Models\NhaCungCap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PhieuNhapController extends BoDieuKhien
{
    public function index(): View
    {
        $filters = [
            'q' => trim((string) request('q', '')),
            'status' => request('status'),
            'stock' => request('stock'),
            'from' => request('from'),
            'to' => request('to'),
        ];

        $receiptsQuery = PhieuNhap::query()
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($innerQuery) use ($filters) {
                    $innerQuery->where('code', 'like', '%'.$filters['q'].'%')
                        ->orWhereHas('supplier', function ($supplierQuery) use ($filters) {
                            $supplierQuery->where('name', 'like', '%'.$filters['q'].'%');
                        });
                });
            })
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['stock'] === 'applied', fn ($query) => $query->where('stock_applied', true))
            ->when($filters['stock'] === 'pending', fn ($query) => $query->where('stock_applied', false))
            ->when($filters['from'], fn ($query, $from) => $query->whereDate('receipt_date', '>=', $from))
            ->when($filters['to'], fn ($query, $to) => $query->whereDate('receipt_date', '<=', $to));

        $overview = [
            'total_receipts' => (clone $receiptsQuery)->count(),
            'total_amount' => (float) (clone $receiptsQuery)->sum('total_amount'),
            'imported_receipts' => (clone $receiptsQuery)->where('status', PhieuNhap::STATUS_IMPORTED)->count(),
            'draft_receipts' => (clone $receiptsQuery)->where('status', PhieuNhap::STATUS_DRAFT)->count(),
            'pending_stock_receipts' => (clone $receiptsQuery)->where('stock_applied', false)->count(),
        ];

        $purchaseReceipts = (clone $receiptsQuery)
            ->with(['supplier', 'items'])
            ->latest('receipt_date')
            ->paginate(10)
            ->withQueryString();

        return view('quan_tri.phieu_nhap.danh_sach', compact('purchaseReceipts', 'overview', 'filters'));
    }

    public function create(): View
    {
        $suppliers = NhaCungCap::where('is_active', true)->orderBy('name')->get();
        $products = SanPham::where('is_active', true)->orderBy('name')->get();

        return view('quan_tri.phieu_nhap.tao', compact('suppliers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $filteredItems = collect($request->input('items', []))
            ->filter(fn (array $item) => filled($item['product_id'] ?? null))
            ->values()
            ->all();

        $request->merge(['items' => $filteredItems]);

        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'receipt_date' => ['required', 'date'],
            'status' => ['required', Rule::in(PhieuNhap::STATUSES)],
            'note' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ], [
            'supplier_id.required' => 'Vui lòng chọn nhà cung cấp.',
            'items.required' => 'Vui lòng thêm ít nhất một sản phẩm vào phiếu nhập.',
        ]);

        $receipt = DB::transaction(function () use ($data) {
            $products = SanPham::whereIn('id', collect($data['items'])->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $totalAmount = collect($data['items'])->sum(fn (array $item) => $item['unit_cost'] * $item['quantity']);

            $receipt = PhieuNhap::create([
                'supplier_id' => $data['supplier_id'],
                'code' => Str::uuid()->toString(),
                'receipt_date' => $data['receipt_date'],
                'status' => $data['status'],
                'stock_applied' => false,
                'total_amount' => $totalAmount,
                'note' => $data['note'] ?? null,
            ]);

            $receipt->update([
                'code' => $this->generateReceiptCode($receipt->id),
            ]);

            foreach ($data['items'] as $item) {
                $product = $products[$item['product_id']];

                ChiTietPhieuNhap::create([
                    'purchase_receipt_id' => $receipt->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'line_total' => $item['unit_cost'] * $item['quantity'],
                ]);
            }

            if ($receipt->isImported()) {
                $this->applyReceiptStock($receipt->fresh('items.product'));
            }

            return $receipt;
        });

        return redirect()->route('admin.purchase-receipts.show', $receipt)->with('status', 'Đã tạo phiếu nhập hàng mới.');
    }

    public function show(PhieuNhap $purchaseReceipt): View
    {
        $purchaseReceipt->load(['supplier', 'items.product']);

        return view('quan_tri.phieu_nhap.chi_tiet', compact('purchaseReceipt'));
    }

    public function update(Request $request, PhieuNhap $purchaseReceipt): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(PhieuNhap::STATUSES)],
            'note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $purchaseReceipt) {
            $purchaseReceipt->loadMissing('items.product');
            $oldStatus = $purchaseReceipt->status;
            $purchaseReceipt->update($data);

            if ($oldStatus !== PhieuNhap::STATUS_IMPORTED && $purchaseReceipt->isImported()) {
                $this->applyReceiptStock($purchaseReceipt);
            }

            if ($oldStatus === PhieuNhap::STATUS_IMPORTED && !$purchaseReceipt->isImported()) {
                $this->restoreReceiptStock($purchaseReceipt);
            }
        });

        return redirect()->route('admin.purchase-receipts.show', $purchaseReceipt)->with('status', 'Đã cập nhật phiếu nhập hàng.');
    }

    private function applyReceiptStock(PhieuNhap $receipt): void
    {
        if ($receipt->stock_applied) {
            return;
        }

        foreach ($receipt->items as $item) {
            if (!$item->product) {
                continue;
            }

            $product = SanPham::lockForUpdate()->findOrFail($item->product->id);
            $stockBefore = (int) $product->stock;
            $stockAfter = $stockBefore + $item->quantity;

            $product->update(['stock' => $stockAfter]);

            BienDongKho::create([
                'product_id' => $product->id,
                'type' => 'Nhập kho',
                'quantity' => $item->quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'movement_date' => $receipt->receipt_date,
                'reference_code' => $receipt->code,
                'note' => 'Nhập kho tự động từ phiếu nhập hàng.',
            ]);
        }

        $receipt->update(['stock_applied' => true]);
    }

    private function restoreReceiptStock(PhieuNhap $receipt): void
    {
        if (!$receipt->stock_applied) {
            return;
        }

        foreach ($receipt->items as $item) {
            if (!$item->product) {
                continue;
            }

            $product = SanPham::lockForUpdate()->findOrFail($item->product->id);

            if ($product->stock < $item->quantity) {
                throw ValidationException::withMessages([
                    'status' => "Không đủ tồn kho để hoàn tác phiếu nhập {$receipt->code} cho sản phẩm {$product->name}.",
                ]);
            }

            $stockBefore = (int) $product->stock;
            $stockAfter = $stockBefore - $item->quantity;

            $product->update(['stock' => $stockAfter]);

            BienDongKho::create([
                'product_id' => $product->id,
                'type' => 'Xuất kho',
                'quantity' => $item->quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'movement_date' => now()->toDateString(),
                'reference_code' => $receipt->code.'-REVERSE',
                'note' => 'Hoàn tác tồn kho do phiếu nhập được chuyển về trạng thái nháp.',
            ]);
        }

        $receipt->update(['stock_applied' => false]);
    }

    private function generateReceiptCode(int $receiptId): string
    {
        return 'PN'.str_pad((string) $receiptId, 6, '0', STR_PAD_LEFT);
    }
}
