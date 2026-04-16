@extends('layouts.quan_tri')

@section('title', 'Thêm khách hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Thêm khách hàng mới</h2>
            <p>Tạo hồ sơ khách hàng để quản lý đơn hàng, địa chỉ giao hàng và nhu cầu mua sắm nội thất một cách bài bản hơn.</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.customers.store') }}" class="form-grid">
            @csrf

            <div class="field">
                <label for="name">Tên khách hàng</label>
                <input id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Nguyễn Văn An">
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="phone">Số điện thoại</label>
                <input id="phone" name="phone" value="{{ old('phone') }}" placeholder="Ví dụ: 0901234567">
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" value="{{ old('email') }}" placeholder="Ví dụ: khachhang@example.com">
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="city">Khu vực</label>
                <input id="city" name="city" value="{{ old('city') }}" placeholder="Ví dụ: TP. Hồ Chí Minh">
            </div>

            <div class="field full">
                <label for="address">Địa chỉ</label>
                <textarea id="address" name="address" placeholder="Nhập địa chỉ giao hàng hoặc địa chỉ liên hệ">{{ old('address') }}</textarea>
            </div>

            <div class="field full">
                <label for="note">Ghi chú</label>
                <textarea id="note" name="note" placeholder="Ghi chú về nhu cầu, phong cách ưa thích hoặc lịch sử chăm sóc">{{ old('note') }}</textarea>
            </div>

            <div class="full">
                <button type="submit" class="primary-button">Lưu khách hàng</button>
            </div>
        </form>
    </div>
@endsection
