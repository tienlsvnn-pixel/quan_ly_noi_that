@extends('layouts.quan_tri')

@section('title', 'Khách hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý khách hàng</h2>
            <p>Lưu trữ thông tin liên hệ, khu vực và lịch sử đơn hàng để chăm sóc khách hàng nội thất chuyên nghiệp và có chiều sâu hơn.</p>
        </div>
        <a href="{{ route('admin.customers.create') }}" class="primary-button">Thêm khách hàng</a>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="search-form">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên, email hoặc số điện thoại">
            <button type="submit" class="secondary-button">Tìm kiếm</button>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Liên hệ</th>
                    <th>Khu vực</th>
                    <th>Số đơn hàng</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>
                            <strong>{{ $customer->name }}</strong>
                            <div style="color:#6b7280; margin-top:6px;">{{ $customer->note ?: 'Chưa có ghi chú chăm sóc.' }}</div>
                        </td>
                        <td>
                            <div>{{ $customer->phone ?: 'Chưa có số điện thoại' }}</div>
                            <div style="color:#6b7280; margin-top:6px;">{{ $customer->email ?: 'Chưa có email' }}</div>
                        </td>
                        <td>{{ $customer->city ?: 'Chưa cập nhật' }}</td>
                        <td>{{ $customer->orders_count }}</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="secondary-button">Sửa</a>
                                <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger-button">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"><div class="empty-state">Chưa có khách hàng nào trong hệ thống.</div></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $customers->links() }}</div>
    </div>
@endsection
