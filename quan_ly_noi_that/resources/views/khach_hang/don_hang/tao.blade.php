@extends('layouts.khach_hang')

@section('title', 'Đặt hàng mới')

@section('content')
    <style>
        .order-layout {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 14px;
        }

        .item-stack { display: grid; gap: 12px; }

        .item-row {
            border: 1px solid #ece4da;
            border-radius: 16px;
            padding: 12px;
            background: #fffdfb;
            display: grid;
            grid-template-columns: 1.3fr 0.5fr auto;
            gap: 10px;
            align-items: end;
        }

        .field { display: grid; gap: 6px; }
        label { font-size: 14px; font-weight: 600; }

        input, select, textarea {
            min-height: 40px;
            border: 1px solid #ddd6cb;
            border-radius: 12px;
            padding: 10px 12px;
            background: #fcfbf8;
            width: 100%;
        }

        textarea { min-height: 100px; resize: vertical; }

        .summary {
            border: 1px solid #ece4da;
            border-radius: 18px;
            padding: 16px;
            background: #fffdfb;
            display: grid;
            gap: 10px;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #efe8df;
        }

        .summary-line:last-child { border-bottom: none; padding-bottom: 0; }

        @media (max-width: 980px) {
            .order-layout,
            .item-row { grid-template-columns: 1fr; }
        }
    </style>

    <form method="POST" action="{{ route('customer.orders.store') }}" id="orderForm">
        @csrf

        <div class="order-layout">
            <section class="card">
                <div class="page-header">
                    <div>
                        <h2>Đặt hàng mới</h2>
                        <p>Chọn sản phẩm, số lượng và gửi yêu cầu đặt hàng tới quản trị viên.</p>
                    </div>
                    <button type="button" class="button button-soft" id="addItemButton">Thêm dòng</button>
                </div>

                <div class="field" style="margin-bottom:12px;">
                    <label for="note">Ghi chú đơn hàng</label>
                    <textarea id="note" name="note" placeholder="Ví dụ: cần giao trong giờ hành chính, yêu cầu màu sắc...">{{ old('note') }}</textarea>
                </div>

                <div class="item-stack" id="orderItems">
                    @php
                        $initialSanPhamId = $preferredSanPhamId ?: '';
                        $oldItems = old('items', [['product_id' => $initialSanPhamId, 'quantity' => 1]]);
                    @endphp

                    @foreach($oldItems as $index => $item)
                        <div class="item-row" data-item-row>
                            <div class="field">
                                <label>Sản phẩm</label>
                                <select name="items[{{ $index }}][product_id]" data-product-select>
                                    <option value="">Chọn sản phẩm</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ (string) ($item['product_id'] ?? '') === (string) $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }}đ
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Số lượng</label>
                                <input type="number" min="1" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" data-quantity-input>
                            </div>
                            <button type="button" class="button button-soft" data-remove-item>Xóa</button>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="summary">
                <h3 style="margin:0;">Tóm tắt đơn hàng</h3>
                <div class="summary-line">
                    <span>Số dòng sản phẩm</span>
                    <strong id="summaryLines">0</strong>
                </div>
                <div class="summary-line">
                    <span>Tổng số lượng</span>
                    <strong id="summaryQty">0</strong>
                </div>
                <div class="summary-line">
                    <span>Tạm tính</span>
                    <strong id="summaryAmount">0đ</strong>
                </div>
                <button type="submit" class="button button-primary">Gửi đơn hàng</button>
            </section>
        </div>
    </form>

    <template id="itemTemplate">
        <div class="item-row" data-item-row>
            <div class="field">
                <label>Sản phẩm</label>
                <select data-product-select>
                    <option value="">Chọn sản phẩm</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                            {{ $product->name }} - {{ number_format($product->price, 0, ',', '.') }}đ
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Số lượng</label>
                <input type="number" min="1" value="1" data-quantity-input>
            </div>
            <button type="button" class="button button-soft" data-remove-item>Xóa</button>
        </div>
    </template>

    <script>
        (function () {
            const itemContainer = document.getElementById('orderItems');
            const addItemButton = document.getElementById('addItemButton');
            const template = document.getElementById('itemTemplate');
            const summaryLines = document.getElementById('summaryLines');
            const summaryQty = document.getElementById('summaryQty');
            const summaryAmount = document.getElementById('summaryAmount');

            function formatCurrency(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
            }

            function renameInputs() {
                Array.from(itemContainer.querySelectorAll('[data-item-row]')).forEach((row, index) => {
                    row.querySelector('[data-product-select]').name = `items[${index}][product_id]`;
                    row.querySelector('[data-quantity-input]').name = `items[${index}][quantity]`;
                });
            }

            function updateSummary() {
                let lines = 0;
                let qty = 0;
                let amount = 0;

                Array.from(itemContainer.querySelectorAll('[data-item-row]')).forEach((row) => {
                    const select = row.querySelector('[data-product-select]');
                    const quantityInput = row.querySelector('[data-quantity-input]');
                    const selectedOption = select.options[select.selectedIndex];
                    const quantity = Number(quantityInput.value || 0);
                    const price = Number(selectedOption?.dataset?.price || 0);

                    if (select.value) {
                        lines += 1;
                        qty += quantity;
                        amount += quantity * price;
                    }
                });

                summaryLines.textContent = String(lines);
                summaryQty.textContent = String(qty);
                summaryAmount.textContent = formatCurrency(amount);
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
