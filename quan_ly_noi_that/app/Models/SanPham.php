<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SanPham extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'price',
        'stock',
        'material',
        'color',
        'description',
        'is_active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DanhMuc::class, 'category_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(ChiTietDonHang::class, 'product_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(BienDongKho::class, 'product_id');
    }

    public function purchaseReceiptItems(): HasMany
    {
        return $this->hasMany(ChiTietPhieuNhap::class, 'product_id');
    }
}
