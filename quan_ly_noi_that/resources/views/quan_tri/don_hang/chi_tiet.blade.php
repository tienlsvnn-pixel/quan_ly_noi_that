@extends('layouts.quan_tri')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chi tiết đơn hàng {{ $order->code }}</h2>
            <p>Xem toàn bộ thông tin khách hàng, trạng thái xử lý, tình trạng kho và danh sách sản phẩm trong đơn.</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="card-grid">
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
                </div>

                <div class="full">
                    <button type="submit" class="primary-button">Cập nhật đơn hàng</button>
                </div>
            </form>
        </div>

        <div class="info-card">
            <div class="detail-grid">
                <div class="detail-item">
                    <strong>Khách hàng</strong>
                    <div>{{ $order->customer->name }}</div>
                </div>
                <div class="detail-item">
                    <strong>Ngày đặt</strong>
                    <div>{{ $order->order_date->format('d/m/Y') }}</div>
                </div>
                <div class="detail-item">
                    <strong>Trạng thái</strong>
                    <div>{{ $order->status }}</div>
                </div>
                <div class="detail-item">
                    <strong>Tổng giá trị</strong>
                    <div>{{ number_format($order->total_amount, 0, ',', '.') }}đ</div>
                </div>
                <div class="detail-item">
                    <strong>Số điện thoại</strong>
                    <div>{{ $order->customer->phone ?: 'Chưa cập nhật' }}</div>
                </div>
                <div class="detail-item">
                    <strong>Địa chỉ</strong>
                    <div>{{ $order->customer->address ?: 'Chưa cập nhật' }}</div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="page-header" style="margin-bottom: 14px;">
                <div>
                    <h2 style="font-size: 22px; margin-bottom: 4px;">Sản phẩm trong đơn</h2>
                    <p>Danh sách hàng hóa, số lượng, đơn giá và thành tiền của từng dòng sản phẩm.</p>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                            <td>{{ number_format($item->line_total, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
