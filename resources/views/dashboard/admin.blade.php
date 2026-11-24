@extends('layouts.app')

@php
    $rupiah = fn ($value) => 'Rp' . number_format($value, 0, ',', '.');
@endphp

@section('title', 'Ringkasan Usaha')

@section('content')
    <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Ringkasan Usaha</h2>
            <p class="mt-1 text-sm text-slate-500">Pantau performa dan aktivitas usaha Anda hari ini.</p>
        </div>
        <span class="flex items-center gap-2 rounded-[10px] border border-line bg-white px-3 py-2 text-xs font-semibold text-[#374151] shadow-sm">
            <x-icon name="calendar" :size="15" />
            {{ now()->translatedFormat('d F Y') }}
        </span>
    </div>

    {{-- Hero --}}
    <div class="relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-r from-[#153577] to-[#2864cc] p-7 text-white shadow-card">
        <div class="pointer-events-none absolute -right-16 -top-20 h-72 w-72 rounded-full border border-white/10"></div>
        <div class="relative flex flex-wrap items-center justify-between gap-6">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-blue-200">{{ now()->translatedFormat('l, d F Y') }}</p>
                <h3 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $greeting }}, {{ $user->name }}!</h3>
                <p class="mt-2 max-w-xl text-sm text-blue-100">Pantau pendapatan, HPP, biaya, dan keuntungan usaha Anda dalam satu layar.</p>
            </div>
            <div class="w-full max-w-xs rounded-xl border border-white/20 bg-white/10 p-4 backdrop-blur-sm">
                <p class="text-[11px] text-blue-100">Laba bersih</p>
                <p class="mt-1 text-2xl font-extrabold">{{ $rupiah($summary['profit']) }}</p>
                <div class="mt-3 h-1.5 w-full overflow-hidden rounded-full bg-white/20">
                    <div class="h-full rounded-full bg-accent-light" style="width: {{ $summary['revenue'] > 0 ? min(100, max(0, round($summary['profit'] / $summary['revenue'] * 100))) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Pendapatan" :value="$rupiah($summary['revenue'])" icon="wallet" tone="blue" :hint="number_format($summary['salesCount'], 0, ',', '.') . ' transaksi selesai'" />
        <x-stat-card label="Total HPP" :value="$rupiah($summary['hpp'])" icon="package" tone="orange" hint="Modal bahan terjual" />
        <x-stat-card label="Biaya Operasional" :value="$rupiah($summary['expenses'])" icon="receipt" tone="purple" hint="Total pengeluaran" />
        <x-stat-card label="Laba Bersih" :value="$rupiah($summary['profit'])" icon="trending" tone="green" hint="Pendapatan − HPP − Biaya" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Grafik --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card xl:col-span-2">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-[#142d70]">Pendapatan &amp; Pengeluaran</h3>
                    <p class="mt-0.5 text-[11px] text-slate-500">Ringkasan transaksi selesai dan biaya operasional</p>
                </div>
                <div class="flex items-center gap-3 text-[10px] font-semibold text-slate-500">
                    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-brand"></span> Pendapatan</span>
                    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-chart-orange"></span> Pengeluaran</span>
                </div>
            </div>
            @if ($summary['revenue'] > 0 || $summary['expenses'] > 0)
                <div class="flex h-52 items-end gap-3 border-b border-line pb-2">
                    @php
                        $max = max($summary['revenue'], $summary['expenses'], 1);
                    @endphp
                    <div class="flex flex-1 flex-col items-center gap-2">
                        <div class="flex h-40 w-full items-end justify-center gap-1.5">
                            <div class="w-6 rounded-t bg-brand" style="height: {{ round($summary['revenue'] / $max * 100) }}%" title="Pendapatan {{ $rupiah($summary['revenue']) }}"></div>
                            <div class="w-6 rounded-t bg-chart-orange" style="height: {{ round($summary['expenses'] / $max * 100) }}%" title="Pengeluaran {{ $rupiah($summary['expenses']) }}"></div>
                        </div>
                        <span class="text-[10px] text-slate-500">Periode ini</span>
                    </div>
                </div>
            @else
                <x-empty-state icon="chart" title="Belum ada transaksi" description="Grafik akan tampil setelah penjualan dan biaya operasional dicatat." />
            @endif
        </div>

        {{-- Rekomendasi --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-[#142d70]">Rekomendasi Produksi</h3>
                    <p class="mt-0.5 text-[11px] text-slate-500">Estimasi hari ini</p>
                </div>
                @if (Route::has('forecast.index'))
                    <a href="{{ route('forecast.index') }}" class="text-[11px] font-bold text-brand hover:text-brand-hover">Lihat semua</a>
                @endif
            </div>
            @if ($summary['forecast'])
                <p class="mt-6 text-center text-3xl font-extrabold text-[#142d70]">{{ number_format((float) $summary['forecast_portions'], 0, ',', '.') }}</p>
                <p class="mt-1 text-center text-[11px] text-slate-500">
                    total porsi @if ($summary['forecast']->data_status === 'terbatas') · data terbatas @endif
                </p>
            @else
                <div class="mt-6">
                    <x-empty-state icon="sparkles" title="Belum ada estimasi" description="Rekomendasi produksi akan tampil setelah data penjualan tersedia." />
                </div>
            @endif
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        {{-- Transaksi terbaru --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#142d70]">Transaksi Terbaru</h3>
                @if (Route::has('sales.index'))
                    <a href="{{ route('sales.index') }}" class="text-[11px] font-bold text-brand hover:text-brand-hover">Lihat semua</a>
                @endif
            </div>
            @if (Route::has('sales.index') && $summary['recentSales']->isNotEmpty())
                <ul class="divide-y divide-line">
                    @foreach ($summary['recentSales'] as $recent)
                        <li class="flex items-center justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-bold text-[#142d70]">{{ $recent->transaction_code }}</p>
                                <p class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($recent->created_at)->translatedFormat('d M Y, H.i') }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-xs font-bold text-[#374151]">Rp{{ number_format((float) $recent->total, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-slate-500">{{ \App\Enums\SaleStatus::tryFrom($recent->status)?->label() ?? $recent->status }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <x-empty-state icon="receipt" title="Belum ada transaksi" description="Penjualan yang dicatat akan tampil di sini." />
            @endif
        </div>

        {{-- Stok menipis --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#142d70]">Stok Menipis</h3>
                @if (Route::has('ingredients.index'))
                    <a href="{{ route('ingredients.index') }}" class="text-[11px] font-bold text-brand hover:text-brand-hover">Kelola stok</a>
                @endif
            </div>
            @forelse ($summary['lowStock'] as $item)
                <div class="flex items-center justify-between border-b border-line py-2.5 last:border-0">
                    <div>
                        <p class="text-xs font-bold text-[#374151]">{{ $item->name }}</p>
                        <p class="text-[10px] text-slate-500">Min. {{ number_format((float) $item->minimum_stock, 1, ',', '.') }} {{ $item->base_unit }}</p>
                    </div>
                    <span class="rounded-full bg-[#fef3c7] px-2.5 py-1 text-[10px] font-bold text-[#b45309]">{{ number_format((float) $item->current_stock, 1, ',', '.') }} {{ $item->base_unit }}</span>
                </div>
            @empty
                <x-empty-state icon="box" title="Stok aman" description="Belum ada bahan yang menipis." />
            @endforelse
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        {{-- Menu terlaris --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <h3 class="mb-4 text-sm font-bold text-[#142d70]">Menu Terlaris</h3>
            @forelse ($summary['topMenus'] as $item)
                <div class="flex items-center justify-between border-b border-line py-2.5 last:border-0">
                    <span class="text-xs font-semibold text-[#374151]">{{ $item->name }}</span>
                    <span class="text-xs font-bold text-brand">{{ number_format((float) $item->qty, 0, ',', '.') }} porsi</span>
                </div>
            @empty
                <x-empty-state icon="chef" title="Belum ada data" description="Menu terlaris tampil setelah ada penjualan berbasis menu." />
            @endforelse
        </div>

        {{-- Pembelian menunggu verifikasi --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#142d70]">Pembelian Menunggu Verifikasi</h3>
                @if (Route::has('purchases.index'))
                    <a href="{{ route('purchases.index') }}" class="text-[11px] font-bold text-brand hover:text-brand-hover">Lihat semua</a>
                @endif
            </div>
            @if ($summary['purchasesPending'] > 0)
                <p class="text-xs text-slate-500">{{ $summary['purchasesPending'] }} nota menunggu verifikasi Anda.</p>
            @else
                <x-empty-state icon="check" title="Semua terverifikasi" description="Tidak ada pembelian yang menunggu verifikasi." />
            @endif
        </div>
    </div>
@endsection
