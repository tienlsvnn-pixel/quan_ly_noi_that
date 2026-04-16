<?php

namespace Tests\Feature\QuanTri;

use App\Models\DanhMuc;
use App\Models\KhachHang;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\SanPham;
use App\Models\BienDongKho;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\CaKiemThu;

class QuanTriHeThongTest extends CaKiemThu
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_management_pages(): void
    {
        $user = User::factory()->create();
        $customer = KhachHang::create(['name' => 'Khách mẫu']);
        $order = DonHang::create([
            'customer_id' => $customer->id,
            'code' => 'DH999',
            'order_date' => now()->toDateString(),
            'status' => 'Mới',
            'stock_applied' => false,
            'total_amount' => 0,
        ]);

        ChiTietDonHang::create([
            'order_id' => $order->id,
            'product_name' => 'Sản phẩm mẫu',
            'quantity' => 1,
            'unit_price' => 1000000,
            'line_total' => 1000000,
        ]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('admin.categories.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.products.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.customers.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.orders.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.orders.show', $order))->assertOk();
    }

    public function test_authenticated_user_can_create_category_product_and_customer(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.categories.store'), [
            'name' => 'Kệ trang trí',
            'description' => 'Danh mục cho kệ và tủ trang trí.',
            'is_active' => 1,
        ])->assertRedirect(route('admin.categories.index'));

        $category = DanhMuc::first();

        $this->actingAs($user)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Kệ sách gỗ hiện đại',
            'sku' => 'KS-001',
            'price' => 3500000,
            'stock' => 7,
            'material' => 'Gỗ MDF',
            'color' => 'Vân sồi',
            'description' => 'Kệ sách thiết kế tối giản.',
            'is_active' => 1,
        ])->assertRedirect(route('admin.products.index'));

        $this->actingAs($user)->post(route('admin.customers.store'), [
            'name' => 'Nguyễn Thị Mai',
            'email' => 'mai@example.com',
            'phone' => '0900000000',
            'city' => 'Cần Thơ',
            'address' => '123 Đường ABC',
            'note' => 'Khách quan tâm nội thất phong cách Nhật.',
        ])->assertRedirect(route('admin.customers.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Kệ trang trí',
            'slug' => Str::slug('Kệ trang trí'),
        ]);

        $this->assertDatabaseHas('products', [
            'sku' => 'KS-001',
            'name' => 'Kệ sách gỗ hiện đại',
        ]);

        $this->assertDatabaseHas('customers', [
            'email' => 'mai@example.com',
            'name' => 'Nguyễn Thị Mai',
        ]);
    }

    public function test_authenticated_user_can_update_and_delete_management_data(): void
    {
        $user = User::factory()->create();
        $category = DanhMuc::create([
            'name' => 'Sofa',
            'slug' => 'sofa',
            'is_active' => true,
        ]);
        $product = SanPham::create([
            'category_id' => $category->id,
            'name' => 'Sofa mẫu',
            'slug' => 'sofa-mau-sp1',
            'sku' => 'SP1',
            'price' => 1000000,
            'stock' => 2,
            'is_active' => true,
        ]);
        $customer = KhachHang::create([
            'name' => 'Khách cũ',
            'email' => 'old@example.com',
        ]);

        $this->actingAs($user)->put(route('admin.categories.update', $category), [
            'name' => 'Sofa cao cấp',
            'description' => 'Đã cập nhật',
            'is_active' => 1,
        ])->assertRedirect(route('admin.categories.index'));

        $this->actingAs($user)->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Sofa da Ý',
            'sku' => 'SP1',
            'price' => 2500000,
            'stock' => 5,
            'material' => 'Da',
            'color' => 'Đen',
            'description' => 'Đã cập nhật',
            'is_active' => 1,
        ])->assertRedirect(route('admin.products.index'));

        $this->actingAs($user)->put(route('admin.customers.update', $customer), [
            'name' => 'Khách mới',
            'email' => 'new@example.com',
            'phone' => '0911222333',
            'city' => 'Huế',
            'address' => '456 Đường XYZ',
            'note' => 'Đã cập nhật',
        ])->assertRedirect(route('admin.customers.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Sofa cao cấp']);
        $this->assertDatabaseHas('products', ['name' => 'Sofa da Ý']);
        $this->assertDatabaseHas('customers', ['email' => 'new@example.com']);

        $this->actingAs($user)->delete(route('admin.products.destroy', $product))->assertRedirect(route('admin.products.index'));
        $this->actingAs($user)->delete(route('admin.customers.destroy', $customer))->assertRedirect(route('admin.customers.index'));
        $this->actingAs($user)->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_authenticated_user_can_create_and_update_order(): void
    {
        $user = User::factory()->create();
        $customer = KhachHang::create(['name' => 'Khách đặt hàng']);
        $product = SanPham::create([
            'name' => 'Bàn ăn gỗ',
            'slug' => 'ban-an-go-ba1',
            'sku' => 'BA1',
            'price' => 5000000,
            'stock' => 3,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.orders.store'), [
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'status' => 'Mới',
            'note' => 'Đơn hàng thử nghiệm',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
                ['product_id' => '', 'quantity' => 1],
            ],
        ]);

        $order = DonHang::first();

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'customer_id' => $customer->id,
            'status' => 'Mới',
            'stock_applied' => false,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->actingAs($user)->put(route('admin.orders.update', $order), [
            'status' => 'Hoàn thành',
            'note' => 'Đã giao hàng',
        ])->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Hoàn thành',
            'stock_applied' => true,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 1,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'Xuất kho',
            'reference_code' => $order->code,
        ]);

        $this->actingAs($user)->put(route('admin.orders.update', $order), [
            'status' => 'Đang xử lý',
            'note' => 'Hoàn kho lại',
        ])->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Đang xử lý',
            'stock_applied' => false,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'Nhập kho',
            'reference_code' => $order->code.'-RETURN',
        ]);
    }

    public function test_cannot_delete_category_or_customer_when_related_data_exists(): void
    {
        $user = User::factory()->create();

        $category = DanhMuc::create([
            'name' => 'Sofa',
            'slug' => 'sofa',
            'is_active' => true,
        ]);

        SanPham::create([
            'category_id' => $category->id,
            'name' => 'Sofa mẫu',
            'slug' => 'sofa-mau',
            'sku' => 'SOFA-001',
            'price' => 1000000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $customer = KhachHang::create([
            'name' => 'Khách thân thiết',
            'email' => 'vip@example.com',
        ]);

        DonHang::create([
            'customer_id' => $customer->id,
            'code' => 'DH888',
            'order_date' => now()->toDateString(),
            'status' => 'Mới',
            'stock_applied' => false,
            'total_amount' => 0,
        ]);

        $this->actingAs($user)->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('error');

        $this->actingAs($user)->delete(route('admin.customers.destroy', $customer))
            ->assertRedirect(route('admin.customers.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }
}
