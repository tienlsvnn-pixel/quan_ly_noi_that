@extends('layouts.khach_hang')

@section('title', 'Tổng quan khách hàng')

@section('content')
    <style>
        .overview-shell {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 16px;
            margin-bottom: 8px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .stat-card {
            border: 1px solid #ece4da;
            border-radius: 16px;
            padding: 16px;
            background: #fffdfb;
        }

        .stat-card h3 {
            margin: 0 0 8px;
            font-size: 12px;
            color: #64707d;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
        }

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
            .overview-shell {
                grid-template-columns: 1fr;
                align-items: stretch;
            }

            .stats-grid,
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="card">
        <div class="overview-shell">
            <div class="stats-grid">
                <article class="stat-card">
                    <h3>Tổng đơn</h3>
                    <div class="stat-number">{{ $totalDonHangs }}</div>
                </article>
                <article class="stat-card">
                    <h3>Đang xử lý</h3>
                    <div class="stat-number">{{ $pendingDonHangs }}</div>
                </article>
                <article class="stat-card">
                    <h3>Đã hoàn thành</h3>
                    <div class="stat-number">{{ $completedDonHangs }}</div>
                </article>
            </div>

            <a href="{{ route('customer.orders.create') }}" class="button button-primary">Đặt hàng mới</a>
        </div>
    </section>

    <div class="grid-2">
        <section class="table-card">
            <div class="page-header">
                <h2 style="font-size:22px;">Đơn hàng gần đây</h2>
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
                    @forelse($recentDonHangs as $order)
                        <tr>
                            <td><a href="{{ route('customer.orders.show', $order) }}"><strong>{{ $order->code }}</strong></a></td>
                            <td>{{ $order->order_date->format('d/m/Y') }}</td>
                            <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                            <td>
                                <span class="badge {{ $order->status === \App\Models\DonHang::STATUS_COMPLETED ? 'badge-success' : ($order->status === \App\Models\DonHang::STATUS_PROCESSING ? 'badge-warning' : 'badge-primary') }}">
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
                <h2 style="font-size:22px;">Sản phẩm mới</h2>
                <a href="{{ route('customer.products.index') }}" class="button button-soft">Xem sản phẩm</a>
            </div>

            <div class="product-list">
                @forelse($featuredSanPhams as $product)
                    <div class="product-item">
                        <strong>{{ $product->name }}</strong>
                        <div style="margin-top:6px; color:#64707d;">SKU: {{ $product->sku }}</div>
                        <div style="margin-top:4px;">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                    </div>
                @empty
                    <div class="empty-state">Chưa có sản phẩm nào sẵn sàng.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
