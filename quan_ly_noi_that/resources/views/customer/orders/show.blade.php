@extends('layouts.customer')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <section class="card">
        <div class="page-header">
            <div>
                <h2>Đơn hàng {{ $order->code }}</h2>
                <p>Theo dõi trạng thái xử lý đơn hàng của bạn.</p>
            </div>
            <a href="{{ route('customer.orders.index') }}" class="button button-soft">Quay lại danh sách</a>
        </div>

        <div style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap:10px; margin-bottom:14px;">
            <div style="border:1px solid #ece4da; border-radius:12px; padding:10px;">
                <strong>Ngày đặt</strong>
                <div style="margin-top:4px;">{{ $order->order_date->format('d/m/Y') }}</div>
            </div>
            <div style="border:1px solid #ece4da; border-radius:12px; padding:10px;">
                <strong>Trạng thái</strong>
                <div style="margin-top:4px;">
                    <span class="badge {{ $order->status === \App\Models\Order::STATUS_COMPLETED ? 'badge-success' : ($order->status === \App\Models\Order::STATUS_PROCESSING ? 'badge-warning' : 'badge-primary') }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>
            <div style="border:1px solid #ece4da; border-radius:12px; padding:10px;">
                <strong>Tổng sản phẩm</strong>
                <div style="margin-top:4px;">{{ $order->items->sum('quantity') }}</div>
            </div>
            <div style="border:1px solid #ece4da; border-radius:12px; padding:10px;">
                <strong>Tổng tiền</strong>
                <div style="margin-top:4px;">{{ number_format($order->total_amount, 0, ',', '.') }}đ</div>
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <strong>Ghi chú</strong>
            <div style="margin-top:6px; color:#6b7280;">
                {{ $order->note ?: 'Không có ghi chú.' }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                        <td>{{ number_format($item->line_total, 0, ',', '.') }}đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
@endsection
