<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAndReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_inventory_and_reports_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.stock-movements.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.stock-movements.create'))->assertOk();
        $this->actingAs($user)->get(route('admin.reports.index'))->assertOk();
    }

    public function test_authenticated_user_can_create_stock_movement_and_update_product_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Tủ đầu giường',
            'slug' => 'tu-dau-giuong-tdg1',
            'sku' => 'TDG1',
            'price' => 1500000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($user)->post(route('admin.stock-movements.store'), [
            'product_id' => $product->id,
            'type' => 'Nhập kho',
            'quantity' => 3,
            'movement_date' => now()->toDateString(),
            'reference_code' => 'NK-TEST-01',
            'note' => 'Nhập thêm hàng',
        ])->assertRedirect(route('admin.stock-movements.index'));

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'Nhập kho',
            'quantity' => 3,
            'stock_before' => 5,
            'stock_after' => 8,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);
    }

    public function test_reports_page_displays_business_data(): void
    {
        $user = User::factory()->create();

        $customer = Customer::create([
            'name' => 'Khách báo cáo',
        ]);

        $product = Product::create([
            'name' => 'Ghế thư giãn',
            'slug' => 'ghe-thu-gian-gtg1',
            'sku' => 'GTG1',
            'price' => 3000000,
            'stock' => 2,
            'is_active' => true,
        ]);

        Order::create([
            'customer_id' => $customer->id,
            'code' => 'DH100',
            'order_date' => now()->toDateString(),
            'status' => 'Hoàn thành',
            'total_amount' => 9000000,
        ]);

        StockMovement::create([
            'product_id' => $product->id,
            'type' => 'Nhập kho',
            'quantity' => 5,
            'stock_before' => 0,
            'stock_after' => 5,
            'movement_date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('9.000.000đ');
        $response->assertSee('Ghế thư giãn');
    }

    public function test_cannot_delete_product_when_inventory_transactions_exist(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Ghế thư giãn',
            'slug' => 'ghe-thu-gian-gtg2',
            'sku' => 'GTG2',
            'price' => 3000000,
            'stock' => 2,
            'is_active' => true,
        ]);

        StockMovement::create([
            'product_id' => $product->id,
            'type' => 'Nhập kho',
            'quantity' => 5,
            'stock_before' => 0,
            'stock_after' => 5,
            'movement_date' => now()->toDateString(),
            'reference_code' => 'NK-LOCK-01',
        ]);

        $this->actingAs($user)->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
