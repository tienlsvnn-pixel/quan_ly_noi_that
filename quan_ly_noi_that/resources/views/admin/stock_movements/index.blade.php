@extends('layouts.admin')

@section('title', 'Kho hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Lịch sử nhập xuất kho</h2>
            <p>Theo dõi toàn bộ thay đổi tồn kho của từng sản phẩm, từ nhập hàng đến xuất giao cho khách hoặc điều chỉnh vận hành.</p>
        </div>
        <a href="{{ route('admin.stock-movements.create') }}" class="primary-button">Tạo phiếu kho</a>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.stock-movements.index') }}" class="search-form">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo sản phẩm, SKU hoặc mã tham chiếu">
            <button type="submit" class="secondary-button">Tìm kiếm</button>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Sản phẩm</th>
                    <th>Loại phiếu</th>
                    <th>Số lượng</th>
                    <th>Tồn trước</th>
                    <th>Tồn sau</th>
                    <th>Tham chiếu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockMovements as $movement)
                    <tr>
                        <td>{{ $movement->movement_date->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $movement->product->name }}</strong>
                            <div style="color:#6b7280; margin-top:6px;">SKU: {{ $movement->product->sku }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $movement->type === 'Nhập kho' ? 'badge-success' : 'badge-warning' }}">
                                {{ $movement->type }}
                            </span>
                        </td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->stock_before }}</td>
                        <td>{{ $movement->stock_after }}</td>
                        <td>{{ $movement->reference_code ?: 'Không có' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7"><div class="empty-state">Chưa có phiếu kho nào được ghi nhận.</div></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $stockMovements->links() }}</div>
    </div>
@endsection
