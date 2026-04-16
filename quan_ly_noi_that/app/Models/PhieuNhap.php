<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhieuNhap extends Model
{
    protected $table = 'purchase_receipts';

    public const STATUS_DRAFT = 'Nháp';
    public const STATUS_IMPORTED = 'Đã nhập kho';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_IMPORTED,
    ];

    protected $fillable = [
        'supplier_id',
        'code',
        'receipt_date',
        'status',
        'stock_applied',
        'total_amount',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'receipt_date' => 'date',
            'stock_applied' => 'boolean',
            'total_amount' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(NhaCungCap::class, 'supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChiTietPhieuNhap::class, 'purchase_receipt_id');
    }

    public function isImported(): bool
    {
        return $this->status === self::STATUS_IMPORTED;
    }
}
