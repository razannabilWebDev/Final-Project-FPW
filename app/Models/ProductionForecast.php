<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionForecast extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'forecast_date',
        'method',
        'data_window',
        'min_days',
        'data_status',
        'period_start',
        'period_end',
        'generated_by',
        'generated_at',
        'adjusted_by',
        'adjusted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'forecast_date' => 'date',
            'period_start' => 'date',
            'period_end' => 'date',
            'generated_at' => 'datetime',
            'adjusted_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductionForecastItem::class, 'forecast_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
