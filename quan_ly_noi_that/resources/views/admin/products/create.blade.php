@extends('layouts.admin')

@section('title', 'Thêm sản phẩm')

@section('content')
    <div class="page-header">
        <div>
            <h2>Thêm sản phẩm mới</h2>
            <p>Cập nhật danh mục, giá bán, tồn kho và thông tin nhận diện để sản phẩm mới sẵn sàng cho quy trình bán hàng.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.products.store') }}" class="form-grid">
            @csrf

            <div class="field">
                <label for="name">Tên sản phẩm</label>
                <input id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Sofa gỗ sồi Bắc Âu">
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="category_id">Danh mục</label>
                <select id="category_id" name="category_id">
                    <option value="">Chọn danh mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="sku">Mã SKU</label>
                <input id="sku" name="sku" value="{{ old('sku') }}" placeholder="Ví dụ: SF-003">
                @error('sku')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="price">Giá bán</label>
                <input id="price" name="price" type="number" min="0" step="1000" value="{{ old('price') }}" placeholder="Ví dụ: 15000000">
                @error('price')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="stock">Tồn kho khởi tạo</label>
                <input id="stock" name="stock" type="number" min="0" value="{{ old('stock') }}" placeholder="Ví dụ: 12">
                @error('stock')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="material">Chất liệu</label>
                <input id="material" name="material" value="{{ old('material') }}" placeholder="Ví dụ: Gỗ sồi">
            </div>

            <div class="field">
                <label for="color">Màu sắc</label>
                <input id="color" name="color" value="{{ old('color') }}" placeholder="Ví dụ: Nâu sáng">
            </div>

            <div class="field">
                <label>Trạng thái</label>
                <label class="checkbox-row">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <span>Cho phép hiển thị sản phẩm trong hệ thống</span>
                </label>
            </div>

            <div class="field full">
                <label for="description">Mô tả sản phẩm</label>
                <textarea id="description" name="description" placeholder="Nhập mô tả ngắn, tính năng hoặc phong cách sản phẩm">{{ old('description') }}</textarea>
            </div>

            <div class="full">
                <button type="submit" class="primary-button">Lưu sản phẩm</button>
            </div>
        </form>
    </div>
@endsection
