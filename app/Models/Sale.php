<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'transaction_code',
    'type',
    'status',
    'payment_method',
    'total',
    'note',
    'created_by',
    'completed_at',
    'stock_deducted_at',
    'reversal_done_at',
])]
class Sale extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => SaleStatus::class,
            'payment_method' => PaymentMethod::class,
            'total' => 'decimal:2',
            'completed_at' => 'datetime',
            'stock_deducted_at' => 'datetime',
            'reversal_done_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(SaleStatusHistory::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
