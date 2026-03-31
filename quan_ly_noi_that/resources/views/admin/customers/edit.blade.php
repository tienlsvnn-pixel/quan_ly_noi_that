@extends('layouts.admin')

@section('title', 'Chỉnh sửa khách hàng')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chỉnh sửa khách hàng</h2>
            <p>Cập nhật thông tin liên hệ, địa chỉ và ghi chú để việc chăm sóc khách hàng chính xác hơn ở từng giai đoạn bán hàng.</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="form-grid">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Tên khách hàng</label>
                <input id="name" name="name" value="{{ old('name', $customer->name) }}">
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="phone">Số điện thoại</label>
                <input id="phone" name="phone" value="{{ old('phone', $customer->phone) }}">
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" value="{{ old('email', $customer->email) }}">
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="city">Khu vực</label>
                <input id="city" name="city" value="{{ old('city', $customer->city) }}">
            </div>

            <div class="field full">
                <label for="address">Địa chỉ</label>
                <textarea id="address" name="address">{{ old('address', $customer->address) }}</textarea>
            </div>

            <div class="field full">
                <label for="note">Ghi chú</label>
                <textarea id="note" name="note">{{ old('note', $customer->note) }}</textarea>
            </div>

            <div class="full">
                <button type="submit" class="primary-button">Lưu thay đổi</button>
            </div>
        </form>
    </div>
@endsection
