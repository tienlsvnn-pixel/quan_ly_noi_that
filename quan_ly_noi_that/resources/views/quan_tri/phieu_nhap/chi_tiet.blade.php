@extends('layouts.quan_tri')

@section('title', 'Chi tiết phiếu nhập hàng')

@section('content')
    @php
        $totalQuantity = $purchaseReceipt->items->sum('quantity');
        $statusMessage = $purchaseReceipt->isImported()
            ? 'Phiếu đã được nhập kho. Tồn kho và báo cáo nhập hàng đã được cập nhật theo số lượng thực tế.'
            : 'Phiếu đang ở trạng thái nháp. Bạn có thể rà lại giá nhập và thông tin nhà cung cấp trước khi cộng kho.';
        $stockMessage = $purchaseReceipt->stock_applied
            ? 'Kho đã cộng đủ số lượng theo phiếu này. Nếu trả trạng thái về “Nháp”, hệ thống sẽ tự động hoàn tác tồn kho.'
            : 'Kho chưa được cộng. Khi cập nhật sang “Đã nhập kho”, hệ thống sẽ cộng tồn dựa trên từng dòng nhập.';
    @endphp

    <div class="page-header">
        <div>
            <h2>Chi tiết phiếu nhập {{ $purchaseReceipt->code }}</h2>
            <p>Theo dõi giá trị đầu vào, trạng thái cộng kho và thông tin liên hệ nhà cung cấp ở cùng một màn hình.</p>
        </div>
        <a href="{{ route('admin.purchase-receipts.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <span class="metric-label">Tổng giá trị nhập</span>
            <strong class="metric-value">{{ number_format($purchaseReceipt->total_amount, 0, ',', '.') }}đ</strong>
            <span class="metric-note">Giá trị mua hàng của toàn bộ phiếu.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Số lượng nhập</span>
            <strong class="metric-value">{{ number_format($totalQuantity) }}</strong>
            <span class="metric-note">{{ number_format($purchaseReceipt->items->count()) }} dòng sản phẩm được ghi nhận.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Trạng thái hiện tại</span>
            <strong class="metric-value">{{ $purchaseReceipt->status }}</strong>
            <span class="metric-note">Theo dõi để biết phiếu đã đủ điều kiện cộng kho hay chưa.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Đồng bộ kho</span>
            <strong class="metric-value">{{ $purchaseReceipt->stock_applied ? 'Đã xong' : 'Đang chờ' }}</strong>
            <span class="metric-note">{{ $purchaseReceipt->stock_applied ? 'Kho đã phản ánh lượng nhập thực tế.' : 'Kho sẽ cập nhật khi phiếu được xác nhận.' }}</span>
        </div>
    </div>

    <div class="split-grid">
        <div class="form-card">
            <form method="POST" action="{{ route('admin.purchase-receipts.update', $purchaseReceipt) }}" class="form-grid">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="status">Trạng thái phiếu nhập</label>
                    <select id="status" name="status">
                        @foreach(\App\Models\PhieuNhap::STATUSES as $status)
                            <option value="{{ $status }}" {{ old('status', $purchaseReceipt->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
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
                    @error('note')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="full">
                    <div class="context-note">{{ $stockMessage }}</div>
                </div>

                <div class="full">
                    <button type="submit" class="primary-button">Cập nhật phiếu nhập</button>
                </div>
            </form>
        </div>

        <div class="stack-grid">
            <div class="info-card">
                <div class="badge-row">
                    <span class="badge {{ $purchaseReceipt->isImported() ? 'badge-success' : 'badge-primary' }}">
                        {{ $purchaseReceipt->status }}
                    </span>
                    <span class="badge {{ $purchaseReceipt->stock_applied ? 'badge-success' : 'badge-warning' }}">
                        {{ $purchaseReceipt->stock_applied ? 'Đã cộng kho' : 'Chưa cộng kho' }}
                    </span>
                </div>

                <div class="context-note" style="margin-top: 14px;">{{ $statusMessage }}</div>

                <div class="detail-grid" style="margin-top: 14px;">
                    <div class="detail-item">
                        <strong>Nhà cung cấp</strong>
                        <div>{{ $purchaseReceipt->supplier->name }}</div>
                    </div>
                    <div class="detail-item">
                        <strong>Ngày nhập</strong>
                        <div>{{ $purchaseReceipt->receipt_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <strong>Người liên hệ</strong>
                        <div>{{ $purchaseReceipt->supplier->contact_person ?: 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="detail-item">
                        <strong>Số điện thoại</strong>
                        <div>{{ $purchaseReceipt->supplier->phone ?: 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="detail-item">
                        <strong>Email</strong>
                        <div>{{ $purchaseReceipt->supplier->email ?: 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="detail-item">
                        <strong>Địa chỉ</strong>
                        <div>{{ $purchaseReceipt->supplier->address ?: 'Chưa cập nhật' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="page-header" style="margin-bottom: 14px;">
            <div>
                <h2 style="font-size: 22px; margin-bottom: 4px;">Sản phẩm nhập</h2>
                <p>Kiểm tra giá nhập, số lượng và tồn hiện tại để tránh cộng kho trùng hoặc thiếu.</p>
            </div>
        </div>

        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá nhập</th>
                        <th>Tồn hiện tại</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseReceipt->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_cost, 0, ',', '.') }}đ</td>
                            <td>
                                @if($item->product)
                                    <span class="badge {{ $item->product->stock <= 5 ? 'badge-warning' : 'badge-success' }}">
                                        {{ $item->product->stock }} hiện có
                                    </span>
                                @else
                                    <span class="badge badge-danger">Không còn dữ liệu sản phẩm</span>
                                @endif
                            </td>
                            <td>{{ number_format($item->line_total, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
