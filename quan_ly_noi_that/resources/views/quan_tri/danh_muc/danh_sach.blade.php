@extends('layouts.quan_tri')

@section('title', 'Danh mục')

@section('content')
    <div class="page-header">
        <div>
            <h2>Quản lý danh mục</h2>
            <p>Tổ chức các nhóm nội thất như sofa, bàn ăn, giường ngủ và những nhánh sản phẩm khác để đội ngũ thao tác rõ ràng hơn.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="primary-button">Thêm danh mục</a>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="search-form">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên danh mục">
            <button type="submit" class="secondary-button">Tìm kiếm</button>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Số sản phẩm</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <strong>{{ $category->name }}</strong>
                            <div style="color:#6b7280; margin-top:6px;">{{ $category->description ?: 'Chưa có mô tả cho danh mục này.' }}</div>
                        </td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $category->is_active ? 'Đang hoạt động' : 'Tạm ẩn' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="secondary-button">Sửa</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="danger-button">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"><div class="empty-state">Chưa có danh mục nào. Bạn có thể tạo danh mục đầu tiên ngay bây giờ.</div></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $categories->links() }}</div>
    </div>
@endsection
