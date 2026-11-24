<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Ingredient;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockService
{
    private const SCALE = 3;

    public function recordIn(
        Ingredient $ingredient,
        string $quantity,
        string $referenceType,
        ?int $referenceId = null,
        ?User $actor = null,
        ?string $unitCost = null,
        ?string $note = null,
    ): StockMovement {
        $this->assertPositive($quantity, 'Kuantitas stok masuk harus lebih dari nol.');

        return DB::transaction(fn (): StockMovement => $this->movement(
            ingredient: $this->lockIngredient($ingredient->id),
            movementType: 'purchase_in',
            quantityIn: $quantity,
            quantityOut: '0',
            referenceType: $referenceType,
            referenceId: $referenceId,
            actor: $actor,
            note: $note,
            unitCost: $unitCost,
        ));
    }

    public function recordOpeningStock(Ingredient $ingredient, string $quantity, ?User $actor = null): StockMovement
    {
        $this->assertPositive($quantity, 'Stok awal harus lebih dari nol.');

        return DB::transaction(fn (): StockMovement => $this->movement(
            ingredient: $this->lockIngredient($ingredient->id),
            movementType: 'adjustment',
            quantityIn: $quantity,
            quantityOut: '0',
            referenceType: 'Ingredient',
            referenceId: $ingredient->id,
            actor: $actor,
            note: 'Stok awal bahan',
        ));
    }

    public function recordWaste(Ingredient $ingredient, string $quantity, string $reason, User $actor): StockMovement
    {
        $this->assertPositive($quantity, 'Kuantitas susut/rusak harus lebih dari nol.');

        return DB::transaction(function () use ($ingredient, $quantity, $reason, $actor): StockMovement {
            $locked = $this->lockIngredient($ingredient->id);
            $this->assertAvailable($locked, $quantity, 'stok susut/rusak');

            return $this->movement(
                ingredient: $locked,
                movementType: 'waste',
                quantityIn: '0',
                quantityOut: $quantity,
                referenceType: 'Waste',
                referenceId: null,
                actor: $actor,
                note: $reason,
            );
        });
    }

    public function adjustPhysical(
        Ingredient $ingredient,
        string $physicalQuantity,
        string $reason,
        User $actor,
    ): StockAdjustment {
        if (! is_numeric($physicalQuantity) || bccomp($physicalQuantity, '0', self::SCALE) < 0) {
            throw new InvalidArgumentException('Stok fisik tidak boleh negatif.');
        }

        return DB::transaction(function () use ($ingredient, $physicalQuantity, $reason, $actor): StockAdjustment {
            $locked = $this->lockIngredient($ingredient->id);
            $systemQuantity = (string) $locked->current_stock;
            $difference = bcsub($physicalQuantity, $systemQuantity, self::SCALE);

            $adjustment = StockAdjustment::create([
                'ingredient_id' => $locked->id,
                'system_quantity' => $systemQuantity,
                'physical_quantity' => $physicalQuantity,
                'difference' => $difference,
                'reason' => $reason,
                'created_by' => $actor->id,
            ]);

            if (bccomp($difference, '0', self::SCALE) !== 0) {
                $this->movement(
                    ingredient: $locked,
                    movementType: 'adjustment',
                    quantityIn: bccomp($difference, '0', self::SCALE) > 0 ? $difference : '0',
                    quantityOut: bccomp($difference, '0', self::SCALE) < 0 ? bcsub('0', $difference, self::SCALE) : '0',
                    referenceType: 'StockAdjustment',
                    referenceId: $adjustment->id,
                    actor: $actor,
                    note: $reason,
                );
            }

            return $adjustment;
        });
    }

    /**
     * @param  list<array{ingredient_id: int, quantity: string}>  $requirements  Kebutuhan bahan dalam base_unit
     */
    public function deductForSale(int $saleId, array $requirements, ?User $actor = null): void
    {
        DB::transaction(function () use ($saleId, $requirements, $actor): void {
            if ($this->movementExists('Sale', $saleId, 'sale_out')) {
                return;
            }

            $needs = $this->normalizeRequirements($requirements);
            $ingredients = $this->lockIngredients(array_keys($needs));
            $shortages = [];

            foreach ($needs as $ingredientId => $required) {
                $ingredient = $ingredients->get($ingredientId);

                if ($ingredient === null) {
                    $shortages[] = [
                        'ingredient_id' => $ingredientId,
                        'name' => "Bahan #{$ingredientId}",
                        'required' => $required,
                        'available' => '0.000',
                    ];

                    continue;
                }

                if (bccomp((string) $ingredient->current_stock, $required, self::SCALE) < 0) {
                    $shortages[] = [
                        'ingredient_id' => $ingredientId,
                        'name' => $ingredient->name,
                        'required' => $required,
                        'available' => (string) $ingredient->current_stock,
                    ];
                }
            }

            if ($shortages !== []) {
                throw InsufficientStockException::forShortages($shortages);
            }

            foreach ($needs as $ingredientId => $required) {
                /** @var Ingredient $ingredient */
                $ingredient = $ingredients->get($ingredientId);

                $this->movement(
                    ingredient: $ingredient,
                    movementType: 'sale_out',
                    quantityIn: '0',
                    quantityOut: $required,
                    referenceType: 'Sale',
                    referenceId: $saleId,
                    actor: $actor,
                    note: 'Pengurangan stok penjualan',
                );
            }
        });
    }

    public function reverseForSale(int $saleId, ?User $actor = null): void
    {
        DB::transaction(function () use ($saleId, $actor): void {
            if ($this->movementExists('Sale', $saleId, 'reversal')) {
                return;
            }

            $deductions = StockMovement::query()
                ->where('reference_type', 'Sale')
                ->where('reference_id', $saleId)
                ->where('movement_type', 'sale_out')
                ->get(['ingredient_id', 'quantity_out']);

            if ($deductions->isEmpty()) {
                return;
            }

            $ingredients = $this->lockIngredients($deductions->pluck('ingredient_id')->all());

            foreach ($deductions as $deduction) {
                $ingredient = $ingredients->get($deduction->ingredient_id);

                if ($ingredient === null) {
                    throw InsufficientStockException::forMissingIngredient((int) $deduction->ingredient_id);
                }

                $this->movement(
                    ingredient: $ingredient,
                    movementType: 'reversal',
                    quantityIn: (string) $deduction->quantity_out,
                    quantityOut: '0',
                    referenceType: 'Sale',
                    referenceId: $saleId,
                    actor: $actor,
                    note: 'Pembalikan stok pembatalan penjualan',
                );
            }
        });
    }

    private function movement(
        Ingredient $ingredient,
        string $movementType,
        string $quantityIn,
        string $quantityOut,
        string $referenceType,
        ?int $referenceId,
        ?User $actor,
        ?string $note,
        ?string $unitCost = null,
    ): StockMovement {
        $balanceBefore = (string) $ingredient->current_stock;
        $balanceAfter = bcsub(bcadd($balanceBefore, $quantityIn, self::SCALE), $quantityOut, self::SCALE);

        if (bccomp($balanceAfter, '0', self::SCALE) < 0) {
            throw InsufficientStockException::forShortages([[
                'ingredient_id' => $ingredient->id,
                'name' => $ingredient->name,
                'required' => $quantityOut,
                'available' => $balanceBefore,
            ]]);
        }

        $ingredient->current_stock = $balanceAfter;

        if ($unitCost !== null && bccomp($quantityIn, '0', self::SCALE) > 0) {
            $denominator = bcadd($balanceBefore, $quantityIn, self::SCALE);
            $numerator = bcadd(
                bcmul((string) $ingredient->average_cost, $balanceBefore, 6),
                bcmul($unitCost, $quantityIn, 6),
                6,
            );

            $ingredient->average_cost = bccomp($denominator, '0', self::SCALE) > 0
                ? bcdiv($numerator, $denominator, 2)
                : $unitCost;
        }

        $ingredient->save();

        return StockMovement::create([
            'ingredient_id' => $ingredient->id,
            'movement_type' => $movementType,
            'quantity_in' => $quantityIn,
            'quantity_out' => $quantityOut,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'note' => $note,
            'created_by' => $actor?->id,
            'created_at' => now(),
        ]);
    }

    private function assertAvailable(Ingredient $ingredient, string $required, string $context): void
    {
        if (bccomp((string) $ingredient->current_stock, $required, self::SCALE) < 0) {
            throw InsufficientStockException::forShortages([[
                'ingredient_id' => $ingredient->id,
                'name' => $ingredient->name,
                'required' => $required,
                'available' => (string) $ingredient->current_stock,
            ]]);
        }
    }

    private function assertPositive(string $quantity, string $message): void
    {
        if (! is_numeric($quantity) || bccomp($quantity, '0', self::SCALE) <= 0) {
            throw new InvalidArgumentException($message);
        }
    }

    private function lockIngredient(int $ingredientId): Ingredient
    {
        /** @var Ingredient $ingredient */
        $ingredient = Ingredient::query()
            ->whereKey($ingredientId)
            ->lockForUpdate()
            ->firstOrFail();

        return $ingredient;
    }

    /**
     * @param  list<int>  $ingredientIds
     * @return Collection<int, Ingredient>
     */
    private function lockIngredients(array $ingredientIds): Collection
    {
        return Ingredient::query()
            ->whereIn('id', $ingredientIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    private function movementExists(string $referenceType, int $referenceId, string $movementType): bool
    {
        return StockMovement::query()
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('movement_type', $movementType)
            ->exists();
    }

    /**
     * @param  list<array{ingredient_id: int, quantity: string}>  $requirements
     * @return array<int, string>
     */
    private function normalizeRequirements(array $requirements): array
    {
        $needs = [];

        foreach ($requirements as $requirement) {
            $ingredientId = (int) ($requirement['ingredient_id'] ?? 0);
            $quantity = (string) ($requirement['quantity'] ?? '');

            if ($ingredientId <= 0 || ! is_numeric($quantity) || bccomp($quantity, '0', self::SCALE) <= 0) {
                throw new InvalidArgumentException('Format kebutuhan bahan tidak valid.');
            }

            $needs[$ingredientId] = isset($needs[$ingredientId])
                ? bcadd($needs[$ingredientId], $quantity, self::SCALE)
                : $quantity;
        }

        if ($needs === []) {
            throw new InvalidArgumentException('Kebutuhan bahan tidak boleh kosong.');
        }

        ksort($needs);

        return $needs;
    }
}
