<?php

namespace App\Support;

/**
 * Format mata uang Rupiah dengan presisi dinamis.
 *
 * Nilai per satuan dasar (mis. harga per gram) bisa lebih kecil dari Rp1.
 * Pemformatan standar number_format(..., 0) akan menampilkan "Rp0" yang
 * menyesatkan, jadi nilai di bawah Rp1 ditampilkan dengan hingga 4 desimal.
 */
final class Rupiah
{
    public static function format(float|int|string|null $value): string
    {
        $amount = (float) ($value ?? 0);

        $decimals = abs($amount) < 1 && abs($amount) > 0 ? 4 : 0;

        return 'Rp'.number_format($amount, $decimals, ',', '.');
    }
}
