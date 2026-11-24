<?php

namespace App\Actions\Forecast;

use App\Models\ProductionForecastItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdjustForecastAction
{
    public function execute(
        ProductionForecastItem $item,
        User $actor,
        string $adjustedQuantity,
        ?string $note,
    ): ProductionForecastItem {
        return DB::transaction(function () use ($item, $actor, $adjustedQuantity, $note): ProductionForecastItem {
            $item->adjusted_quantity = $adjustedQuantity;
            $item->adjustment_note = $note;
            $item->save();

            $forecast = $item->forecast;
            $forecast->adjusted_by = $actor->id;
            $forecast->adjusted_at = now();
            $forecast->save();

            return $item;
        });
    }
}
