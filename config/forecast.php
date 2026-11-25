<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Metode prediksi produksi (D8 / PRD §6.9)
    |--------------------------------------------------------------------------
    |
    | Rata-rata bergerak harian per menu. Jumlah porsi dalam jendela historis
    | dibagi jumlah hari dalam jendela (bukan hanya hari dengan transaksi).
    | Jumlah hari dengan data lebih kecil dari min_days → status "terbatas".
    |
    */
    'method' => 'moving_average',
    'window_days' => 28,
    'min_days' => 7,
];
