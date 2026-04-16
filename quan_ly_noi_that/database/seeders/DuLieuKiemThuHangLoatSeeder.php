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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DuLieuKiemThuHangLoatSeeder extends Seeder
{
    private const MOVEMENT_IN = 'Nháº­p kho';
    private const MOVEMENT_OUT = 'Xuáº¥t kho';

    public function run(): void
    {
        $faker = fake('vi_VN');
        $batchCode = now()->format('ymdHis').Str::upper(Str::random(3));

        $this->command?->info('Seeding bulk test data...');

        $categories = $this->seedCategories();
        $products = $this->seedSanPhams($categories, $faker);
        $suppliers = $this->seedNhaCungCaps($faker);
        $customers = $this->seedKhachHangs($faker);

        $this->seedPhieuNhaps($products, $suppliers, $batchCode, (int) env('SEED_BULK_RECEIPTS', 220));
        $this->seedDonHangs($products, $customers, $batchCode, (int) env('SEED_BULK_ORDERS', 450));
        $this->seedManualBienDongKhos($products, $batchCode, (int) env('SEED_BULK_MOVEMENTS', 260));

        $this->command?->info('Bulk test data seeded successfully.');
    }

    private function seedCategories(): Collection
    {
        $baseCategories = [
            'Noi that phong khach',
            'Noi that phong ngu',
            'Noi that phong bep',
            'Ban an',
            'Ban tra',
            'Ghe sofa',
            'Ghe ban an',
            'Giuong ngu',
            'Tu quan ao',
            'Ke tivi',
            'Ban lam viec',
            'Ke sach',
            'Den trang tri',
            'Trang tri tuong',
            'Phu kien noi that',
            'Noi that thong minh',
            'Noi that van phong',
            'Noi that cao cap',
        ];

        foreach ($baseCategories as $index => $name) {
            DanhMuc::firstOrCreate(
                ['slug' => Str::slug($name).'-'.($index + 1)],
                [
                    'name' => $name,
                    'description' => 'Danh muc du lieu test so '.($index + 1),
                    'is_active' => true,
                ]
            );
        }

        return DanhMuc::query()->where('is_active', true)->get();
    }

    private function seedSanPhams(Collection $categories, \Faker\Generator $faker): Collection
    {
        $targetSanPhams = (int) env('SEED_BULK_PRODUCTS', 220);
        $existingTestSanPhams = SanPham::query()->where('sku', 'like', 'TST-SP-%')->count();
        $toCreate = max(0, $targetSanPhams - $existingTestSanPhams);

        $materials = ['Go tu nhien', 'Go cong nghiep', 'Kim loai', 'Niem cao su', 'Da cong nghiep', 'Vai ni'];
        $colors = ['Trang', 'Nau', 'Den', 'Xam', 'Kem', 'Vang nhat'];

        for ($i = 1; $i <= $toCreate; $i++) {
            $sequence = $existingTestSanPhams + $i;
            $sku = 'TST-SP-'.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
            $name = 'San pham test '.$sequence;
            $category = $categories->random();

            SanPham::create([
                'category_id' => $category->id,
                'name' => $name,
                'slug' => Str::slug($name.'-'.$sku),
                'sku' => $sku,
                'price' => random_int(900_000, 45_000_000),
                'stock' => random_int(25, 140),
                'material' => $faker->randomElement($materials),
                'color' => $faker->randomElement($colors),
                'description' => 'Du lieu san pham dung de test giao dien va nghiep vu.',
                'is_active' => random_int(1, 100) <= 92,
            ]);
        }

        return SanPham::query()->where('is_active', true)->get()->keyBy('id');
    }

    private function seedNhaCungCaps(\Faker\Generator $faker): Collection
    {
        $targetNhaCungCaps = (int) env('SEED_BULK_SUPPLIERS', 45);

        for ($i = 1; $i <= $targetNhaCungCaps; $i++) {
            $email = 'supplier.test'.str_pad((string) $i, 3, '0', STR_PAD_LEFT).'@homestore.local';

            NhaCungCap::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Nha cung cap test '.$i,
                    'contact_person' => 'Lien he '.$i,
                    'phone' => '09'.str_pad((string) random_int(10_000_000, 99_999_999), 8, '0', STR_PAD_LEFT),
                    'city' => $faker->randomElement(['Ha Noi', 'Da Nang', 'TP HCM', 'Can Tho', 'Hai Phong', 'Binh Duong']),
                    'address' => 'Dia chi test '.$i,
                    'note' => 'Nha cung cap du lieu test.',
                    'is_active' => random_int(1, 100) <= 90,
                ]
            );
        }

        return NhaCungCap::query()->where('is_active', true)->get();
    }

    private function seedKhachHangs(\Faker\Generator $faker): Collection
    {
        $targetUserKhachHangs = (int) env('SEED_BULK_CUSTOMER_USERS', 140);
        $targetWalkInKhachHangs = (int) env('SEED_BULK_CUSTOMERS', 90);

        for ($i = 1; $i <= $targetUserKhachHangs; $i++) {
            $email = 'customer.test'.str_pad((string) $i, 3, '0', STR_PAD_LEFT).'@homestore.local';
            $name = 'Khach Test '.str_pad((string) $i, 3, '0', STR_PAD_LEFT);

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('KhachHang@123456'),
                    'role' => User::ROLE_CUSTOMER,
                ]
            );

            KhachHang::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $name,
                    'email' => $email,
                    'phone' => '03'.str_pad((string) random_int(10_000_000, 99_999_999), 8, '0', STR_PAD_LEFT),
                    'city' => $faker->randomElement(['Ha Noi', 'Da Nang', 'TP HCM', 'Can Tho', 'Hai Phong', 'Nha Trang']),
                    'address' => 'Dia chi khach test '.$i,
                    'note' => 'Khach hang co tai khoan dang nhap.',
                ]
            );
        }

        for ($i = 1; $i <= $targetWalkInKhachHangs; $i++) {
            $email = 'walkin.test'.str_pad((string) $i, 3, '0', STR_PAD_LEFT).'@homestore.local';

            KhachHang::updateOrCreate(
                ['email' => $email],
                [
                    'user_id' => null,
                    'name' => 'Khach le '.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                    'phone' => '07'.str_pad((string) random_int(10_000_000, 99_999_999), 8, '0', STR_PAD_LEFT),
                    'city' => $faker->randomElement(['Ha Noi', 'Da Nang', 'TP HCM', 'Can Tho', 'Hai Phong', 'Hue']),
                    'address' => 'Dia chi khach le '.$i,
                    'note' => 'Khach hang du lieu test cho khu vuc admin.',
                ]
            );
        }

        return KhachHang::query()->get();
    }

    private function seedPhieuNhaps(Collection $products, Collection $suppliers, string $batchCode, int $count): void
    {
        if ($products->isEmpty() || $suppliers->isEmpty() || $count <= 0) {
            return;
        }

        for ($i = 1; $i <= $count; $i++) {
            $supplier = $suppliers->random();
            $receiptDate = now()->subDays(random_int(2, 260))->toDateString();
            $receiptCode = 'PNT'.$batchCode.str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            $itemCount = random_int(1, min(5, $products->count()));
            $selectedSanPhams = $products->shuffle()->take($itemCount)->values();
            $isImported = random_int(1, 100) <= 65;
            $items = [];
            $totalAmount = 0;

            foreach ($selectedSanPhams as $product) {
                $quantity = random_int(4, 28);
                $costRatio = random_int(55, 88) / 100;
                $unitCost = max(100_000, (int) round((float) $product->price * $costRatio, -2));
                $lineTotal = $unitCost * $quantity;

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'line_total' => $lineTotal,
                ];

                $totalAmount += $lineTotal;
            }

            $receipt = PhieuNhap::create([
                'supplier_id' => $supplier->id,
                'code' => $receiptCode,
                'receipt_date' => $receiptDate,
                'status' => $isImported ? PhieuNhap::STATUS_IMPORTED : PhieuNhap::STATUS_DRAFT,
                'stock_applied' => $isImported,
                'total_amount' => $totalAmount,
                'note' => 'Phieu nhap test generated by DuLieuKiemThuHangLoatSeeder.',
            ]);

            foreach ($items as $item) {
                /** @var SanPham $product */
                $product = $item['product'];

                ChiTietPhieuNhap::create([
                    'purchase_receipt_id' => $receipt->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'line_total' => $item['line_total'],
                ]);

                if (!$isImported) {
                    continue;
                }

                $before = (int) $product->stock;
                $after = $before + (int) $item['quantity'];

                $product->stock = $after;
                $product->save();
                $products->put($product->id, $product);

                BienDongKho::create([
                    'product_id' => $product->id,
                    'type' => self::MOVEMENT_IN,
                    'quantity' => $item['quantity'],
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'movement_date' => $receiptDate,
                    'reference_code' => $receiptCode,
                    'note' => 'Nhap kho tu du lieu test.',
                ]);
            }
        }
    }

    private function seedDonHangs(Collection $products, Collection $customers, string $batchCode, int $count): void
    {
        if ($products->isEmpty() || $customers->isEmpty() || $count <= 0) {
            return;
        }

        for ($i = 1; $i <= $count; $i++) {
            $customer = $customers->random();
            $orderDate = now()->subDays(random_int(0, 210))->toDateString();
            $orderCode = 'ODT'.$batchCode.str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            $itemCount = random_int(1, min(4, $products->count()));
            $selectedSanPhams = $products->shuffle()->take($itemCount)->values();
            $items = [];
            $totalAmount = 0;
            $candidateStatus = $this->pickDonHangStatus();

            foreach ($selectedSanPhams as $product) {
                $quantity = random_int(1, 6);
                $lineTotal = (float) $product->price * $quantity;

                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => (float) $product->price,
                    'line_total' => $lineTotal,
                ];

                $totalAmount += (int) $lineTotal;
            }

            $canApplyStock = $candidateStatus === DonHang::STATUS_COMPLETED
                && $this->canApplyDonHangStock($items);

            $status = $candidateStatus;

            if ($candidateStatus === DonHang::STATUS_COMPLETED && !$canApplyStock) {
                $status = DonHang::STATUS_PROCESSING;
            }

            $order = DonHang::create([
                'customer_id' => $customer->id,
                'code' => $orderCode,
                'order_date' => $orderDate,
                'status' => $status,
                'stock_applied' => $status === DonHang::STATUS_COMPLETED && $canApplyStock,
                'total_amount' => $totalAmount,
                'note' => 'Don hang test generated by DuLieuKiemThuHangLoatSeeder.',
            ]);

            foreach ($items as $item) {
                /** @var SanPham $product */
                $product = $item['product'];

                ChiTietDonHang::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                ]);

                if (!($status === DonHang::STATUS_COMPLETED && $canApplyStock)) {
                    continue;
                }

                $before = (int) $product->stock;
                $after = max(0, $before - (int) $item['quantity']);

                $product->stock = $after;
                $product->save();
                $products->put($product->id, $product);

                BienDongKho::create([
                    'product_id' => $product->id,
                    'type' => self::MOVEMENT_OUT,
                    'quantity' => $item['quantity'],
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'movement_date' => $orderDate,
                    'reference_code' => $orderCode,
                    'note' => 'Xuat kho tu don hang test.',
                ]);
            }
        }
    }

    private function seedManualBienDongKhos(Collection $products, string $batchCode, int $count): void
    {
        if ($products->isEmpty() || $count <= 0) {
            return;
        }

        for ($i = 1; $i <= $count; $i++) {
            /** @var SanPham $product */
            $product = $products->random();
            $before = (int) $product->stock;
            $forceImport = $before <= 3;
            $isImport = $forceImport || random_int(1, 100) <= 58;

            if (!$isImport && $before === 0) {
                $isImport = true;
            }

            if ($isImport) {
                $quantity = random_int(1, 20);
                $after = $before + $quantity;
                $type = self::MOVEMENT_IN;
            } else {
                $quantity = random_int(1, min(12, $before));
                $after = $before - $quantity;
                $type = self::MOVEMENT_OUT;
            }

            $product->stock = $after;
            $product->save();
            $products->put($product->id, $product);

            BienDongKho::create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $quantity,
                'stock_before' => $before,
                'stock_after' => $after,
                'movement_date' => now()->subDays(random_int(0, 120))->toDateString(),
                'reference_code' => 'ADJ'.$batchCode.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'note' => 'Dieu chinh ton kho bo sung cho du lieu test.',
            ]);
        }
    }

    private function pickDonHangStatus(): string
    {
        $roll = random_int(1, 100);

        if ($roll <= 30) {
            return DonHang::STATUS_COMPLETED;
        }

        if ($roll <= 68) {
            return DonHang::STATUS_PROCESSING;
        }

        return DonHang::STATUS_NEW;
    }

    private function canApplyDonHangStock(array $items): bool
    {
        foreach ($items as $item) {
            /** @var SanPham $product */
            $product = $item['product'];

            if ((int) $product->stock < (int) $item['quantity']) {
                return false;
            }
        }

        return true;
    }
}
