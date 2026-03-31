@extends('layouts.admin')

@section('title', 'Thêm danh mục')

@section('content')
    <div class="page-header">
        <div>
            <h2>Thêm danh mục mới</h2>
            <p>Tạo nhóm sản phẩm mới để việc quản lý hàng hóa nội thất rõ ràng, dễ tìm và thuận tiện hơn cho đội ngũ vận hành.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="form-grid">
            @csrf

            <div class="field">
                <label for="name">Tên danh mục</label>
                <input id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Sofa phòng khách">
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label>Trạng thái</label>
                <label class="checkbox-row">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <span>Kích hoạt danh mục ngay sau khi tạo</span>
                </label>
            </div>

            <div class="field full">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" placeholder="Mô tả ngắn về nhóm sản phẩm này">{{ old('description') }}</textarea>
            </div>

            <div class="full">
                <button type="submit" class="primary-button">Lưu danh mục</button>
            </div>
        </form>
    </div>
@endsection
