@extends('layouts.admin')

@section('title', 'Chỉnh sửa sản phẩm')

@section('content')
    <div class="page-header">
        <div>
            <h2>Chỉnh sửa sản phẩm</h2>
            <p>Cập nhật thông tin nhận diện, giá bán, tồn kho và phân loại của sản phẩm nội thất để dữ liệu bán hàng luôn chính xác.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" class="form-grid">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="name">Tên sản phẩm</label>
                <input id="name" name="name" value="{{ old('name', $product->name) }}">
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="category_id">Danh mục</label>
                <select id="category_id" name="category_id">
                    <option value="">Chọn danh mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="sku">Mã SKU</label>
                <input id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                @error('sku')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="price">Giá bán</label>
                <input id="price" name="price" type="number" min="0" step="1000" value="{{ old('price', $product->price) }}">
            </div>

            <div class="field">
                <label for="material">Chất liệu</label>
                <input id="material" name="material" value="{{ old('material', $product->material) }}">
            </div>

            <div class="field">
                <label for="color">Màu sắc</label>
                <input id="color" name="color" value="{{ old('color', $product->color) }}">
            </div>

            <div class="field">
                <label>Trạng thái</label>
                <label class="checkbox-row">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <span>Hiển thị sản phẩm trong hệ thống</span>
                </label>
            </div>

            <div class="field full">
                <label>Tồn kho hiện tại</label>
                <input value="{{ $product->stock }}" readonly>
                <div style="color:#6b7280; font-size:13px;">
                    Điều chỉnh tồn kho tại
                    <a href="{{ route('admin.stock-movements.create') }}" style="color:#2f5d50; font-weight:600;">phiếu kho</a>
                    để giữ đúng lịch sử nhập xuất.
                </div>
            </div>

            <div class="field full">
                <label for="description">Mô tả sản phẩm</label>
                <textarea id="description" name="description">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="full">
                <button type="submit" class="primary-button">Lưu thay đổi</button>
            </div>
        </form>
    </div>
@endsection
