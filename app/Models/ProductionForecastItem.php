<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionForecastItem extends Model
{
    /** @var list<string> */
    protected $fillable = [
        'forecast_id',
        'menu_id',
        'predicted_quantity',
        'adjusted_quantity',
        'adjustment_note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'predicted_quantity' => 'decimal:3',
            'adjusted_quantity' => 'decimal:3',
        ];
    }

    public function forecast(): BelongsTo
    {
        return $this->belongsTo(ProductionForecast::class, 'forecast_id');
    }
}
