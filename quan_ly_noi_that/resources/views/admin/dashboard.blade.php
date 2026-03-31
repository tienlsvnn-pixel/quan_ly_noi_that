@extends('layouts.admin')

@section('title', 'Tổng quan')

@section('content')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 16px;
        }

        .stat-card {
            padding: 18px;
            border-radius: 22px;
            background: #ffffff;
            border: 1px solid #e8e1d8;
            box-shadow: 0 18px 34px rgba(31, 41, 51, 0.06);
        }

        .stat-card h3 {
            margin: 0 0 10px;
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-card .number {
            font-size: 30px;
            font-weight: 700;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.35fr 0.85fr;
            gap: 18px;
            margin-top: 18px;
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .list-stack {
            display: grid;
            gap: 12px;
        }

        .mini-card {
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid #ece4da;
            background: #faf8f4;
        }

        .mini-card strong {
            display: block;
            margin-bottom: 4px;
        }

        .muted {
            color: #6b7280;
            font-size: 14px;
        }

        @media (max-width: 1180px) {
            .stats-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="page-box">
        <div class="quick-actions">
            <a href="{{ route('admin.orders.create') }}" class="primary-button">Tạo đơn hàng</a>
            <a href="{{ route('admin.purchase-receipts.create') }}" class="secondary-button">Tạo phiếu nhập</a>
            <a href="{{ route('admin.stock-movements.create') }}" class="secondary-button">Điều chỉnh kho</a>
        </div>
    </section>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Danh mục</h3>
            <div class="number">{{ $totalCategories }}</div>
        </div>
        <div class="stat-card">
            <h3>Sản phẩm</h3>
            <div class="number">{{ $totalProducts }}</div>
        </div>
        <div class="stat-card">
            <h3>Khách hàng</h3>
            <div class="number">{{ $totalCustomers }}</div>
        </div>
        <div class="stat-card">
            <h3>Đơn hàng</h3>
            <div class="number">{{ $totalOrders }}</div>
        </div>
        <div class="stat-card">
            <h3>Nhà cung cấp</h3>
            <div class="number">{{ $totalSuppliers }}</div>
        </div>
        <div class="stat-card">
            <h3>Phiếu nhập</h3>
            <div class="number">{{ $totalPurchaseReceipts }}</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="card-grid">
            <div class="table-card">
                <div class="page-header" style="margin-bottom: 14px;">
                    <div>
                        <h2 style="font-size: 22px; margin-bottom: 4px;">Đơn hàng gần đây</h2>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="secondary-button">Xem tất cả</a>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Giá trị</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->code }}</td>
                                <td>{{ $order->customer->name }}</td>
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
                                <td colspan="5"><div class="empty-state">Chưa có đơn hàng.</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-grid">
            <div class="info-card">
                <div class="page-header" style="margin-bottom: 14px;">
                    <div>
                        <h2 style="font-size: 22px; margin-bottom: 4px;">Sản phẩm nổi bật</h2>
                    </div>
                </div>

                <div class="list-stack">
                    @forelse($topProducts as $product)
                        <div class="mini-card">
                            <strong>{{ $product->name }}</strong>
                            <div class="muted">SKU: {{ $product->sku }}</div>
                            <div class="muted">Tồn kho: {{ $product->stock }}</div>
                        </div>
                    @empty
                        <div class="empty-state">Chưa có sản phẩm.</div>
                    @endforelse
                </div>
            </div>

            <div class="info-card">
                <div class="page-header" style="margin-bottom: 14px;">
                    <div>
                        <h2 style="font-size: 22px; margin-bottom: 4px;">Khách hàng mới</h2>
                    </div>
                </div>

                <div class="list-stack">
                    @forelse($latestCustomers as $customer)
                        <div class="mini-card">
                            <strong>{{ $customer->name }}</strong>
                            <div class="muted">{{ $customer->phone ?: 'Chưa có số điện thoại' }}</div>
                            <div class="muted">{{ $customer->city ?: 'Chưa cập nhật khu vực' }}</div>
                        </div>
                    @empty
                        <div class="empty-state">Chưa có khách hàng.</div>
                    @endforelse
                </div>
            </div>

            <div class="info-card">
                <div class="page-header" style="margin-bottom: 14px;">
                    <div>
                        <h2 style="font-size: 22px; margin-bottom: 4px;">Nhà cung cấp mới</h2>
                    </div>
                </div>

                <div class="list-stack">
                    @forelse($latestSuppliers as $supplier)
                        <div class="mini-card">
                            <strong>{{ $supplier->name }}</strong>
                            <div class="muted">{{ $supplier->contact_person ?: 'Chưa có người liên hệ' }}</div>
                            <div class="muted">{{ $supplier->city ?: 'Chưa cập nhật khu vực' }}</div>
                        </div>
                    @empty
                        <div class="empty-state">Chưa có nhà cung cấp.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
