<?php

namespace App\Actions\Stock;

use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\StockService;

class RecordWasteAction
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * @param  array{ingredient_id: int, quantity: string, reason: string}  $data
     */
    public function execute(array $data, User $actor): StockMovement
    {
        return $this->stock->recordWaste(
            Ingredient::findOrFail($data['ingredient_id']),
            $data['quantity'],
            $data['reason'],
            $actor,
        );
    }
}
