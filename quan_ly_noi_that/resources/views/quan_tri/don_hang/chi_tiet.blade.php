@extends('layouts.quan_tri')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    @php
        $totalQuantity = $order->items->sum('quantity');
        $statusClass = match ($order->status) {
            \App\Models\DonHang::STATUS_COMPLETED => 'badge-success',
            \App\Models\DonHang::STATUS_PROCESSING => 'badge-warning',
            default => 'badge-primary',
        };
        $statusMessage = match ($order->status) {
            \App\Models\DonHang::STATUS_COMPLETED => 'Đơn hàng đã hoàn thành. Hệ thống đã sẵn sàng phản ánh kết quả giao dịch vào báo cáo doanh thu và tồn kho.',
            \App\Models\DonHang::STATUS_PROCESSING => 'Đơn đang được xử lý. Đây là thời điểm phù hợp để rà soát tiến độ giao hàng và xác nhận lại số lượng.',
            default => 'Đơn mới được tạo và chưa đi vào giai đoạn hoàn tất. Bạn có thể bổ sung ghi chú trước khi chuyển trạng thái.',
        };
        $stockMessage = $order->stock_applied
            ? 'Tồn kho đã được đồng bộ cho đơn này. Nếu đổi sang trạng thái khác ngoài “Hoàn thành”, hệ thống sẽ hoàn kho tự động.'
            : 'Tồn kho chưa bị trừ. Khi cập nhật sang trạng thái “Hoàn thành”, hệ thống sẽ tự động xuất kho theo số lượng trong đơn.';
    @endphp

    <div class="page-header">
        <div>
            <h2>Chi tiết đơn hàng {{ $order->code }}</h2>
            <p>Xem nhanh trạng thái xử lý, thông tin khách mua và ảnh hưởng tồn kho của từng dòng sản phẩm.</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="metric-grid">
        <div class="metric-card">
            <span class="metric-label">Tổng giá trị</span>
            <strong class="metric-value">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
            <span class="metric-note">Giá trị thanh toán của toàn bộ đơn hàng.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Số lượng sản phẩm</span>
            <strong class="metric-value">{{ number_format($totalQuantity) }}</strong>
            <span class="metric-note">{{ number_format($order->items->count()) }} dòng sản phẩm đang được ghi nhận.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Trạng thái hiện tại</span>
            <strong class="metric-value">{{ $order->status }}</strong>
            <span class="metric-note">Theo dõi để biết đơn đã sẵn sàng trừ kho hay chưa.</span>
        </div>
        <div class="metric-card">
            <span class="metric-label">Đồng bộ kho</span>
            <strong class="metric-value">{{ $order->stock_applied ? 'Đã xong' : 'Đang chờ' }}</strong>
            <span class="metric-note">{{ $order->stock_applied ? 'Kho đã phản ánh đúng số lượng bán.' : 'Kho sẽ cập nhật khi đơn hoàn thành.' }}</span>
        </div>
    </div>

    <div class="split-grid">
        <div class="form-card">
            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="form-grid">
                @csrf
                @method('PUT')

                <div class="field">
                    <label for="status">Trạng thái đơn hàng</label>
                    <select id="status" name="status">
                        @foreach(\App\Models\DonHang::STATUSES as $status)
                            <option value="{{ $status }}" {{ old('status', $order->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label>Tình trạng kho</label>
                    <div class="badge {{ $order->stock_applied ? 'badge-success' : 'badge-warning' }}">
                        {{ $order->stock_applied ? 'Đã đồng bộ tồn kho' : 'Chưa đồng bộ tồn kho' }}
                    </div>
                </div>

                <div class="field full">
                    <label for="note">Ghi chú</label>
                    <textarea id="note" name="note">{{ old('note', $order->note) }}</textarea>
                    @error('note')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="full">
                    <div class="context-note">{{ $stockMessage }}</div>
                </div>

                <div class="full">
                    <button type="submit" class="primary-button">Cập nhật đơn hàng</button>
                </div>
            </form>
        </div>

        <div class="stack-grid">
            <div class="info-card">
                <div class="badge-row">
                    <span class="badge {{ $statusClass }}">{{ $order->status }}</span>
                    <span class="badge {{ $order->stock_applied ? 'badge-success' : 'badge-warning' }}">
                        {{ $order->stock_applied ? 'Đã trừ kho' : 'Chưa trừ kho' }}
                    </span>
                </div>

                <div class="context-note" style="margin-top: 14px;">{{ $statusMessage }}</div>

                <div class="detail-grid" style="margin-top: 14px;">
                    <div class="detail-item">
                        <strong>Khách hàng</strong>
                        <div>{{ $order->customer->name }}</div>
                    </div>
                    <div class="detail-item">
                        <strong>Ngày đặt</strong>
                        <div>{{ $order->order_date->format('d/m/Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <strong>Số điện thoại</strong>
                        <div>{{ $order->customer->phone ?: 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="detail-item">
                        <strong>Email</strong>
                        <div>{{ $order->customer->email ?: 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="detail-item">
                        <strong>Tỉnh/Thành phố</strong>
                        <div>{{ $order->customer->city ?: 'Chưa cập nhật' }}</div>
                    </div>
                    <div class="detail-item">
                        <strong>Địa chỉ</strong>
                        <div>{{ $order->customer->address ?: 'Chưa cập nhật' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="page-header" style="margin-bottom: 14px;">
            <div>
                <h2 style="font-size: 22px; margin-bottom: 4px;">Sản phẩm trong đơn</h2>
                <p>So sánh số lượng bán, đơn giá và tồn hiện tại để xử lý các đơn hoàn thành an toàn hơn.</p>
            </div>
        </div>

        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Tồn hiện tại</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                            <td>
                                @if($item->product)
                                    <span class="badge {{ $item->product->stock <= 5 ? 'badge-warning' : 'badge-success' }}">
                                        {{ $item->product->stock }} còn lại
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
