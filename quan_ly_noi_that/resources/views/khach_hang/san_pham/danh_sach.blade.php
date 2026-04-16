@extends('layouts.khach_hang')

@section('title', 'Sản phẩm')

@section('content')
    <style>
        .toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .toolbar input {
            flex: 1 1 220px;
            min-height: 42px;
            border-radius: 12px;
            border: 1px solid #ddd6cb;
            padding: 10px 12px;
            background: #fcfbf8;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .product-card {
            border: 1px solid #ece4da;
            border-radius: 18px;
            padding: 16px;
            background: #fffdfb;
            display: grid;
            gap: 8px;
        }

        .meta {
            font-size: 14px;
            color: #6b7280;
        }

        @media (max-width: 1000px) {
            .product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 700px) {
            .product-grid { grid-template-columns: 1fr; }
        }
    </style>

    <section class="card">
        <div class="page-header">
            <div>
                <h2>Danh mục sản phẩm</h2>
                <p>Tìm sản phẩm nội thất phù hợp và đặt hàng trực tiếp ngay trên hệ thống.</p>
            </div>
            <a href="{{ route('customer.orders.create') }}" class="button button-primary">Đặt hàng ngay</a>
        </div>

        <form method="GET" action="{{ route('customer.products.index') }}" class="toolbar">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên, SKU, chất liệu hoặc màu sắc">
            <button type="submit" class="button button-soft">Tìm kiếm</button>
        </form>

        <div class="product-grid">
            @forelse($products as $product)
                <article class="product-card">
                    <strong>{{ $product->name }}</strong>
                    <div class="meta">SKU: {{ $product->sku }}</div>
                    <div class="meta">Chất liệu: {{ $product->material ?: 'Chưa cập nhật' }}</div>
                    <div class="meta">Màu sắc: {{ $product->color ?: 'Chưa cập nhật' }}</div>
                    <div style="font-size:20px; font-weight:700;">{{ number_format($product->price, 0, ',', '.') }}đ</div>
                    <div class="meta">Tồn kho hiện tại: {{ $product->stock }}</div>
                    <a href="{{ route('customer.orders.create', ['product_id' => $product->id]) }}" class="button button-primary">Chọn sản phẩm này</a>
                </article>
            @empty
                <div class="empty-state" style="grid-column: 1 / -1;">Không tìm thấy sản phẩm phù hợp.</div>
            @endforelse
        </div>

        <div style="margin-top:14px;">
            {{ $products->links() }}
        </div>
    </section>
@endsection
