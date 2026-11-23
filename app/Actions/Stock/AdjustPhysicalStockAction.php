<?php

namespace App\Actions\Stock;

use App\Models\Ingredient;
use App\Models\StockAdjustment;
use App\Models\User;
use App\Services\StockService;

class AdjustPhysicalStockAction
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * @param  array{ingredient_id: int, physical_quantity: string, reason: string}  $data
     */
    public function execute(array $data, User $actor): StockAdjustment
    {
        return $this->stock->adjustPhysical(
            Ingredient::findOrFail($data['ingredient_id']),
            $data['physical_quantity'],
            $data['reason'],
            $actor,
        );
    }
}
