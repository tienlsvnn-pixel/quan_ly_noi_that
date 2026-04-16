<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NhaCungCap extends Model
{
    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'city',
        'address',
        'note',
        'is_active',
    ];

    public function purchaseReceipts(): HasMany
    {
        return $this->hasMany(PhieuNhap::class, 'supplier_id');
    }
}
