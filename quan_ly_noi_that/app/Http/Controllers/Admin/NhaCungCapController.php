<?php

namespace App\Http\Controllers\QuanTri;

use App\Http\Controllers\BoDieuKhien;
use App\Models\NhaCungCap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NhaCungCapController extends BoDieuKhien
{
    public function index(): View
    {
        $search = request('q');

        $suppliers = NhaCungCap::withCount('purchaseReceipts')
            ->when($search, function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('quan_tri.nha_cung_cap.danh_sach', compact('suppliers'));
    }

    public function create(): View
    {
        return view('quan_tri.nha_cung_cap.tao');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255', 'unique:suppliers,email'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'email.email' => 'Email nhà cung cấp không đúng định dạng.',
            'email.unique' => 'Email nhà cung cấp đã tồn tại.',
        ]);

        NhaCungCap::create([
            ...$data,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.suppliers.index')->with('status', 'Đã thêm nhà cung cấp mới.');
    }

    public function edit(NhaCungCap $supplier): View
    {
        return view('quan_tri.nha_cung_cap.sua', compact('supplier'));
    }

    public function update(Request $request, NhaCungCap $supplier): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255', 'unique:suppliers,email,'.$supplier->id],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $supplier->update([
            ...$data,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.suppliers.index')->with('status', 'Đã cập nhật nhà cung cấp.');
    }

    public function destroy(NhaCungCap $supplier): RedirectResponse
    {
        if ($supplier->purchaseReceipts()->exists()) {
            return redirect()
                ->route('admin.suppliers.index')
                ->with('error', 'Không thể xóa nhà cung cấp đã có phiếu nhập hàng.');
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')->with('status', 'Đã xóa nhà cung cấp.');
    }
}
