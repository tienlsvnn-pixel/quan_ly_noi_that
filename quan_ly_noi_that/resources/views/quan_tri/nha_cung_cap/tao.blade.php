@extends('layouts.quan_tri')

@section('title', 'Thêm nhà cung cấp')

@section('content')
    <div class="page-header">
        <div>
            <h2>Thêm nhà cung cấp mới</h2>
            <p>Tạo hồ sơ đối tác cung ứng để quản lý nguồn hàng, lịch sử nhập và đầu mối liên hệ thuận tiện hơn.</p>
        </div>
        <a href="{{ route('admin.suppliers.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.suppliers.store') }}" class="form-grid">
            @csrf

            <div class="field">
                <label for="name">Tên nhà cung cấp</label>
                <input id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Nội thất Hoàng Gia">
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="contact_person">Người liên hệ</label>
                <input id="contact_person" name="contact_person" value="{{ old('contact_person') }}" placeholder="Ví dụ: Trần Minh Phúc">
            </div>

            <div class="field">
                <label for="phone">Số điện thoại</label>
                <input id="phone" name="phone" value="{{ old('phone') }}">
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" value="{{ old('email') }}">
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="city">Khu vực</label>
                <input id="city" name="city" value="{{ old('city') }}">
            </div>

            <div class="field">
                <label>Trạng thái</label>
                <label class="checkbox-row">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <span>Đang hợp tác</span>
                </label>
            </div>

            <div class="field full">
                <label for="address">Địa chỉ</label>
                <textarea id="address" name="address">{{ old('address') }}</textarea>
            </div>

            <div class="field full">
                <label for="note">Ghi chú</label>
                <textarea id="note" name="note">{{ old('note') }}</textarea>
            </div>

            <div class="full">
                <button type="submit" class="primary-button">Lưu nhà cung cấp</button>
            </div>
        </form>
    </div>
@endsection
