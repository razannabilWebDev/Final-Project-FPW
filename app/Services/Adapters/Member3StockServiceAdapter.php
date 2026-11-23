<?php

namespace App\Services\Adapters;

use App\Models\Sale;
use App\Models\User;
use App\Services\Contracts\StockService;
use App\Services\StockService as Member3StockService;

/**
 * Menjembatani kontrak StockService milik Anggota 4 ke implementasi
 * service stok terpusat Anggota 3 tanpa mengubah signature kontrak.
 */
class Member3StockServiceAdapter implements StockService
{
    public function __construct(private readonly Member3StockService $stock) {}

    public function deductForSale(int $saleId, array $items, ?int $userId = null): void
    {
        $this->stock->deductForSale($saleId, $items, $this->resolveActor($userId));

        Sale::whereKey($saleId)->update(['stock_deducted_at' => now()]);
    }

    public function reverseForSale(int $saleId, ?int $userId = null): void
    {
        $this->stock->reverseForSale($saleId, $this->resolveActor($userId));

        Sale::whereKey($saleId)->update(['reversal_done_at' => now()]);
    }

    private function resolveActor(?int $userId): ?User
    {
        return $userId !== null ? User::find($userId) : null;
    }
}
