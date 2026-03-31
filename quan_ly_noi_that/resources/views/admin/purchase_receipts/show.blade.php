@extends('layouts.admin')

@section('title', 'Chi tiết phiếu nhập hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chi tiết phiếu nhập {{ $purchaseReceipt->code }}</h2>
            <p>Xem nhà cung cấp, trạng thái nhập kho, danh sách sản phẩm và giá trị của từng phiếu nhập hàng.</p>
        </div>
        <a href="{{ route('admin.purchase-receipts.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="card-grid">
        <div class="form-card">
            <form method="POST" action="{{ route('admin.purchase-receipts.update', $purchaseReceipt) }}" class="form-grid">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="status">Trạng thái phiếu nhập</label>
                    <select id="status" name="status">
                        @foreach(\App\Models\PurchaseReceipt::STATUSES as $status)
                            <option value="{{ $status }}" {{ old('status', $purchaseReceipt->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Tình trạng kho</label>
                    <div class="badge {{ $purchaseReceipt->stock_applied ? 'badge-success' : 'badge-warning' }}">
                        {{ $purchaseReceipt->stock_applied ? 'Đã cộng tồn kho' : 'Chưa cộng tồn kho' }}
                    </div>
                </div>

                <div class="field full">
                    <label for="note">Ghi chú</label>
                    <textarea id="note" name="note">{{ old('note', $purchaseReceipt->note) }}</textarea>
                </div>

                <div class="full">
                    <button type="submit" class="primary-button">Cập nhật phiếu nhập</button>
                </div>
            </form>
        </div>

        <div class="info-card">
            <div class="detail-grid">
                <div class="detail-item">
                    <strong>Nhà cung cấp</strong>
                    <div>{{ $purchaseReceipt->supplier->name }}</div>
                </div>
                <div class="detail-item">
                    <strong>Ngày nhập</strong>
                    <div>{{ $purchaseReceipt->receipt_date->format('d/m/Y') }}</div>
                </div>
                <div class="detail-item">
                    <strong>Trạng thái</strong>
                    <div>{{ $purchaseReceipt->status }}</div>
                </div>
                <div class="detail-item">
                    <strong>Tổng giá trị</strong>
                    <div>{{ number_format($purchaseReceipt->total_amount, 0, ',', '.') }}đ</div>
                </div>
                <div class="detail-item">
                    <strong>Người liên hệ</strong>
                    <div>{{ $purchaseReceipt->supplier->contact_person ?: 'Chưa cập nhật' }}</div>
                </div>
                <div class="detail-item">
                    <strong>Số điện thoại</strong>
                    <div>{{ $purchaseReceipt->supplier->phone ?: 'Chưa cập nhật' }}</div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="page-header" style="margin-bottom: 14px;">
                <div>
                    <h2 style="font-size: 22px; margin-bottom: 4px;">Sản phẩm nhập</h2>
                    <p>Danh sách hàng hóa, số lượng và giá nhập của từng dòng trong phiếu.</p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá nhập</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseReceipt->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_cost, 0, ',', '.') }}đ</td>
                            <td>{{ number_format($item->line_total, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
