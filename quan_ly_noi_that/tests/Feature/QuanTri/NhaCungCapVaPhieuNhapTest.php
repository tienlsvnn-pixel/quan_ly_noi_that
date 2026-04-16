<?php

namespace Tests\Feature\QuanTri;

use App\Models\SanPham;
use App\Models\PhieuNhap;
use App\Models\NhaCungCap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CaKiemThu;

class NhaCungCapVaPhieuNhapTest extends CaKiemThu
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_supplier_and_purchase_receipt_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.suppliers.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.suppliers.create'))->assertOk();
        $this->actingAs($user)->get(route('admin.purchase-receipts.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.purchase-receipts.create'))->assertOk();
    }

    public function test_authenticated_user_can_create_supplier(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.suppliers.store'), [
            'name' => 'Xưởng Mộc Bình Minh',
            'contact_person' => 'Lê Quốc Bảo',
            'phone' => '0909777888',
            'email' => 'bao@binhminh.vn',
            'city' => 'Đồng Nai',
            'address' => 'Địa chỉ mẫu',
            'note' => 'Cung ứng hàng gỗ tự nhiên.',
            'is_active' => 1,
        ])->assertRedirect(route('admin.suppliers.index'));

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Xưởng Mộc Bình Minh',
            'email' => 'bao@binhminh.vn',
        ]);
    }

    public function test_authenticated_user_can_create_purchase_receipt_and_apply_stock(): void
    {
        $user = User::factory()->create();
        $supplier = NhaCungCap::create([
            'name' => 'Nội thất Minh Long',
            'is_active' => true,
        ]);
        $product = SanPham::create([
            'name' => 'Ghế ăn bọc nệm',
            'slug' => 'ghe-an-boc-nem-gan1',
            'sku' => 'GAN1',
            'price' => 1800000,
            'stock' => 4,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.purchase-receipts.store'), [
            'supplier_id' => $supplier->id,
            'receipt_date' => now()->toDateString(),
            'status' => 'Đã nhập kho',
            'note' => 'Phiếu nhập thử nghiệm',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3, 'unit_cost' => 1200000],
            ],
        ]);

        $receipt = PhieuNhap::first();

        $response->assertRedirect(route('admin.purchase-receipts.show', $receipt));
        $this->assertDatabaseHas('purchase_receipts', [
            'id' => $receipt->id,
            'supplier_id' => $supplier->id,
            'status' => 'Đã nhập kho',
            'stock_applied' => true,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 7,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'Nhập kho',
            'reference_code' => $receipt->code,
        ]);
    }

    public function test_authenticated_user_can_update_purchase_receipt_status_to_apply_stock(): void
    {
        $user = User::factory()->create();
        $supplier = NhaCungCap::create([
            'name' => 'Kho Gỗ Phú An',
            'is_active' => true,
        ]);
        $product = SanPham::create([
            'name' => 'Táp đầu giường',
            'slug' => 'tap-dau-giuong-tdg2',
            'sku' => 'TDG2',
            'price' => 2200000,
            'stock' => 2,
            'is_active' => true,
        ]);

        $receipt = PhieuNhap::create([
            'supplier_id' => $supplier->id,
            'code' => 'PN900',
            'receipt_date' => now()->toDateString(),
            'status' => 'Nháp',
            'stock_applied' => false,
            'total_amount' => 3000000,
        ]);

        $receipt->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_cost' => 1500000,
            'line_total' => 3000000,
        ]);

        $this->actingAs($user)->put(route('admin.purchase-receipts.update', $receipt), [
            'status' => 'Đã nhập kho',
            'note' => 'Đã nhận hàng',
        ])->assertRedirect(route('admin.purchase-receipts.show', $receipt));

        $this->assertDatabaseHas('purchase_receipts', [
            'id' => $receipt->id,
            'stock_applied' => true,
            'status' => 'Đã nhập kho',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 4,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'reference_code' => 'PN900',
        ]);
    }

    public function test_changing_receipt_back_to_draft_restores_stock(): void
    {
        $user = User::factory()->create();
        $supplier = NhaCungCap::create([
            'name' => 'Kho Gỗ Phú An',
            'is_active' => true,
        ]);
        $product = SanPham::create([
            'name' => 'Táp đầu giường',
            'slug' => 'tap-dau-giuong-tdg3',
            'sku' => 'TDG3',
            'price' => 2200000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $receipt = PhieuNhap::create([
            'supplier_id' => $supplier->id,
            'code' => 'PN901',
            'receipt_date' => now()->toDateString(),
            'status' => 'Đã nhập kho',
            'stock_applied' => true,
            'total_amount' => 3000000,
        ]);

        $receipt->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_cost' => 1500000,
            'line_total' => 3000000,
        ]);

        $this->actingAs($user)->put(route('admin.purchase-receipts.update', $receipt), [
            'status' => 'Nháp',
            'note' => 'Hoàn tác nhập kho',
        ])->assertRedirect(route('admin.purchase-receipts.show', $receipt));

        $this->assertDatabaseHas('purchase_receipts', [
            'id' => $receipt->id,
            'stock_applied' => false,
            'status' => 'Nháp',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'Xuất kho',
            'reference_code' => 'PN901-REVERSE',
        ]);
    }

    public function test_cannot_delete_supplier_with_existing_purchase_receipts(): void
    {
        $user = User::factory()->create();
        $supplier = NhaCungCap::create([
            'name' => 'Kho gỗ An Việt',
            'is_active' => true,
        ]);

        PhieuNhap::create([
            'supplier_id' => $supplier->id,
            'code' => 'PN777',
            'receipt_date' => now()->toDateString(),
            'status' => 'Nháp',
            'stock_applied' => false,
            'total_amount' => 0,
        ]);

        $this->actingAs($user)->delete(route('admin.suppliers.destroy', $supplier))
            ->assertRedirect(route('admin.suppliers.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }
}
