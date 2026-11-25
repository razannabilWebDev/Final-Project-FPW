<?php

namespace App\Enums;

enum SaleStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Processing => 'Diproses',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function isCompleted(): bool
    {
        return $this === self::Completed;
    }

    /**
     * Status lanjutan yang diizinkan dari status saat ini.
     * Transaksi batal bersifat terminal; selesai hanya boleh dibatalkan.
     *
     * @return list<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Processing, self::Completed, self::Cancelled],
            self::Processing => [self::Completed, self::Cancelled],
            self::Completed => [self::Cancelled],
            self::Cancelled => [],
        };
    }

    public static function fromString(string $value): self
    {
        return self::tryFrom($value) ?? throw new \InvalidArgumentException("Status penjualan tidak valid: {$value}");
    }
}
