@extends('layouts.admin')

@section('title', 'Chỉnh sửa danh mục')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chỉnh sửa danh mục</h2>
            <p>Cập nhật tên hiển thị, mô tả và trạng thái hoạt động của danh mục để dữ liệu luôn nhất quán và dễ hiểu.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="form-grid">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Tên danh mục</label>
                <input id="name" name="name" value="{{ old('name', $category->name) }}">
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label>Trạng thái</label>
                <label class="checkbox-row">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <span>Kích hoạt danh mục</span>
                </label>
            </div>

            <div class="field full">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="full">
                <button type="submit" class="primary-button">Lưu thay đổi</button>
            </div>
        </form>
    </div>
@endsection
