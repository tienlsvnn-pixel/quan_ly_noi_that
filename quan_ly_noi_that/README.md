# HomeStore - Website Quản Lý Bán Đồ Nội Thất

Đây là dự án Laravel 12 dành cho việc quản lý bán đồ nội thất, gồm các phân hệ:

- Khu vực quản trị (admin): quản lý danh mục, sản phẩm, kho, đơn hàng, báo cáo
- Khu vực khách hàng (customer): đăng ký tài khoản, xem sản phẩm, đặt hàng, theo dõi đơn hàng
- Đăng nhập phân quyền theo vai trò

## Công nghệ sử dụng

- PHP 8.2+
- Laravel 12
- MySQL
- Blade

## Cấu hình MySQL

Mặc định dự án đã được chuyển sang MySQL trong `.env` và `.env.example`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestore_noithat
DB_USERNAME=root
DB_PASSWORD=
```

Bạn cần tạo database trước:

```sql
CREATE DATABASE homestore_noithat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Cài đặt dự án

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## Tài khoản quản trị mẫu

```text
Email: admin@homestore.vn
Mật khẩu: Admin@123456
```

## Tài khoản khách hàng mẫu (seed)

```text
Email: test@example.com
Mật khẩu: Customer@123456
```

## Chạy test

Test đang dùng SQLite in-memory để chạy nhanh và độc lập với MySQL:

```bash
php artisan test
```

## Các route chính

- `/login`
- `/register`
- `/admin`
- `/customer`
- `/customer/products`
- `/customer/orders`
- `/admin/categories`
- `/admin/products`
- `/admin/customers`
- `/admin/orders`

## Hướng phát triển tiếp theo

- CRUD đầy đủ cho đơn hàng với thêm/xóa nhiều dòng sản phẩm linh hoạt
- Quản lý tồn kho nhập/xuất
- Bộ lọc tìm kiếm nâng cao
- Báo cáo doanh thu theo ngày/tháng
- Giỏ hàng và thanh toán trực tuyến cho khách hàng
