<?php

namespace App\Actions\Forecast;

class CalculateMovingAverageAction
{
    /**
     * Rata-rata porsi harian = total porsi dalam jendela ÷ jumlah hari jendela.
     * Hari tanpa transaksi tetap dihitung sebagai 0 (window tetap).
     *
     * @param  list<array{date: string, quantity: string}>  $dailySales
     * @return array{average: string, days_with_data: int, data_status: string}
     */
    public function execute(array $dailySales, int $windowDays, int $minDays): array
    {
        $total = '0';
        $daysWithData = [];

        foreach ($dailySales as $sale) {
            $total = bcadd($total, (string) $sale['quantity'], 3);
            $daysWithData[$sale['date']] = true;
        }

        $average = $windowDays > 0
            ? bcdiv($total, (string) $windowDays, 3)
            : '0.000';

        return [
            'average' => $average,
            'days_with_data' => count($daysWithData),
            'data_status' => count($daysWithData) >= $minDays ? 'cukup' : 'terbatas',
        ];
    }
}
