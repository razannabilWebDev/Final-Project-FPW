@extends('layouts.app')

@section('title', 'Laporan Keuntungan')

@section('content')
    @php
        $rupiah = fn ($v) => 'Rp'.number_format((float) $v, 0, ',', '.');
        $pct = fn ($part, $total) => $total > 0 ? number_format(($part / $total) * 100, 1, ',', '.').'%' : '0,0%';
    @endphp

    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Laporan Keuntungan</h1>
            <p class="mt-1 text-sm text-slate-500">Analisis kesehatan dan pertumbuhan keuangan.</p>
        </div>
        @can('export-reports')
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('reports.profit.pdf', request()->query()) }}" target="_blank"
                   class="flex items-center gap-2 rounded-lg border border-line-input bg-white px-3 py-2 text-xs font-bold text-[#374151] hover:border-brand">
                    <x-icon name="receipt" :size="14" /> Cetak / PDF
                </a>
                <a href="{{ route('reports.profit.excel', request()->query()) }}"
                   class="flex items-center gap-2 rounded-lg bg-brand px-3 py-2 text-xs font-bold text-white shadow-lg shadow-blue-200 hover:bg-brand-hover">
                    Excel
                </a>
            </div>
        @endcan
    </div>

    {{-- Filter periode: tabs + date range --}}
    <div class="mb-5 flex flex-wrap items-center gap-3">
        <div class="flex flex-wrap items-center gap-2">
            @foreach (['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $key => $label)
                <a href="{{ route('reports.profit', ['period' => $key]) }}"
                   class="rounded-lg px-3 py-1.5 text-xs font-bold transition {{ $period === $key ? 'bg-brand text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $label }}
                </a>
            @endforeach
            @if ($period || request('from') || request('to'))
                <a href="{{ route('reports.profit') }}" class="text-xs font-bold text-brand hover:text-brand-hover">Reset</a>
            @endif
        </div>
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <input type="date" name="from" value="{{ request('from') }}" aria-label="Dari tanggal"
                   class="h-9 rounded-lg border border-line-input px-2 text-xs outline-none focus:border-brand">
            <span class="text-xs text-slate-400">s/d</span>
            <input type="date" name="to" value="{{ request('to') }}" aria-label="Sampai tanggal"
                   class="h-9 rounded-lg border border-line-input px-2 text-xs outline-none focus:border-brand">
            <button type="submit" class="h-9 rounded-lg border border-line-input bg-white px-3 text-xs font-bold text-[#374151]">Terapkan</button>
        </form>
        <span class="ml-auto text-xs font-semibold text-slate-500">{{ $label }}</span>
    </div>

    {{-- KPI --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Pendapatan" :value="$rupiah($revenue)" icon="wallet" tone="blue" :hint="number_format($transactionCount, 0, ',', '.').' transaksi selesai'" />
        <x-stat-card label="Modal Bahan Baku" :value="$rupiah($hpp)" icon="package" tone="orange" hint="Dari snapshot HPP per porsi" />
        <x-stat-card label="Biaya Operasional" :value="$rupiah($expenses)" icon="receipt" tone="purple" hint="Pengeluaran pada periode" />
        <x-stat-card label="Laba Bersih" :value="$rupiah($netProfit)" icon="trending" tone="green" hint="Pendapatan − HPP − Biaya" />
    </div>

    {{-- Grafik --}}
    <div class="mb-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <h3 class="text-sm font-bold text-[#142d70]">Pendapatan vs Pengeluaran</h3>
            <p class="mt-0.5 text-[11px] text-slate-500">{{ $label }}</p>
            <div class="mt-5 flex h-52 items-end gap-3 border-b border-line pb-2">
                @php $max = max($revenue, $expenses, 1); @endphp
                <div class="flex flex-1 items-end justify-center gap-2">
                    <div class="w-10 rounded-t bg-brand transition-all" style="height: {{ round($revenue / $max * 100) }}%" title="Pendapatan {{ $rupiah($revenue) }}"></div>
                    <div class="w-10 rounded-t bg-chart-orange transition-all" style="height: {{ round($expenses / $max * 100) }}%" title="Pengeluaran {{ $rupiah($expenses) }}"></div>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-center gap-5 text-[10px] font-semibold text-slate-500">
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-brand"></span> Pendapatan</span>
                <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-chart-orange"></span> Pengeluaran</span>
            </div>
        </div>

        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <h3 class="text-sm font-bold text-[#142d70]">Tren Laba Bersih</h3>
            <p class="mt-0.5 text-[11px] text-slate-500">Pertumbuhan harian pada periode</p>
            @if ($trend->isNotEmpty())
                <div class="mt-5 flex h-52 items-end gap-2 border-b border-line pb-2">
                    @php $maxTrend = $trend->max(fn ($row) => (float) $row->menu_revenue + (float) $row->manual_revenue) ?: 1; @endphp
                    @foreach ($trend as $row)
                        @php $amount = (float) $row->menu_revenue + (float) $row->manual_revenue; @endphp
                        <div class="flex flex-1 flex-col items-center justify-end gap-1">
                            <div class="w-full max-w-8 rounded-t bg-brand" style="height: {{ max(4, round($amount / $maxTrend * 100)) }}%" title="{{ $row->day }}: {{ $rupiah($amount) }}"></div>
                            <span class="text-[9px] text-slate-400">{{ \Carbon\Carbon::parse($row->day)->translatedFormat('d M') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-6"><x-empty-state icon="chart" title="Belum ada tren" description="Data penjualan selesai akan tampil di grafik." /></div>
            @endif
        </div>
    </div>

    {{-- Ringkasan keuangan --}}
    <div class="mb-6 rounded-2xl border border-line bg-white p-5 shadow-card">
        <h3 class="text-sm font-bold text-[#142d70]">Ringkasan Keuangan</h3>
        <p class="mt-0.5 text-[11px] text-slate-500">Periode {{ $label }}</p>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-line bg-head-bg text-[10px] font-bold uppercase text-slate-500">
                        <th class="px-3 py-2.5">Komponen</th>
                        <th class="px-3 py-2.5">Nominal</th>
                        <th class="px-3 py-2.5">Porsi</th>
                        <th class="px-3 py-2.5">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-line">
                        <td class="px-3 py-2.5 font-semibold text-[#374151]">Pendapatan (menu)</td>
                        <td class="px-3 py-2.5">{{ $rupiah($menuRevenue) }}</td>
                        <td class="px-3 py-2.5">{{ $pct($menuRevenue, $revenue) }}</td>
                        <td class="px-3 py-2.5 text-slate-500">{{ number_format($menuCount, 0, ',', '.') }} transaksi</td>
                    </tr>
                    <tr class="border-b border-line">
                        <td class="px-3 py-2.5 font-semibold text-[#374151]">Pendapatan (manual)</td>
                        <td class="px-3 py-2.5">{{ $rupiah($manualRevenue) }}</td>
                        <td class="px-3 py-2.5">{{ $pct($manualRevenue, $revenue) }}</td>
                        <td class="px-3 py-2.5">
                            @if ($manualCount > 0)
                                <span class="rounded-full bg-[#fef3c7] px-2 py-0.5 text-[10px] font-bold text-[#b45309]">HPP tidak tersedia</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr class="border-b border-line">
                        <td class="px-3 py-2.5 font-semibold text-[#374151]">HPP (snapshot bahan)</td>
                        <td class="px-3 py-2.5">-{{ $rupiah($hpp) }}</td>
                        <td class="px-3 py-2.5">{{ $pct($hpp, $revenue) }}</td>
                        <td class="px-3 py-2.5 text-slate-500">Hanya transaksi berbasis menu</td>
                    </tr>
                    <tr class="border-b border-line">
                        <td class="px-3 py-2.5 font-semibold text-[#374151]">Laba Kotor</td>
                        <td class="px-3 py-2.5 font-bold">{{ $rupiah($grossProfit) }}</td>
                        <td class="px-3 py-2.5">{{ $pct($grossProfit, $revenue) }}</td>
                        <td class="px-3 py-2.5 text-slate-500">Pendapatan − HPP</td>
                    </tr>
                    <tr class="border-b border-line">
                        <td class="px-3 py-2.5 font-semibold text-[#374151]">Biaya Operasional</td>
                        <td class="px-3 py-2.5">-{{ $rupiah($expenses) }}</td>
                        <td class="px-3 py-2.5">{{ $pct($expenses, $revenue) }}</td>
                        <td class="px-3 py-2.5 text-slate-500">Pengeluaran pada periode</td>
                    </tr>
                    <tr class="bg-head-bg">
                        <td class="px-3 py-3 font-extrabold text-[#142d70]">Laba Bersih</td>
                        <td class="px-3 py-3 font-extrabold text-[#142d70]">{{ $rupiah($netProfit) }}</td>
                        <td class="px-3 py-3 font-bold text-[#142d70]">{{ $pct($netProfit, $revenue) }}</td>
                        <td class="px-3 py-3 text-slate-500">Laba Kotor − Biaya Operasional</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex items-start gap-3 rounded-xl border border-[#dbeafe] bg-[#eff6ff] px-4 py-3">
            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#dbeafe] text-brand">
                <x-icon name="shield" :size="15" />
            </span>
            <p class="text-[11px] leading-relaxed text-[#4b5563]">
                <strong class="text-[#1d4ed8]">Rumus:</strong> Pendapatan − Modal bahan baku terjual (snapshot) − Biaya operasional = Laba bersih.
                @if ($manualCount > 0)
                    Penjualan manual ({{ number_format($manualCount, 0, ',', '.') }} transaksi, {{ $rupiah($manualRevenue) }}) menyumbang pendapatan tetapi <strong>HPP-nya tidak tersedia</strong>, sehingga laba bersih mungkin lebih tinggi dari nilai sebenarnya.
                @endif
            </p>
        </div>
    </div>

    {{-- Menu terlaris --}}
    @if ($topMenus->isNotEmpty())
        <div class="mb-6 rounded-2xl border border-line bg-white p-5 shadow-card">
            <h3 class="mb-4 text-sm font-bold text-[#142d70]">Menu Terlaris</h3>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($topMenus as $menu)
                    <div class="rounded-xl border border-line p-3">
                        <p class="truncate text-xs font-bold text-[#374151]">{{ $menu->name }}</p>
                        <p class="mt-1 text-[10px] text-slate-500">{{ number_format((float) $menu->qty, 0, ',', '.') }} porsi</p>
                        <p class="mt-1 text-xs font-bold text-brand">{{ $rupiah($menu->revenue) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Rincian --}}
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <h3 class="mb-4 text-sm font-bold text-[#142d70]">Rincian Penjualan</h3>
            @if ($saleDetails->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead><tr class="border-b border-line text-[10px] font-bold uppercase text-slate-500">
                            <th class="py-2">Transaksi</th><th class="py-2">Jenis</th><th class="py-2 text-right">Total</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($saleDetails as $sale)
                                <tr class="border-b border-line last:border-0">
                                    <td class="py-2 font-semibold text-[#142d70]">{{ $sale->transaction_code }}</td>
                                    <td class="py-2 text-slate-500">{{ $sale->type === 'manual' ? 'Manual' : 'Menu' }}</td>
                                    <td class="py-2 text-right">{{ $rupiah($sale->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $saleDetails->links() }}</div>
            @else
                <x-empty-state icon="receipt" title="Belum ada penjualan" description="Transaksi selesai akan tampil di sini." />
            @endif
        </div>

        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <h3 class="mb-4 text-sm font-bold text-[#142d70]">Rincian Biaya</h3>
            @if ($expenseDetails->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead><tr class="border-b border-line text-[10px] font-bold uppercase text-slate-500">
                            <th class="py-2">Tanggal</th><th class="py-2">Keterangan</th><th class="py-2 text-right">Nominal</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($expenseDetails as $expense)
                                <tr class="border-b border-line last:border-0">
                                    <td class="py-2 text-slate-500">{{ $expense->expense_date->translatedFormat('d M Y') }}</td>
                                    <td class="py-2 text-[#374151]">{{ $expense->description ?? $expense->category?->name }}</td>
                                    <td class="py-2 text-right font-semibold">{{ $rupiah($expense->amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $expenseDetails->links() }}</div>
            @else
                <x-empty-state icon="wallet" title="Belum ada biaya" description="Pengeluaran operasional akan tampil di sini." />
            @endif
        </div>
    </div>
@endsection
