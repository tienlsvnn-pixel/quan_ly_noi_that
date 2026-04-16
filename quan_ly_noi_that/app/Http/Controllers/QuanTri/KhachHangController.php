<?php

namespace App\Http\Controllers\QuanTri;

use App\Http\Controllers\BoDieuKhien;
use App\Models\KhachHang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KhachHangController extends BoDieuKhien
{
    public function index(): View
    {
        $search = request('q');

        $customers = KhachHang::withCount('orders')
            ->when($search, function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('quan_tri.khach_hang.danh_sach', compact('customers'));
    }

    public function create(): View
    {
        return view('quan_tri.khach_hang.tao');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ], [
            'name.required' => 'Vui lòng nhập tên khách hàng.',
            'email.email' => 'Email khách hàng không đúng định dạng.',
            'email.unique' => 'Email khách hàng đã tồn tại.',
        ]);

        KhachHang::create($data);

        return redirect()->route('admin.customers.index')->with('status', 'Đã thêm khách hàng mới.');
    }

    public function edit(KhachHang $customer): View
    {
        return view('quan_tri.khach_hang.sua', compact('customer'));
    }

    public function update(Request $request, KhachHang $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email,'.$customer->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        $customer->update($data);

        return redirect()->route('admin.customers.index')->with('status', 'Đã cập nhật khách hàng.');
    }

    public function destroy(KhachHang $customer): RedirectResponse
    {
        if ($customer->orders()->exists()) {
            return redirect()
                ->route('admin.customers.index')
                ->with('error', 'Không thể xóa khách hàng đã có đơn hàng phát sinh.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('status', 'Đã xóa khách hàng.');
    }
}
