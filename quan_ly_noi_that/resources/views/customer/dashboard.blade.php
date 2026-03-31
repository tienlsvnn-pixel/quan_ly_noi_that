@extends('layouts.customer')

@section('title', 'Tổng quan khách hàng')

@section('content')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .stat-card {
            border: 1px solid #ece4da;
            border-radius: 18px;
            padding: 16px;
            background: #fffdfb;
        }

        .stat-card h3 {
            margin: 0 0 8px;
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-number { font-size: 28px; font-weight: 700; }

        .grid-2 {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 14px;
        }

        .product-list { display: grid; gap: 10px; }

        .product-item {
            border: 1px solid #ece4da;
            border-radius: 14px;
            padding: 12px 14px;
            background: #fffdfb;
        }

        @media (max-width: 960px) {
            .stats-grid,
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>

    <section class="card">
        <div class="page-header">
            <div>
                <h2>Xin chào, {{ $customer->name }}</h2>
                <p>Quản lý đơn hàng và theo dõi tiến độ xử lý ngay tại khu vực khách hàng.</p>
            </div>
            <a href="{{ route('customer.orders.create') }}" class="button button-primary">Đặt hàng mới</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Tổng đơn</h3>
                <div class="stat-number">{{ $totalOrders }}</div>
            </div>
            <div class="stat-card">
                <h3>Đang xử lý</h3>
                <div class="stat-number">{{ $pendingOrders }}</div>
            </div>
            <div class="stat-card">
                <h3>Đã hoàn thành</h3>
                <div class="stat-number">{{ $completedOrders }}</div>
            </div>
        </div>
    </section>

    <div class="grid-2">
        <section class="table-card">
            <div class="page-header">
                <div>
                    <h2 style="font-size:22px;">Đơn hàng gần đây</h2>
                    <p>Theo dõi các đơn bạn vừa đặt.</p>
                </div>
                <a href="{{ route('customer.orders.index') }}" class="button button-soft">Xem tất cả</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('customer.orders.show', $order) }}"><strong>{{ $order->code }}</strong></a></td>
                            <td>{{ $order->order_date->format('d/m/Y') }}</td>
                            <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                            <td>
                                <span class="badge {{ $order->status === \App\Models\Order::STATUS_COMPLETED ? 'badge-success' : ($order->status === \App\Models\Order::STATUS_PROCESSING ? 'badge-warning' : 'badge-primary') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"><div class="empty-state">Bạn chưa có đơn hàng nào.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="card">
            <div class="page-header">
                <div>
                    <h2 style="font-size:22px;">Sản phẩm mới</h2>
                    <p>Danh sách sản phẩm nội thất mới cập nhật.</p>
                </div>
                <a href="{{ route('customer.products.index') }}" class="button button-soft">Xem sản phẩm</a>
            </div>

            <div class="product-list">
                @forelse($featuredProducts as $product)
                    <div class="product-item">
                        <strong>{{ $product->name }}</strong>
                        <div style="margin-top:6px; color:#6b7280;">SKU: {{ $product->sku }}</div>
                        <div style="margin-top:4px;">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                    </div>
                @empty
                    <div class="empty-state">Chưa có sản phẩm nào sẵn sàng.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
