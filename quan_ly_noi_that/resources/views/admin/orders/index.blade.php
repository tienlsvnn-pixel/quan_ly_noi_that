@extends('layouts.admin')

@section('title', 'Đơn hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý đơn hàng</h2>
            <p>Theo dõi khách đặt, số lượng sản phẩm, giá trị đơn hàng và nhịp xử lý của từng giao dịch trong hệ thống.</p>
        </div>
        <a href="{{ route('admin.orders.create') }}" class="primary-button">Tạo đơn hàng</a>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="search-form">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo mã đơn hoặc tên khách hàng">
            <button type="submit" class="secondary-button">Tìm kiếm</button>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Số lượng</th>
                    <th>Tổng tiền</th>
                    <th>Kho</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->code }}</strong></td>
                        <td>{{ $order->customer->name }}</td>
                        <td>{{ $order->order_date->format('d/m/Y') }}</td>
                        <td>{{ $order->items->sum('quantity') }} sản phẩm</td>
                        <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                        <td>
                            <span class="badge {{ $order->stock_applied ? 'badge-success' : 'badge-warning' }}">
                                {{ $order->stock_applied ? 'Đã trừ kho' : 'Chưa trừ kho' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $order->status === \App\Models\Order::STATUS_COMPLETED ? 'badge-success' : ($order->status === \App\Models\Order::STATUS_PROCESSING ? 'badge-warning' : 'badge-primary') }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="secondary-button">Chi tiết</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8"><div class="empty-state">Chưa có đơn hàng nào trong hệ thống.</div></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $orders->links() }}</div>
    </div>
@endsection
