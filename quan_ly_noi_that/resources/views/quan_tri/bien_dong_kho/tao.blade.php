@extends('layouts.quan_tri')

@section('title', 'Tạo phiếu kho')

@section('content')
    <div class="page-header">
        <div>
            <h2>Tạo phiếu nhập hoặc xuất kho</h2>
            <p>Ghi nhận thay đổi tồn kho thực tế để hệ thống luôn phản ánh đúng số lượng hàng còn lại sau mỗi lần vận hành.</p>
        </div>
        <a href="{{ route('admin.stock-movements.index') }}" class="secondary-button">Quay lại lịch sử</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.stock-movements.store') }}" class="form-grid">
            @csrf

            <div class="field">
                <label for="product_id">Sản phẩm</label>
                <select id="product_id" name="product_id">
                    <option value="">Chọn sản phẩm</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} - tồn hiện tại {{ $product->stock }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="type">Loại phiếu</label>
                <select id="type" name="type">
                    @foreach(['Nhập kho', 'Xuất kho'] as $type)
                        <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="quantity">Số lượng</label>
                <input id="quantity" type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}">
                @error('quantity')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="movement_date">Ngày thực hiện</label>
                <input id="movement_date" type="date" name="movement_date" value="{{ old('movement_date', now()->toDateString()) }}">
            </div>

            <div class="field full">
                <label for="reference_code">Mã tham chiếu</label>
                <input id="reference_code" name="reference_code" value="{{ old('reference_code') }}" placeholder="Ví dụ: NK-001 hoặc PX-001">
            </div>

            <div class="field full">
                <label for="note">Ghi chú</label>
                <textarea id="note" name="note" placeholder="Ghi chú về nguồn hàng, lý do xuất kho hoặc tình trạng sản phẩm">{{ old('note') }}</textarea>
            </div>

            <div class="full">
                <button type="submit" class="primary-button">Lưu phiếu kho</button>
            </div>
        </form>
    </div>
@endsection
