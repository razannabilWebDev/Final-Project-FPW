<?php

namespace Database\Factories;

use App\Enums\PurchaseStatus;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'purchase_code' => 'PUR-'.now()->format('Ymd').'-'.fake()->unique()->numerify('####'),
            'purchase_date' => fake()->date(),
            'supplier_name' => fake()->company(),
            'status' => PurchaseStatus::Pending,
            'total' => '0.00',
            'receipt_path' => null,
            'created_by' => null,
            'verified_by' => null,
            'verified_at' => null,
            'rejection_note' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PurchaseStatus::Approved,
            'verified_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PurchaseStatus::Rejected,
            'verified_at' => now(),
            'rejection_note' => 'Tidak sesuai nota.',
        ]);
    }
}
