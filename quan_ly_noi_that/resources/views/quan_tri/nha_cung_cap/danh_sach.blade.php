@extends('layouts.quan_tri')

@section('title', 'Nhà cung cấp')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý nhà cung cấp</h2>
            <p>Theo dõi đối tác cung ứng, thông tin liên hệ và số phiếu nhập đã phát sinh để việc nhập hàng nội thất có hệ thống hơn.</p>
        </div>
        <a href="{{ route('admin.suppliers.create') }}" class="primary-button">Thêm nhà cung cấp</a>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.suppliers.index') }}" class="search-form">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên, số điện thoại hoặc email">
            <button type="submit" class="secondary-button">Tìm kiếm</button>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Nhà cung cấp</th>
                    <th>Liên hệ</th>
                    <th>Khu vực</th>
                    <th>Số phiếu nhập</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td>
                            <strong>{{ $supplier->name }}</strong>
                            <div style="color:#6b7280; margin-top:6px;">{{ $supplier->contact_person ?: 'Chưa có người liên hệ' }}</div>
                        </td>
                        <td>
                            <div>{{ $supplier->phone ?: 'Chưa có số điện thoại' }}</div>
                            <div style="color:#6b7280; margin-top:6px;">{{ $supplier->email ?: 'Chưa có email' }}</div>
                        </td>
                        <td>{{ $supplier->city ?: 'Chưa cập nhật' }}</td>
                        <td>{{ $supplier->purchase_receipts_count }}</td>
                        <td>
                            <span class="badge {{ $supplier->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $supplier->is_active ? 'Đang hợp tác' : 'Tạm ngưng' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="secondary-button">Sửa</a>
                                <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger-button">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"><div class="empty-state">Chưa có nhà cung cấp nào trong hệ thống.</div></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $suppliers->links() }}</div>
    </div>
@endsection
