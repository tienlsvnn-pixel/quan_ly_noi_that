@extends('layouts.quan_tri')

@section('title', 'Tổng quan')

@section('content')
    <style>
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 14px;
        }

        .stat-card {
            padding: 18px;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid #e9e1d6;
            box-shadow: 0 14px 30px rgba(31, 41, 51, 0.06);
        }

        .stat-card h3 {
            margin: 0 0 8px;
            font-size: 12px;
            color: #64707d;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-card .number {
            font-size: 30px;
            font-weight: 700;
            line-height: 1.2;
        }

        .dashboard-layout {
            display: grid;
            grid-template-columns: 1.35fr 0.85fr;
            gap: 18px;
        }

        .section-title {
            margin: 0;
            font-size: 22px;
        }

        .list-stack {
            display: grid;
            gap: 10px;
        }

        .mini-card {
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #ece4da;
            background: #faf8f4;
        }

        .mini-card strong {
            display: block;
            margin-bottom: 4px;
        }

        .muted {
            color: #64707d;
            font-size: 14px;
        }

        @media (max-width: 1240px) {
            .stats-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .dashboard-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 560px) {
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

    <section class="stats-grid">
        <article class="stat-card">
            <h3>Danh mục</h3>
            <div class="number">{{ $totalCategories }}</div>
        </article>
        <article class="stat-card">
            <h3>Sản phẩm</h3>
            <div class="number">{{ $totalSanPhams }}</div>
        </article>
        <article class="stat-card">
            <h3>Khách hàng</h3>
            <div class="number">{{ $totalKhachHangs }}</div>
        </article>
        <article class="stat-card">
            <h3>Đơn hàng</h3>
            <div class="number">{{ $totalDonHangs }}</div>
        </article>
        <article class="stat-card">
            <h3>Nhà cung cấp</h3>
            <div class="number">{{ $totalNhaCungCaps }}</div>
        </article>
        <article class="stat-card">
            <h3>Phiếu nhập</h3>
            <div class="number">{{ $totalPhieuNhaps }}</div>
        </article>
    </section>

    <div class="dashboard-layout">
        <section class="table-card">
            <div class="page-header" style="margin-bottom: 14px;">
                <h2 class="section-title">Đơn hàng gần đây</h2>
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
                    @forelse($recentDonHangs as $order)
                        <tr>
                            <td>{{ $order->code }}</td>
                            <td>{{ $order->customer->name }}</td>
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
                            <td colspan="5"><div class="empty-state">Chưa có đơn hàng.</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <div class="card-grid">
            <section class="info-card">
                <div class="page-header" style="margin-bottom: 12px;">
                    <h2 class="section-title">Sản phẩm nổi bật</h2>
                </div>

                <div class="list-stack">
                    @forelse($topSanPhams as $product)
                        <div class="mini-card">
                            <strong>{{ $product->name }}</strong>
                            <div class="muted">SKU: {{ $product->sku }}</div>
                            <div class="muted">Tồn kho: {{ $product->stock }}</div>
                        </div>
                    @empty
                        <div class="empty-state">Chưa có sản phẩm.</div>
                    @endforelse
                </div>
            </section>

            <section class="info-card">
                <div class="page-header" style="margin-bottom: 12px;">
                    <h2 class="section-title">Khách hàng mới</h2>
                </div>

                <div class="list-stack">
                    @forelse($latestKhachHangs as $customer)
                        <div class="mini-card">
                            <strong>{{ $customer->name }}</strong>
                            <div class="muted">{{ $customer->phone ?: 'Chưa có số điện thoại' }}</div>
                            <div class="muted">{{ $customer->city ?: 'Chưa cập nhật khu vực' }}</div>
                        </div>
                    @empty
                        <div class="empty-state">Chưa có khách hàng.</div>
                    @endforelse
                </div>
            </section>

            <section class="info-card">
                <div class="page-header" style="margin-bottom: 12px;">
                    <h2 class="section-title">Nhà cung cấp mới</h2>
                </div>

                <div class="list-stack">
                    @forelse($latestNhaCungCaps as $supplier)
                        <div class="mini-card">
                            <strong>{{ $supplier->name }}</strong>
                            <div class="muted">{{ $supplier->contact_person ?: 'Chưa có người liên hệ' }}</div>
                            <div class="muted">{{ $supplier->city ?: 'Chưa cập nhật khu vực' }}</div>
                        </div>
                    @empty
                        <div class="empty-state">Chưa có nhà cung cấp.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
