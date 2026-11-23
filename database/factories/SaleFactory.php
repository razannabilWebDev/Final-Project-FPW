<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\SaleStatus;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'transaction_code' => 'TRX-'.fake()->unique()->numerify('########'),
            'type' => 'manual',
            'status' => SaleStatus::Pending,
            'payment_method' => PaymentMethod::Cash,
            'total' => fake()->randomFloat(2, 10000, 500000),
            'note' => fake()->optional()->sentence(),
            'created_by' => null,
        ];
    }
}
