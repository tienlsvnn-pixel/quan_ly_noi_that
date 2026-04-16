@extends('layouts.khach_hang')

@section('title', 'Đơn hàng của tôi')

@section('content')
    <section class="table-card">
        <div class="page-header">
            <div>
                <h2>Đơn hàng của tôi</h2>
                <p>Danh sách toàn bộ đơn hàng bạn đã tạo trên hệ thống.</p>
            </div>
            <a href="{{ route('customer.orders.create') }}" class="button button-primary">Đặt hàng mới</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Số lượng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><strong>{{ $order->code }}</strong></td>
                        <td>{{ $order->order_date->format('d/m/Y') }}</td>
                        <td>{{ $order->items->sum('quantity') }}</td>
                        <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                        <td>
                            <span class="badge {{ $order->status === \App\Models\DonHang::STATUS_COMPLETED ? 'badge-success' : ($order->status === \App\Models\DonHang::STATUS_PROCESSING ? 'badge-warning' : 'badge-primary') }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td><a href="{{ route('customer.orders.show', $order) }}" class="button button-soft">Chi tiết</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"><div class="empty-state">Bạn chưa có đơn hàng nào.</div></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:14px;">
            {{ $orders->links() }}
        </div>
    </section>
@endsection
