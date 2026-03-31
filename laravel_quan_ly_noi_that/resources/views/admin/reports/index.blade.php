@extends('layouts.admin')

@section('title', 'Báo cáo')

@section('content')
    <style>
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat-item {
            padding: 22px;
            border-radius: 20px;
            background: linear-gradient(180deg, #ffffff, #fff9f3);
            border: 1px solid rgba(120, 53, 15, 0.12);
        }

        .stat-item h3 {
            font-size: 14px;
            color: #92400e;
            margin-bottom: 10px;
        }

        .stat-item .number {
            font-size: 30px;
            font-weight: 700;
        }

        .report-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 20px;
        }

        .list-stack {
            display: grid;
            gap: 12px;
        }

        .mini-card {
            padding: 16px;
            border-radius: 16px;
            background: #fffdfb;
            border: 1px solid #efe7df;
        }

        .mini-card strong {
            display: block;
            margin-bottom: 6px;
        }

        .muted {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }

        @media (max-width: 992px) {
            .stats-row,
            .report-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="page-header">
        <div>
            <h2>Báo cáo kinh doanh</h2>
            <p>Tổng hợp doanh thu, trạng thái đơn hàng và tình hình kho theo khoảng thời gian bạn muốn theo dõi.</p>
        </div>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="search-form">
            <input type="date" name="from" value="{{ $from }}">
            <input type="date" name="to" value="{{ $to }}">
            <button type="submit" class="secondary-button">Lọc báo cáo</button>
            <a href="{{ route('admin.reports.index') }}" class="danger-button">Xóa lọc</a>
        </form>
    </div>

    <div class="stats-row">
        <div class="stat-item">
            <h3>Tổng doanh thu</h3>
            <div class="number">{{ number_format($revenue, 0, ',', '.') }}đ</div>
        </div>
        <div class="stat-item">
            <h3>Doanh thu hoàn thành</h3>
            <div class="number">{{ number_format($completedRevenue, 0, ',', '.') }}đ</div>
        </div>
        <div class="stat-item">
            <h3>Đơn đang xử lý</h3>
            <div class="number">{{ $processingOrders }}</div>
        </div>
        <div class="stat-item">
            <h3>Đơn mới</h3>
            <div class="number">{{ $newOrders }}</div>
        </div>
    </div>

    <div class="report-grid">
        <div class="card-grid">
            <div class="table-card">
                <div class="page-header" style="margin-bottom: 14px;">
                    <div>
                        <h2 style="font-size: 22px; margin-bottom: 4px;">Doanh thu theo tháng</h2>
                        <p>Thống kê nhanh giá trị đơn hàng theo từng tháng trong phạm vi dữ liệu đã chọn.</p>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Tháng</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyRevenue as $item)
                            <tr>
                                <td>{{ $item['period'] }}</td>
                                <td>{{ number_format($item['total'], 0, ',', '.') }}đ</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2"><div class="empty-state">Chưa có dữ liệu doanh thu theo tháng trong phạm vi đã lọc.</div></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-card">
                <div class="page-header" style="margin-bottom: 14px;">
                    <div>
                        <h2 style="font-size: 22px; margin-bottom: 4px;">Tổng hợp nhập xuất kho</h2>
                        <p>Khối lượng hàng hóa đã được ghi nhận vào và ra khỏi kho trong khoảng thời gian tương ứng.</p>
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Loại phiếu</th>
                            <th>Tổng số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Nhập kho</td>
                            <td>{{ $stockSummary['Nhập kho'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td>Xuất kho</td>
                            <td>{{ $stockSummary['Xuất kho'] ?? 0 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="info-card">
            <div class="page-header" style="margin-bottom: 14px;">
                <div>
                    <h2 style="font-size: 22px; margin-bottom: 4px;">Sản phẩm sắp hết hàng</h2>
                    <p>Các sản phẩm có tồn kho thấp để bạn ưu tiên nhập thêm hoặc phân phối lại.</p>
                </div>
            </div>

            <div class="list-stack">
                @forelse($lowStockProducts as $product)
                    <div class="mini-card">
                        <strong>{{ $product->name }}</strong>
                        <div class="muted">SKU: {{ $product->sku }}</div>
                        <div class="muted">Tồn kho còn lại: {{ $product->stock }} sản phẩm</div>
                    </div>
                @empty
                    <div class="empty-state">Hiện chưa có sản phẩm nào chạm ngưỡng tồn kho thấp.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
