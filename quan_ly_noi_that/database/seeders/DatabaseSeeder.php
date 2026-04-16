<?php

namespace Database\Seeders;

use App\Models\DanhMuc;
use App\Models\KhachHang;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\SanPham;
use App\Models\PhieuNhap;
use App\Models\ChiTietPhieuNhap;
use App\Models\BienDongKho;
use App\Models\NhaCungCap;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            User::updateOrCreate(
                ['email' => 'admin@homestore.vn'],
                [
                    'name' => 'Quản trị viên',
                    'password' => Hash::make('Admin@123456'),
                    'role' => User::ROLE_ADMIN,
                ]
            );

            User::updateOrCreate(
                ['email' => 'test@example.com'],
                [
                    'name' => 'Khách hàng thử nghiệm',
                    'password' => Hash::make('KhachHang@123456'),
                    'role' => User::ROLE_CUSTOMER,
                ]
            );

            $customerUser = User::where('email', 'test@example.com')->first();

            if ($customerUser) {
                KhachHang::updateOrCreate(
                    ['user_id' => $customerUser->id],
                    [
                        'name' => $customerUser->name,
                        'email' => $customerUser->email,
                        'note' => 'Khách hàng mặc định dùng cho việc trải nghiệm hệ thống.',
                    ]
                );
            }

            $categoryData = [
                ['name' => 'Sofa phòng khách', 'description' => 'Các dòng sofa gỗ, sofa nỉ và sofa cao cấp cho phòng khách.'],
                ['name' => 'Bàn ăn', 'description' => 'Bàn ăn gia đình, bàn ăn mặt đá và bộ bàn ghế ăn hiện đại.'],
                ['name' => 'Giường ngủ', 'description' => 'Giường ngủ gỗ công nghiệp, gỗ tự nhiên và combo phòng ngủ.'],
                ['name' => 'Tủ quần áo', 'description' => 'Tủ cánh lùa, tủ 3 cánh, tủ âm tường và tủ hiện đại.'],
                ['name' => 'Bàn làm việc', 'description' => 'Bàn làm việc tại nhà, bàn giám đốc và kệ đi kèm.'],
            ];

            $categories = collect($categoryData)->map(function (array $item) {
                return DanhMuc::updateOrCreate(
                    ['slug' => Str::slug($item['name'])],
                    [
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'is_active' => true,
                    ]
                );
            })->keyBy('name');

            $productData = [
                ['category' => 'Sofa phòng khách', 'name' => 'Sofa gỗ sồi Bắc Âu', 'sku' => 'SF-001', 'price' => 18500000, 'stock' => 8, 'material' => 'Gỗ sồi', 'color' => 'Nâu sáng'],
                ['category' => 'Sofa phòng khách', 'name' => 'Sofa nỉ chữ L cao cấp', 'sku' => 'SF-002', 'price' => 22900000, 'stock' => 5, 'material' => 'Nỉ nhung', 'color' => 'Kem'],
                ['category' => 'Bàn ăn', 'name' => 'Bộ bàn ăn 6 ghế mặt đá', 'sku' => 'BA-001', 'price' => 16900000, 'stock' => 6, 'material' => 'Mặt đá sinter', 'color' => 'Trắng'],
                ['category' => 'Giường ngủ', 'name' => 'Giường ngủ gỗ óc chó 1m8', 'sku' => 'GN-001', 'price' => 24500000, 'stock' => 4, 'material' => 'Gỗ óc chó', 'color' => 'Nâu trầm'],
                ['category' => 'Tủ quần áo', 'name' => 'Tủ quần áo 3 cánh hiện đại', 'sku' => 'TQA-001', 'price' => 12800000, 'stock' => 9, 'material' => 'MDF chống ẩm', 'color' => 'Trắng vân gỗ'],
                ['category' => 'Bàn làm việc', 'name' => 'Bàn làm việc chân sắt tối giản', 'sku' => 'BLV-001', 'price' => 4200000, 'stock' => 14, 'material' => 'Gỗ MDF', 'color' => 'Nâu óc chó'],
            ];

            $products = collect($productData)->map(function (array $item) use ($categories) {
                return SanPham::updateOrCreate(
                    ['sku' => $item['sku']],
                    [
                        'category_id' => $categories[$item['category']]->id ?? null,
                        'name' => $item['name'],
                        'slug' => Str::slug($item['name'].'-'.$item['sku']),
                        'price' => $item['price'],
                        'stock' => $item['stock'],
                        'material' => $item['material'],
                        'color' => $item['color'],
                        'description' => 'Sản phẩm nội thất chất lượng cao, phù hợp không gian sống hiện đại.',
                        'is_active' => true,
                    ]
                );
            })->keyBy('sku');

            $customers = collect([
                ['name' => 'Nguyễn Văn An', 'email' => 'an.nguyen@example.com', 'phone' => '0901234567', 'city' => 'TP. Hồ Chí Minh'],
                ['name' => 'Trần Thị Bình', 'email' => 'binh.tran@example.com', 'phone' => '0912345678', 'city' => 'Hà Nội'],
                ['name' => 'Lê Minh Châu', 'email' => 'chau.le@example.com', 'phone' => '0987654321', 'city' => 'Đà Nẵng'],
            ])->map(function (array $item) {
                return KhachHang::updateOrCreate(
                    ['email' => $item['email']],
                    [
                        'name' => $item['name'],
                        'phone' => $item['phone'],
                        'city' => $item['city'],
                        'address' => 'Địa chỉ giao hàng mẫu cho khách hàng '.$item['name'],
                        'note' => 'Khách hàng tiềm năng quan tâm nội thất phong cách hiện đại.',
                    ]
                );
            })->values();

            $suppliers = collect([
                ['name' => 'Nội thất Hoàng Gia', 'contact_person' => 'Trần Minh Phúc', 'phone' => '0908111222', 'email' => 'phuc@hoanggia.vn', 'city' => 'TP. Hồ Chí Minh'],
                ['name' => 'Kho gỗ An Phát', 'contact_person' => 'Nguyễn Đức An', 'phone' => '0911444555', 'email' => 'an@anphat.vn', 'city' => 'Bình Dương'],
            ])->map(function (array $item) {
                return NhaCungCap::updateOrCreate(
                    ['email' => $item['email']],
                    [
                        'name' => $item['name'],
                        'contact_person' => $item['contact_person'],
                        'phone' => $item['phone'],
                        'city' => $item['city'],
                        'address' => 'Địa chỉ mẫu của nhà cung cấp '.$item['name'],
                        'note' => 'Nhà cung cấp quen thuộc cho nhóm hàng nội thất.',
                        'is_active' => true,
                    ]
                );
            })->values();

            $orders = [
                [
                    'code' => 'DH001',
                    'customer' => $customers[0],
                    'order_date' => now()->subDays(2)->toDateString(),
                    'status' => 'Hoàn thành',
                    'stock_applied' => true,
                    'items' => [
                        ['sku' => 'SF-001', 'quantity' => 1],
                    ],
                ],
                [
                    'code' => 'DH002',
                    'customer' => $customers[1],
                    'order_date' => now()->subDay()->toDateString(),
                    'status' => 'Đang xử lý',
                    'stock_applied' => false,
                    'items' => [
                        ['sku' => 'BA-001', 'quantity' => 1],
                        ['sku' => 'BLV-001', 'quantity' => 2],
                    ],
                ],
                [
                    'code' => 'DH003',
                    'customer' => $customers[2],
                    'order_date' => now()->toDateString(),
                    'status' => 'Mới',
                    'stock_applied' => false,
                    'items' => [
                        ['sku' => 'GN-001', 'quantity' => 1],
                        ['sku' => 'TQA-001', 'quantity' => 1],
                    ],
                ],
            ];

            foreach ($orders as $orderData) {
                $totalAmount = collect($orderData['items'])->sum(function (array $item) use ($products) {
                    $product = $products[$item['sku']];

                    return $product->price * $item['quantity'];
                });

                $order = DonHang::updateOrCreate(
                    ['code' => $orderData['code']],
                    [
                        'customer_id' => $orderData['customer']->id,
                        'order_date' => $orderData['order_date'],
                        'status' => $orderData['status'],
                        'stock_applied' => $orderData['stock_applied'],
                        'total_amount' => $totalAmount,
                        'note' => 'Đơn hàng được tạo từ dữ liệu mẫu của hệ thống.',
                    ]
                );

                $order->items()->delete();

                foreach ($orderData['items'] as $item) {
                    $product = $products[$item['sku']];

                    ChiTietDonHang::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'line_total' => $product->price * $item['quantity'],
                    ]);
                }
            }

            $purchaseReceipts = [
                [
                    'code' => 'PN001',
                    'supplier' => $suppliers[0],
                    'receipt_date' => now()->subDays(8)->toDateString(),
                    'status' => 'Đã nhập kho',
                    'stock_applied' => true,
                    'items' => [
                        ['sku' => 'SF-001', 'quantity' => 3, 'unit_cost' => 14200000],
                        ['sku' => 'BA-001', 'quantity' => 2, 'unit_cost' => 12100000],
                    ],
                ],
                [
                    'code' => 'PN002',
                    'supplier' => $suppliers[1],
                    'receipt_date' => now()->subDays(3)->toDateString(),
                    'status' => 'Nháp',
                    'stock_applied' => false,
                    'items' => [
                        ['sku' => 'BLV-001', 'quantity' => 5, 'unit_cost' => 2800000],
                    ],
                ],
            ];

            foreach ($purchaseReceipts as $receiptData) {
                $totalAmount = collect($receiptData['items'])->sum(fn (array $item) => $item['unit_cost'] * $item['quantity']);

                $receipt = PhieuNhap::updateOrCreate(
                    ['code' => $receiptData['code']],
                    [
                        'supplier_id' => $receiptData['supplier']->id,
                        'receipt_date' => $receiptData['receipt_date'],
                        'status' => $receiptData['status'],
                        'stock_applied' => $receiptData['stock_applied'],
                        'total_amount' => $totalAmount,
                        'note' => 'Phiếu nhập hàng mẫu của hệ thống.',
                    ]
                );

                $receipt->items()->delete();

                foreach ($receiptData['items'] as $item) {
                    $product = $products[$item['sku']];

                    ChiTietPhieuNhap::create([
                        'purchase_receipt_id' => $receipt->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $item['quantity'],
                        'unit_cost' => $item['unit_cost'],
                        'line_total' => $item['unit_cost'] * $item['quantity'],
                    ]);
                }
            }

            $stockMovementData = [
                ['sku' => 'SF-001', 'type' => 'Nhập kho', 'quantity' => 10, 'movement_date' => now()->subDays(10)->toDateString(), 'reference_code' => 'NK-001'],
                ['sku' => 'SF-001', 'type' => 'Xuất kho', 'quantity' => 2, 'movement_date' => now()->subDays(2)->toDateString(), 'reference_code' => 'PX-001'],
                ['sku' => 'BA-001', 'type' => 'Nhập kho', 'quantity' => 8, 'movement_date' => now()->subDays(7)->toDateString(), 'reference_code' => 'NK-002'],
                ['sku' => 'BLV-001', 'type' => 'Xuất kho', 'quantity' => 1, 'movement_date' => now()->subDay()->toDateString(), 'reference_code' => 'PX-002'],
                ['sku' => 'SF-001', 'type' => 'Nhập kho', 'quantity' => 3, 'movement_date' => now()->subDays(8)->toDateString(), 'reference_code' => 'PN001'],
                ['sku' => 'BA-001', 'type' => 'Nhập kho', 'quantity' => 2, 'movement_date' => now()->subDays(8)->toDateString(), 'reference_code' => 'PN001-BA'],
            ];

            foreach ($stockMovementData as $movement) {
                $product = $products[$movement['sku']];
                $existing = BienDongKho::where('reference_code', $movement['reference_code'])->first();

                if ($existing) {
                    continue;
                }

                $stockBefore = $movement['type'] === 'Nhập kho'
                    ? max(0, $product->stock - $movement['quantity'])
                    : $product->stock + $movement['quantity'];

                $stockAfter = $movement['type'] === 'Nhập kho'
                    ? $stockBefore + $movement['quantity']
                    : max(0, $stockBefore - $movement['quantity']);

                BienDongKho::create([
                    'product_id' => $product->id,
                    'type' => $movement['type'],
                    'quantity' => $movement['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'movement_date' => $movement['movement_date'],
                    'reference_code' => $movement['reference_code'],
                    'note' => 'Dữ liệu mẫu cho lịch sử nhập xuất kho.',
                ]);
            }
        });
    }
}
