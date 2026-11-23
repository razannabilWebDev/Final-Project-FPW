<?php

namespace App\Exports;

use App\Services\ProfitReportService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProfitReportExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function __construct(
        private readonly ProfitReportService $reports,
        private readonly ?Carbon $start,
        private readonly ?Carbon $end,
    ) {}

    public function headings(): array
    {
        return ['Komponen', 'Nominal (Rp)', 'Keterangan'];
    }

    public function array(): array
    {
        $s = $this->reports->summarize($this->start, $this->end);

        return [
            ['Pendapatan (menu)', $s['menuRevenue'], number_format($s['menuCount'], 0, ',', '.').' transaksi'],
            ['Pendapatan (manual)', $s['manualRevenue'], $s['manualCount'] > 0 ? 'HPP tidak tersedia' : '-'],
            ['HPP (snapshot bahan)', -$s['hpp'], 'Hanya transaksi berbasis menu'],
            ['Laba Kotor', $s['grossProfit'], 'Pendapatan - HPP'],
            ['Biaya Operasional', -$s['expenses'], 'Pengeluaran pada periode'],
            ['Laba Bersih', $s['netProfit'], 'Laba Kotor - Biaya Operasional'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
            6 => ['font' => ['bold' => true]],
        ];
    }
}
