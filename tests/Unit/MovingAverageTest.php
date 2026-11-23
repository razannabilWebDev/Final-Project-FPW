<?php

namespace Tests\Unit;

use App\Actions\Forecast\CalculateMovingAverageAction;
use PHPUnit\Framework\TestCase;

class MovingAverageTest extends TestCase
{
    public function test_average_uses_full_window_and_counts_distinct_days(): void
    {
        $result = (new CalculateMovingAverageAction)->execute([
            ['date' => '2026-11-01', 'quantity' => '10'],
            ['date' => '2026-11-02', 'quantity' => '20'],
            ['date' => '2026-11-02', 'quantity' => '5'],
        ], 10, 7);

        $this->assertSame('3.500', $result['average']);
        $this->assertSame(2, $result['days_with_data']);
        $this->assertSame('terbatas', $result['data_status']);
    }

    public function test_data_status_becomes_cukup_when_minimum_days_are_reached(): void
    {
        $sales = [];

        for ($day = 1; $day <= 7; $day++) {
            $sales[] = ['date' => sprintf('2026-11-%02d', $day), 'quantity' => '7'];
        }

        $result = (new CalculateMovingAverageAction)->execute($sales, 28, 7);

        $this->assertSame('1.750', $result['average']);
        $this->assertSame(7, $result['days_with_data']);
        $this->assertSame('cukup', $result['data_status']);
    }

    public function test_zero_window_returns_zero_average(): void
    {
        $result = (new CalculateMovingAverageAction)->execute([
            ['date' => '2026-11-01', 'quantity' => '10'],
        ], 0, 7);

        $this->assertSame('0.000', $result['average']);
        $this->assertSame('terbatas', $result['data_status']);
    }
}
