<?php

namespace Tests\Feature\KhachHang;

use App\Models\KhachHang;
use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CaKiemThu;

class TraiNghiemKhachHangTest extends CaKiemThu
{
    use RefreshDatabase;

    public function test_customer_can_view_customer_pages(): void
    {
        $customerUser = User::factory()->customer()->create();

        $this->actingAs($customerUser)->get(route('customer.dashboard'))->assertOk();
        $this->actingAs($customerUser)->get(route('customer.products.index'))->assertOk();
        $this->actingAs($customerUser)->get(route('customer.orders.index'))->assertOk();
        $this->actingAs($customerUser)->get(route('customer.orders.create'))->assertOk();
    }

    public function test_admin_cannot_access_customer_pages(): void
    {
        $adminUser = User::factory()->create();

        $this->actingAs($adminUser)->get(route('customer.dashboard'))->assertForbidden();
    }

    public function test_customer_can_create_order_from_customer_site(): void
    {
        $customerUser = User::factory()->customer()->create([
            'name' => 'Khách thử nghiệm',
            'email' => 'khach@example.com',
        ]);

        $product = SanPham::create([
            'name' => 'Bàn làm việc',
            'slug' => 'ban-lam-viec-blv9',
            'sku' => 'BLV9',
            'price' => 3200000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($customerUser)->post(route('customer.orders.store'), [
            'note' => 'Giao sau 18h',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $order = DonHang::first();

        $response->assertRedirect(route('customer.orders.show', $order));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => DonHang::STATUS_NEW,
            'stock_applied' => false,
            'total_amount' => 6400000,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_customer_cannot_view_order_of_another_customer(): void
    {
        $ownerUser = User::factory()->customer()->create([
            'name' => 'Chủ đơn',
            'email' => 'owner@example.com',
        ]);
        $otherUser = User::factory()->customer()->create([
            'name' => 'Khách khác',
            'email' => 'other@example.com',
        ]);

        $ownerProfile = KhachHang::create([
            'user_id' => $ownerUser->id,
            'name' => $ownerUser->name,
            'email' => $ownerUser->email,
        ]);

        $order = DonHang::create([
            'customer_id' => $ownerProfile->id,
            'code' => 'DH000123',
            'order_date' => now()->toDateString(),
            'status' => DonHang::STATUS_NEW,
            'stock_applied' => false,
            'total_amount' => 1000000,
        ]);

        $this->actingAs($otherUser)
            ->get(route('customer.orders.show', $order))
            ->assertForbidden();
    }
}
