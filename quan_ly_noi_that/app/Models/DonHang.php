<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonHang extends Model
{
    protected $table = 'orders';

    public const STATUS_NEW = 'Mới';
    public const STATUS_PROCESSING = 'Đang xử lý';
    public const STATUS_COMPLETED = 'Hoàn thành';

    public const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_PROCESSING,
        self::STATUS_COMPLETED,
    ];

    protected $fillable = [
        'customer_id',
        'code',
        'order_date',
        'status',
        'stock_applied',
        'total_amount',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'stock_applied' => 'boolean',
            'total_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(KhachHang::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChiTietDonHang::class, 'order_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
