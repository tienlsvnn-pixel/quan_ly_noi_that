@extends('layouts.admin')

@section('title', 'Tạo đơn hàng')

@section('content')
    <style>
        .order-layout {
            display: grid;
            grid-template-columns: 1.25fr 0.75fr;
            gap: 20px;
        }

        .item-stack {
            display: grid;
            gap: 14px;
        }

        .item-row {
            display: grid;
            grid-template-columns: 1.4fr 0.55fr auto;
            gap: 12px;
            align-items: end;
            padding: 16px;
            border-radius: 18px;
            background: #fffdfb;
            border: 1px solid #efe7df;
        }

        .summary-panel {
            display: grid;
            gap: 14px;
        }

        .summary-card {
            padding: 20px;
            border-radius: 20px;
            background: linear-gradient(180deg, #fff7ed, #fffdf9);
            border: 1px solid rgba(245, 158, 11, 0.18);
        }

        .summary-card h3 {
            font-size: 18px;
            margin-bottom: 12px;
        }

        .summary-stat {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(120, 53, 15, 0.08);
        }

        .summary-stat:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .summary-label {
            color: #6b7280;
        }

        .summary-value {
            font-weight: 700;
        }

        @media (max-width: 1000px) {
            .order-layout,
            .item-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="page-header">
        <div>
            <h2>Tạo đơn hàng mới</h2>
            <p>Thêm nhiều dòng sản phẩm linh hoạt, xem tổng tiền theo thời gian thực và chốt đơn nhanh hơn ngay trong giao diện quản trị.</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <form method="POST" action="{{ route('admin.orders.store') }}" id="orderForm">
        @csrf

        <div class="order-layout">
            <div class="card-grid">
                <div class="form-card">
                    <div class="page-header" style="margin-bottom: 14px;">
                        <div>
                            <h2 style="font-size: 22px; margin-bottom: 4px;">Thông tin đơn hàng</h2>
                            <p>Thiết lập khách mua, ngày đặt và trạng thái xử lý ban đầu.</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field">
                            <label for="customer_id">Khách hàng</label>
                            <select id="customer_id" name="customer_id">
                                <option value="">Chọn khách hàng</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="order_date">Ngày đặt</label>
                            <input id="order_date" type="date" name="order_date" value="{{ old('order_date', now()->toDateString()) }}">
                        </div>

                        <div class="field">
                            <label for="status">Trạng thái</label>
                            <select id="status" name="status">
                                @foreach(\App\Models\Order::STATUSES as $status)
                                    <option value="{{ $status }}" {{ old('status', \App\Models\Order::STATUS_NEW) === $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field full">
                            <label for="note">Ghi chú</label>
                            <textarea id="note" name="note" placeholder="Ghi chú giao hàng, màu sắc mong muốn, thời gian lắp đặt...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="page-header" style="margin-bottom: 14px;">
                        <div>
                            <h2 style="font-size: 22px; margin-bottom: 4px;">Sản phẩm trong đơn</h2>
                            <p>Thêm hoặc xóa dòng sản phẩm tùy ý. Tổng tiền sẽ tự cập nhật theo từng lựa chọn.</p>
                        </div>
                        <button type="button" class="secondary-button" id="addItemButton">Thêm dòng sản phẩm</button>
                    </div>

                    @error('items')
                        <div class="field-error" style="margin-bottom: 12px;">{{ $message }}</div>
                    @enderror

                    <div class="item-stack" id="orderItems">
                        @php
                            $oldItems = old('items', [['product_id' => '', 'quantity' => 1]]);
                        @endphp

                        @foreach($oldItems as $index => $item)
                            <div class="item-row" data-item-row>
                                <div class="field">
                                    <label>Sản phẩm</label>
                                    <select name="items[{{ $index }}][product_id]" data-product-select>
                                        <option value="">Chọn sản phẩm</option>
                                        @foreach($products as $product)
                                            <option
                                                value="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}"
                                                data-stock="{{ $product->stock }}"
                                                {{ (string) ($item['product_id'] ?? '') === (string) $product->id ? 'selected' : '' }}
                                            >
                                                {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }}đ
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="field">
                                    <label>Số lượng</label>
                                    <input type="number" min="1" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" data-quantity-input>
                                </div>

                                <button type="button" class="danger-button" data-remove-item>Xóa</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="summary-panel">
                <div class="summary-card">
                    <h3>Tóm tắt đơn hàng</h3>
                    <div class="summary-stat">
                        <span class="summary-label">Số dòng sản phẩm</span>
                        <span class="summary-value" id="summaryLines">0</span>
                    </div>
                    <div class="summary-stat">
                        <span class="summary-label">Tổng số lượng</span>
                        <span class="summary-value" id="summaryQuantity">0</span>
                    </div>
                    <div class="summary-stat">
                        <span class="summary-label">Tạm tính</span>
                        <span class="summary-value" id="summaryTotal">0đ</span>
                    </div>
                </div>

                <div class="summary-card">
                    <h3>Mẹo thao tác</h3>
                    <div class="summary-stat">
                        <span class="summary-label">Chốt kho tự động</span>
                        <span class="summary-value">Khi đơn “Hoàn thành”</span>
                    </div>
                    <div class="summary-stat">
                        <span class="summary-label">Hoàn kho tự động</span>
                        <span class="summary-value">Khi đổi khỏi “Hoàn thành”</span>
                    </div>
                    <div class="summary-stat">
                        <span class="summary-label">Theo dõi lại</span>
                        <span class="summary-value">Tại mục Kho hàng</span>
                    </div>
                </div>

                <button type="submit" class="primary-button">Lưu đơn hàng</button>
            </div>
        </div>
    </form>

    <template id="orderItemTemplate">
        <div class="item-row" data-item-row>
            <div class="field">
                <label>Sản phẩm</label>
                <select data-product-select>
                    <option value="">Chọn sản phẩm</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                            {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }}đ
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Số lượng</label>
                <input type="number" min="1" value="1" data-quantity-input>
            </div>

            <button type="button" class="danger-button" data-remove-item>Xóa</button>
        </div>
    </template>

    <script>
        (function () {
            const itemContainer = document.getElementById('orderItems');
            const addItemButton = document.getElementById('addItemButton');
            const template = document.getElementById('orderItemTemplate');
            const summaryLines = document.getElementById('summaryLines');
            const summaryQuantity = document.getElementById('summaryQuantity');
            const summaryTotal = document.getElementById('summaryTotal');

            function formatCurrency(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
            }

            function renameInputs() {
                Array.from(itemContainer.querySelectorAll('[data-item-row]')).forEach((row, index) => {
                    const select = row.querySelector('[data-product-select]');
                    const quantity = row.querySelector('[data-quantity-input]');

                    select.name = `items[${index}][product_id]`;
                    quantity.name = `items[${index}][quantity]`;
                });
            }

            function updateSummary() {
                let lines = 0;
                let quantityTotal = 0;
                let amountTotal = 0;

                Array.from(itemContainer.querySelectorAll('[data-item-row]')).forEach((row) => {
                    const select = row.querySelector('[data-product-select]');
                    const quantityInput = row.querySelector('[data-quantity-input]');
                    const selectedOption = select.options[select.selectedIndex];
                    const quantity = Number(quantityInput.value || 0);
                    const price = Number(selectedOption?.dataset?.price || 0);

                    if (select.value) {
                        lines += 1;
                        quantityTotal += quantity;
                        amountTotal += quantity * price;
                    }
                });

                summaryLines.textContent = String(lines);
                summaryQuantity.textContent = String(quantityTotal);
                summaryTotal.textContent = formatCurrency(amountTotal);
            }

            function bindRowEvents(row) {
                row.querySelector('[data-product-select]').addEventListener('change', updateSummary);
                row.querySelector('[data-quantity-input]').addEventListener('input', updateSummary);
                row.querySelector('[data-remove-item]').addEventListener('click', function () {
                    if (itemContainer.querySelectorAll('[data-item-row]').length === 1) {
                        row.querySelector('[data-product-select]').value = '';
                        row.querySelector('[data-quantity-input]').value = 1;
                    } else {
                        row.remove();
                    }

                    renameInputs();
                    updateSummary();
                });
            }

            addItemButton.addEventListener('click', function () {
                const fragment = template.content.cloneNode(true);
                const row = fragment.querySelector('[data-item-row]');

                itemContainer.appendChild(fragment);
                bindRowEvents(itemContainer.lastElementChild);
                renameInputs();
                updateSummary();
            });

            Array.from(itemContainer.querySelectorAll('[data-item-row]')).forEach(bindRowEvents);
            renameInputs();
            updateSummary();
        })();
    </script>
@endsection
