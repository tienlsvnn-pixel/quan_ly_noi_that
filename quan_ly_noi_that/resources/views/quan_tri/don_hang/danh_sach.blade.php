@extends('layouts.quan_tri')

@section('title', 'Đơn hàng')

@section('content')
    @php
        $hasFilters = filled($filters['q']) || filled($filters['status']) || filled($filters['stock']) || filled($filters['from']) || filled($filters['to']);
    @endphp

    <div class="page-header">
        <div>
            <h2>Quản lý đơn hàng</h2>
            <p>Theo dõi tiến độ xử lý, đối soát tồn kho và lọc nhanh các đơn đang cần ưu tiên trong ngày.</p>
        </div>
        <a href="{{ route('admin.orders.create') }}" class="primary-button">Tạo đơn hàng</a>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <span class="metric-label">Tổng đơn phù hợp</span>
            <strong class="metric-value">{{ number_format($overview['total_orders']) }}</strong>
            <span class="metric-note">Số đơn sau khi áp dụng bộ lọc hiện tại.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Tổng giá trị</span>
            <strong class="metric-value">{{ number_format($overview['total_amount'], 0, ',', '.') }}đ</strong>
            <span class="metric-note">Doanh số gộp của các đơn đang hiển thị.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Hoàn thành</span>
            <strong class="metric-value">{{ number_format($overview['completed_orders']) }}</strong>
            <span class="metric-note">Những đơn đã chốt giao và cập nhật kết quả.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Đang xử lý</span>
            <strong class="metric-value">{{ number_format($overview['processing_orders']) }}</strong>
            <span class="metric-note">Nhóm đơn cần theo dõi tiến độ tiếp theo.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Chưa trừ kho</span>
            <strong class="metric-value">{{ number_format($overview['pending_stock_orders']) }}</strong>
            <span class="metric-note">Các đơn chưa đồng bộ tồn kho tự động.</span>
        </div>
    </div>

    <div class="page-box">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="search-form">
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm theo mã đơn hoặc tên khách hàng">

            <select name="status">
                <option value="">Tất cả trạng thái</option>
                @foreach(\App\Models\DonHang::STATUSES as $status)
                    <option value="{{ $status }}" {{ $filters['status'] === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>

            <select name="stock">
                <option value="">Tất cả kho</option>
                <option value="applied" {{ $filters['stock'] === 'applied' ? 'selected' : '' }}>Đã trừ kho</option>
                <option value="pending" {{ $filters['stock'] === 'pending' ? 'selected' : '' }}>Chưa trừ kho</option>
            </select>

            <input type="date" name="from" value="{{ $filters['from'] }}" aria-label="Từ ngày">
            <input type="date" name="to" value="{{ $filters['to'] }}" aria-label="Đến ngày">

            <div class="action-group">
                <button type="submit" class="secondary-button">Lọc dữ liệu</button>
                @if($hasFilters)
                    <a href="{{ route('admin.orders.index') }}" class="secondary-button">Đặt lại</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-summary">
            <span>Đang xem {{ $orders->count() }} đơn trên trang này.</span>
            <strong>{{ number_format($orders->total()) }} kết quả phù hợp</strong>
        </div>

        <div class="table-scroll">
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
                        @php
                            $statusClass = match ($order->status) {
                                \App\Models\DonHang::STATUS_COMPLETED => 'badge-success',
                                \App\Models\DonHang::STATUS_PROCESSING => 'badge-warning',
                                default => 'badge-primary',
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $order->code }}</strong>
                                <div class="table-subtle">{{ $order->items->count() }} dòng sản phẩm</div>
                            </td>
                            <td>
                                {{ $order->customer->name }}
                                <div class="table-subtle">{{ $order->customer->phone ?: 'Chưa có số điện thoại' }}</div>
                            </td>
                            <td>{{ $order->order_date->format('d/m/Y') }}</td>
                            <td>{{ $order->items->sum('quantity') }} sản phẩm</td>
                            <td>{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                            <td>
                                <span class="badge {{ $order->stock_applied ? 'badge-success' : 'badge-warning' }}">
                                    {{ $order->stock_applied ? 'Đã trừ kho' : 'Chưa trừ kho' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $statusClass }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="secondary-button">Chi tiết</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    {{ $hasFilters ? 'Không tìm thấy đơn hàng phù hợp với bộ lọc hiện tại.' : 'Chưa có đơn hàng nào trong hệ thống.' }}
                                    @if($hasFilters)
                                        <div class="action-group" style="justify-content: center; margin-top: 12px;">
                                            <a href="{{ route('admin.orders.index') }}" class="secondary-button">Xóa bộ lọc</a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $orders->links() }}</div>
    </div>
@endsection
