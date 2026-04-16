@extends('layouts.quan_tri')

@section('title', 'Chỉnh sửa nhà cung cấp')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chỉnh sửa nhà cung cấp</h2>
            <p>Cập nhật thông tin đối tác, đầu mối liên hệ và trạng thái hợp tác để luồng nhập hàng luôn rõ ràng.</p>
        </div>
        <a href="{{ route('admin.suppliers.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" class="form-grid">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Tên nhà cung cấp</label>
                <input id="name" name="name" value="{{ old('name', $supplier->name) }}">
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="contact_person">Người liên hệ</label>
                <input id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}">
            </div>

            <div class="field">
                <label for="phone">Số điện thoại</label>
                <input id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}">
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" value="{{ old('email', $supplier->email) }}">
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="city">Khu vực</label>
                <input id="city" name="city" value="{{ old('city', $supplier->city) }}">
            </div>

            <div class="field">
                <label>Trạng thái</label>
                <label class="checkbox-row">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                    <span>Đang hợp tác</span>
                </label>
            </div>

            <div class="field full">
                <label for="address">Địa chỉ</label>
                <textarea id="address" name="address">{{ old('address', $supplier->address) }}</textarea>
            </div>

            <div class="field full">
                <label for="note">Ghi chú</label>
                <textarea id="note" name="note">{{ old('note', $supplier->note) }}</textarea>
            </div>

            <div class="full">
                <button type="submit" class="primary-button">Lưu thay đổi</button>
            </div>
        </form>
    </div>
@endsection
