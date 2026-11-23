<?php

namespace App\Services\Contracts;

use App\Exceptions\InsufficientStockException;

interface StockService
{
    /**
     * Kurangi stok bahan sesuai resep untuk sebuah transaksi.
     * Idempoten: dipanggil ulang dengan reference_id yang sama tidak mengurangi lagi.
     *
     * @param  list<array{ingredient_id: int, quantity: string}>  $items
     *
     * @throws InsufficientStockException
     */
    public function deductForSale(int $saleId, array $items, ?int $userId = null): void;

    /**
     * Kembalikan stok (reversal) untuk sebuah transaksi yang dibatalkan.
     * Idempoten: dipanggil ulang tidak menambah lagi.
     */
    public function reverseForSale(int $saleId, ?int $userId = null): void;
}
