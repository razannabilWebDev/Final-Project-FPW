<?php

namespace App\Exceptions;

use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class InsufficientStockException extends RuntimeException implements ShouldntReport
{
    /**
     * @param  list<array{ingredient_id: int, name: string, required: string, available: string}>  $shortages
     */
    public function __construct(string $message, public readonly array $shortages = [])
    {
        parent::__construct($message);
    }

    /**
     * @param  list<array{ingredient_id: int, name: string, required: string, available: string}>  $shortages
     */
    public static function forShortages(array $shortages): self
    {
        $details = collect($shortages)
            ->map(fn (array $shortage): string => "{$shortage['name']}: butuh {$shortage['required']}, tersedia {$shortage['available']}")
            ->implode('; ');

        return new self('Stok tidak cukup. '.$details.'.', $shortages);
    }

    public static function forMissingIngredient(int $ingredientId): self
    {
        return new self("Bahan dengan ID {$ingredientId} tidak ditemukan.", []);
    }

    public function render(Request $request): RedirectResponse
    {
        return redirect()->back()->with('error', $this->getMessage());
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return ['shortages' => $this->shortages];
    }
}
