<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Keuntungan — {{ $label }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; margin: 0 0 4px; color: #142d70; }
        h2 { font-size: 13px; margin: 18px 0 8px; color: #142d70; }
        .muted { color: #6b7280; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background: #f8fafc; font-size: 10px; text-transform: uppercase; color: #6b7280; }
        td.num, th.num { text-align: right; }
        .total { font-weight: bold; background: #eff6ff; }
        .note { margin-top: 14px; padding: 10px; background: #f8fafc; border: 1px solid #e5e7eb; }
        .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>

<body>
    <h1>Laporan Keuntungan</h1>
    <p class="muted">SmartFood Home · Periode: {{ $label }} · Dicetak {{ now()->translatedFormat('d F Y, H.i') }}</p>

    <h2>Ringkasan Keuangan</h2>
    <table>
        <thead>
            <tr>
                <th>Komponen</th>
                <th class="num">Nominal (Rp)</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Pendapatan (menu)</td><td class="num">{{ number_format($menuRevenue, 0, ',', '.') }}</td><td>{{ number_format($menuCount, 0, ',', '.') }} transaksi</td></tr>
            <tr><td>Pendapatan (manual)</td><td class="num">{{ number_format($manualRevenue, 0, ',', '.') }}</td><td>{{ $manualCount > 0 ? 'HPP tidak tersedia' : '-' }}</td></tr>
            <tr><td>HPP (snapshot bahan)</td><td class="num">-{{ number_format($hpp, 0, ',', '.') }}</td><td>Hanya transaksi berbasis menu</td></tr>
            <tr><td>Laba Kotor</td><td class="num">{{ number_format($grossProfit, 0, ',', '.') }}</td><td>Pendapatan − HPP</td></tr>
            <tr><td>Biaya Operasional</td><td class="num">-{{ number_format($expenses, 0, ',', '.') }}</td><td>Pengeluaran pada periode</td></tr>
            <tr class="total"><td>Laba Bersih</td><td class="num">{{ number_format($netProfit, 0, ',', '.') }}</td><td>Laba Kotor − Biaya Operasional</td></tr>
        </tbody>
    </table>

    <div class="note">
        <strong>Rumus:</strong> Pendapatan − Modal bahan baku terjual (snapshot) − Biaya operasional = Laba bersih.
        @if ($manualCount > 0)
            Penjualan manual ({{ number_format($manualCount, 0, ',', '.') }} transaksi, Rp{{ number_format($manualRevenue, 0, ',', '.') }}) menyumbang pendapatan tetapi <strong>HPP-nya tidak tersedia</strong>, sehingga laba bersih mungkin lebih tinggi dari nilai sebenarnya.
        @endif
    </div>

    @if ($topMenus->isNotEmpty())
        <h2>Menu Terlaris</h2>
        <table>
            <thead><tr><th>Menu</th><th class="num">Porsi</th><th class="num">Pendapatan (Rp)</th></tr></thead>
            <tbody>
                @foreach ($topMenus as $menu)
                    <tr>
                        <td>{{ $menu->name }}</td>
                        <td class="num">{{ number_format((float) $menu->qty, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format((float) $menu->revenue, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="footer">Dokumen ini dihasilkan otomatis oleh SmartFood Home.</p>
</body>

</html>
