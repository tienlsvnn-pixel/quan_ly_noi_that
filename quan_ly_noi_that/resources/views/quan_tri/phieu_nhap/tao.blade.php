@extends('layouts.quan_tri')

@section('title', 'Tạo phiếu nhập hàng')

@section('content')
    <style>
        .receipt-layout {
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
            grid-template-columns: 1.15fr 0.45fr 0.55fr auto;
            gap: 12px;
            align-items: end;
            padding: 16px;
            border-radius: 18px;
            background: #fffdfb;
            border: 1px solid #efe7df;
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

        @media (max-width: 1000px) {
            .receipt-layout,
            .item-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="page-header">
        <div>
            <h2>Tạo phiếu nhập hàng</h2>
            <p>Chọn nhà cung cấp, thêm các dòng sản phẩm và giá nhập để hệ thống ghi nhận đầu vào hàng hóa thật sự.</p>
        </div>
        <a href="{{ route('admin.purchase-receipts.index') }}" class="secondary-button">Quay lại danh sách</a>
    </div>

    <form method="POST" action="{{ route('admin.purchase-receipts.store') }}" id="purchaseReceiptForm">
        @csrf

        <div class="receipt-layout">
            <div class="card-grid">
                <div class="form-card">
                    <div class="page-header" style="margin-bottom: 14px;">
                        <div>
                            <h2 style="font-size: 22px; margin-bottom: 4px;">Thông tin phiếu nhập</h2>
                            <p>Thiết lập nhà cung cấp, ngày nhận hàng và trạng thái xử lý ban đầu.</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field">
                            <label for="supplier_id">Nhà cung cấp</label>
                            <select id="supplier_id" name="supplier_id">
                                <option value="">Chọn nhà cung cấp</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="receipt_date">Ngày nhập</label>
                            <input id="receipt_date" type="date" name="receipt_date" value="{{ old('receipt_date', now()->toDateString()) }}">
                        </div>

                        <div class="field">
                            <label for="status">Trạng thái</label>
                            <select id="status" name="status">
                                @foreach(\App\Models\PhieuNhap::STATUSES as $status)
                                    <option value="{{ $status }}" {{ old('status', \App\Models\PhieuNhap::STATUS_DRAFT) === $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field full">
                            <label for="note">Ghi chú</label>
                            <textarea id="note" name="note">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="page-header" style="margin-bottom: 14px;">
                        <div>
                            <h2 style="font-size: 22px; margin-bottom: 4px;">Sản phẩm nhập</h2>
                            <p>Thêm nhiều dòng hàng, số lượng và giá nhập tương ứng cho từng SKU.</p>
                        </div>
                        <button type="button" class="secondary-button" id="addReceiptItemButton">Thêm dòng hàng</button>
                    </div>

                    @error('items')
                        <div class="field-error" style="margin-bottom: 12px;">{{ $message }}</div>
                    @enderror

                    <div class="item-stack" id="receiptItems">
                        @php
                            $oldItems = old('items', [['product_id' => '', 'quantity' => 1, 'unit_cost' => 0]]);
                        @endphp

                        @foreach($oldItems as $index => $item)
                            <div class="item-row" data-receipt-row>
                                <div class="field">
                                    <label>Sản phẩm</label>
                                    <select name="items[{{ $index }}][product_id]" data-product-select>
                                        <option value="">Chọn sản phẩm</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" {{ (string) ($item['product_id'] ?? '') === (string) $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="field">
                                    <label>Số lượng</label>
                                    <input type="number" min="1" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" data-quantity-input>
                                </div>

                                <div class="field">
                                    <label>Giá nhập</label>
                                    <input type="number" min="0" step="1000" name="items[{{ $index }}][unit_cost]" value="{{ $item['unit_cost'] ?? 0 }}" data-cost-input>
                                </div>

                                <button type="button" class="danger-button" data-remove-item>Xóa</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card-grid">
                <div class="summary-card">
                    <h3>Tóm tắt phiếu nhập</h3>
                    <div class="summary-stat">
                        <span>Số dòng hàng</span>
                        <strong id="receiptLineCount">0</strong>
                    </div>
                    <div class="summary-stat">
                        <span>Tổng số lượng</span>
                        <strong id="receiptQuantityCount">0</strong>
                    </div>
                    <div class="summary-stat">
                        <span>Tổng giá trị nhập</span>
                        <strong id="receiptTotalAmount">0đ</strong>
                    </div>
                </div>

                <button type="submit" class="primary-button">Lưu phiếu nhập</button>
            </div>
        </div>
    </form>

    <template id="receiptItemTemplate">
        <div class="item-row" data-receipt-row>
            <div class="field">
                <label>Sản phẩm</label>
                <select data-product-select>
                    <option value="">Chọn sản phẩm</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Số lượng</label>
                <input type="number" min="1" value="1" data-quantity-input>
            </div>

            <div class="field">
                <label>Giá nhập</label>
                <input type="number" min="0" step="1000" value="0" data-cost-input>
            </div>

            <button type="button" class="danger-button" data-remove-item>Xóa</button>
        </div>
    </template>

    <script>
        (function () {
            const itemContainer = document.getElementById('receiptItems');
            const addItemButton = document.getElementById('addReceiptItemButton');
            const template = document.getElementById('receiptItemTemplate');
            const lineCount = document.getElementById('receiptLineCount');
            const quantityCount = document.getElementById('receiptQuantityCount');
            const totalAmount = document.getElementById('receiptTotalAmount');

            function formatCurrency(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
            }

            function renameInputs() {
                Array.from(itemContainer.querySelectorAll('[data-receipt-row]')).forEach((row, index) => {
                    row.querySelector('[data-product-select]').name = `items[${index}][product_id]`;
                    row.querySelector('[data-quantity-input]').name = `items[${index}][quantity]`;
                    row.querySelector('[data-cost-input]').name = `items[${index}][unit_cost]`;
                });
            }

            function updateSummary() {
                let lines = 0;
                let quantity = 0;
                let amount = 0;

                Array.from(itemContainer.querySelectorAll('[data-receipt-row]')).forEach((row) => {
                    const select = row.querySelector('[data-product-select]');
                    const qty = Number(row.querySelector('[data-quantity-input]').value || 0);
                    const cost = Number(row.querySelector('[data-cost-input]').value || 0);

                    if (select.value) {
                        lines += 1;
                        quantity += qty;
                        amount += qty * cost;
                    }
                });

                lineCount.textContent = String(lines);
                quantityCount.textContent = String(quantity);
                totalAmount.textContent = formatCurrency(amount);
            }

            function bindRowEvents(row) {
                row.querySelector('[data-product-select]').addEventListener('change', updateSummary);
                row.querySelector('[data-quantity-input]').addEventListener('input', updateSummary);
                row.querySelector('[data-cost-input]').addEventListener('input', updateSummary);
                row.querySelector('[data-remove-item]').addEventListener('click', function () {
                    if (itemContainer.querySelectorAll('[data-receipt-row]').length === 1) {
                        row.querySelector('[data-product-select]').value = '';
                        row.querySelector('[data-quantity-input]').value = 1;
                        row.querySelector('[data-cost-input]').value = 0;
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

            Array.from(itemContainer.querySelectorAll('[data-receipt-row]')).forEach(bindRowEvents);
            renameInputs();
            updateSummary();
        })();
    </script>
@endsection
