@extends('layouts.quan_tri')

@section('title', 'Phiếu nhập hàng')

@section('content')
    @php
        $hasFilters = filled($filters['q']) || filled($filters['status']) || filled($filters['stock']) || filled($filters['from']) || filled($filters['to']);
    @endphp

    <div class="page-header">
        <div>
            <h2>Quản lý phiếu nhập hàng</h2>
            <p>Lọc nhanh các phiếu đã nhập kho, đang chờ cộng kho và giá trị mua hàng theo từng giai đoạn.</p>
        </div>
        <a href="{{ route('admin.purchase-receipts.create') }}" class="primary-button">Tạo phiếu nhập</a>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <span class="metric-label">Tổng phiếu phù hợp</span>
            <strong class="metric-value">{{ number_format($overview['total_receipts']) }}</strong>
            <span class="metric-note">Số phiếu khớp với bộ lọc hiện tại.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Tổng giá trị nhập</span>
            <strong class="metric-value">{{ number_format($overview['total_amount'], 0, ',', '.') }}đ</strong>
            <span class="metric-note">Chi phí đầu vào đang được theo dõi.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Đã nhập kho</span>
            <strong class="metric-value">{{ number_format($overview['imported_receipts']) }}</strong>
            <span class="metric-note">Những phiếu đã cộng tồn kho thành công.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Đang ở nháp</span>
            <strong class="metric-value">{{ number_format($overview['draft_receipts']) }}</strong>
            <span class="metric-note">Nhóm phiếu còn chờ xác nhận nhập kho.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Chưa cộng kho</span>
            <strong class="metric-value">{{ number_format($overview['pending_stock_receipts']) }}</strong>
            <span class="metric-note">Các phiếu chưa phản ánh vào tồn kho thực tế.</span>
        </div>
    </div>

    <div class="page-box">
        <form method="GET" action="{{ route('admin.purchase-receipts.index') }}" class="search-form">
            <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Tìm theo mã phiếu hoặc tên nhà cung cấp">

            <select name="status">
                <option value="">Tất cả trạng thái</option>
                @foreach(\App\Models\PhieuNhap::STATUSES as $status)
                    <option value="{{ $status }}" {{ $filters['status'] === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>

            <select name="stock">
                <option value="">Tất cả kho</option>
                <option value="applied" {{ $filters['stock'] === 'applied' ? 'selected' : '' }}>Đã cộng kho</option>
                <option value="pending" {{ $filters['stock'] === 'pending' ? 'selected' : '' }}>Chưa cộng kho</option>
            </select>

            <input type="date" name="from" value="{{ $filters['from'] }}" aria-label="Từ ngày">
            <input type="date" name="to" value="{{ $filters['to'] }}" aria-label="Đến ngày">

            <div class="action-group">
                <button type="submit" class="secondary-button">Lọc dữ liệu</button>
                @if($hasFilters)
                    <a href="{{ route('admin.purchase-receipts.index') }}" class="secondary-button">Đặt lại</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-summary">
            <span>Đang xem {{ $purchaseReceipts->count() }} phiếu trên trang này.</span>
            <strong>{{ number_format($purchaseReceipts->total()) }} kết quả phù hợp</strong>
        </div>

        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Mã phiếu</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày nhập</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Kho</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseReceipts as $receipt)
                        <tr>
                            <td>
                                <strong>{{ $receipt->code }}</strong>
                                <div class="table-subtle">{{ $receipt->items->count() }} dòng nhập</div>
                            </td>
                            <td>
                                {{ $receipt->supplier->name }}
                                <div class="table-subtle">{{ $receipt->supplier->phone ?: 'Chưa có số điện thoại' }}</div>
                            </td>
                            <td>{{ $receipt->receipt_date->format('d/m/Y') }}</td>
                            <td>{{ $receipt->items->sum('quantity') }} sản phẩm</td>
                            <td>{{ number_format($receipt->total_amount, 0, ',', '.') }}đ</td>
                            <td>
                                <span class="badge {{ $receipt->stock_applied ? 'badge-success' : 'badge-warning' }}">
                                    {{ $receipt->stock_applied ? 'Đã cộng kho' : 'Chưa cộng kho' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $receipt->status === \App\Models\PhieuNhap::STATUS_IMPORTED ? 'badge-success' : 'badge-primary' }}">
                                    {{ $receipt->status }}
                                </span>
                            </td>
                            <td><a href="{{ route('admin.purchase-receipts.show', $receipt) }}" class="secondary-button">Chi tiết</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    {{ $hasFilters ? 'Không tìm thấy phiếu nhập phù hợp với bộ lọc hiện tại.' : 'Chưa có phiếu nhập hàng nào trong hệ thống.' }}
                                    @if($hasFilters)
                                        <div class="action-group" style="justify-content: center; margin-top: 12px;">
                                            <a href="{{ route('admin.purchase-receipts.index') }}" class="secondary-button">Xóa bộ lọc</a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $purchaseReceipts->links() }}</div>
    </div>
@endsection
