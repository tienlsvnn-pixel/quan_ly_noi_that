@extends('layouts.admin')

@section('title', 'Phiếu nhập hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý phiếu nhập hàng</h2>
            <p>Theo dõi nguồn hàng đầu vào, giá nhập, trạng thái nhập kho và giá trị từng phiếu để kiểm soát vận hành mua hàng tốt hơn.</p>
        </div>
        <a href="{{ route('admin.purchase-receipts.create') }}" class="primary-button">Tạo phiếu nhập</a>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.purchase-receipts.index') }}" class="search-form">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo mã phiếu hoặc tên nhà cung cấp">
            <button type="submit" class="secondary-button">Tìm kiếm</button>
        </form>
    </div>

    <div class="table-card">
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
                        <td><strong>{{ $receipt->code }}</strong></td>
                        <td>{{ $receipt->supplier->name }}</td>
                        <td>{{ $receipt->receipt_date->format('d/m/Y') }}</td>
                        <td>{{ $receipt->items->sum('quantity') }} sản phẩm</td>
                        <td>{{ number_format($receipt->total_amount, 0, ',', '.') }}đ</td>
                        <td>
                            <span class="badge {{ $receipt->stock_applied ? 'badge-success' : 'badge-warning' }}">
                                {{ $receipt->stock_applied ? 'Đã cộng kho' : 'Chưa cộng kho' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $receipt->status === \App\Models\PurchaseReceipt::STATUS_IMPORTED ? 'badge-success' : 'badge-primary' }}">
                                {{ $receipt->status }}
                            </span>
                        </td>
                        <td><a href="{{ route('admin.purchase-receipts.show', $receipt) }}" class="secondary-button">Chi tiết</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8"><div class="empty-state">Chưa có phiếu nhập hàng nào trong hệ thống.</div></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $purchaseReceipts->links() }}</div>
    </div>
@endsection
