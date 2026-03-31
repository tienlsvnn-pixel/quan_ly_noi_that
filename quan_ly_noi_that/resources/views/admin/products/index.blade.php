@extends('layouts.admin')

@section('title', 'Sản phẩm')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý sản phẩm</h2>
            <p>Theo dõi mã SKU, giá bán, tồn kho, chất liệu và màu sắc của từng sản phẩm nội thất trong kho dữ liệu bán hàng.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="primary-button">Thêm sản phẩm</a>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.products.index') }}" class="search-form">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên sản phẩm hoặc SKU">
            <button type="submit" class="secondary-button">Tìm kiếm</button>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>SKU</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            <div style="color:#6b7280; margin-top:6px;">
                                {{ $product->material ?: 'Chưa rõ chất liệu' }}{{ $product->color ? ' • '.$product->color : '' }}
                            </div>
                        </td>
                        <td>{{ $product->category?->name ?: 'Chưa phân loại' }}</td>
                        <td>{{ $product->sku }}</td>
                        <td>{{ number_format($product->price, 0, ',', '.') }}đ</td>
                        <td>{{ $product->stock }}</td>
                        <td>
                            <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $product->is_active ? 'Đang bán' : 'Tạm ẩn' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.products.edit', $product) }}" class="secondary-button">Sửa</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger-button">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7"><div class="empty-state">Chưa có sản phẩm nào trong hệ thống.</div></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $products->links() }}</div>
    </div>
@endsection
