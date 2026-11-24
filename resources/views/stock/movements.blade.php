@extends('layouts.app')

@section('title', 'Riwayat Pergerakan Stok')

@section('content')
    @php
        $labels = [
            'purchase_in' => 'Stok Masuk Pembelian',
            'sale_out' => 'Pemakaian Penjualan',
            'waste' => 'Susut/Rusak',
            'adjustment' => 'Penyesuaian',
            'reversal' => 'Pembalikan Stok',
        ];
        $tones = [
            'purchase_in' => 'bg-success-soft text-[#15803d]',
            'reversal' => 'bg-success-soft text-[#15803d]',
            'sale_out' => 'bg-warning-soft text-[#b45309]',
            'waste' => 'bg-danger-soft text-[#b91c1c]',
            'adjustment' => 'bg-info-soft text-[#1d4ed8]',
        ];
        $quantity = static function ($value): string {
            $formatted = number_format((float) $value, 3, ',', '.');

            return rtrim(rtrim($formatted, '0'), ',');
        };
    @endphp

    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Riwayat Pergerakan Stok</h1>
        <p class="mt-1 text-sm text-slate-500">Setiap perubahan stok memiliki catatan saldo sebelum dan sesudah.</p>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-stat-card label="Total Pergerakan" :value="number_format($totalMovements, 0, ',', '.')" icon="clock" tone="blue" />
        <x-stat-card label="Total Stok Masuk" :value="$quantity($totalStockIn)" icon="check" tone="green" />
        <x-stat-card label="Total Stok Keluar" :value="$quantity($totalStockOut)" icon="arrow-right" tone="orange" />
    </div>

    <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-[#142d70]">Catatan Pergerakan</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Menampilkan {{ $movements->count() }} dari {{ $movements->total() }} pergerakan</p>
            </div>
            @can('update-stock-physical')
                @if (Route::has('stock.adjustments.create'))
                    <a href="{{ route('stock.adjustments.create') }}" class="flex h-10 items-center gap-2 rounded-lg bg-brand px-4 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover">
                        <x-icon name="plus" :size="15" :stroke="2.4" />
                        Penyesuaian Stok
                    </a>
                @endif
            @endcan
        </div>

        <form method="GET" action="{{ route('stock.movements') }}" class="mb-4 flex flex-wrap gap-3">
            <select name="ingredient_id" class="h-10 min-w-[200px] rounded-lg border border-line-input bg-white px-3 text-sm outline-none focus:border-brand">
                <option value="">Semua bahan</option>
                @foreach ($ingredients as $ingredient)
                    <option value="{{ $ingredient->id }}" @selected($selectedIngredient === $ingredient->id)>{{ $ingredient->name }}</option>
                @endforeach
            </select>
            <select name="movement_type" class="h-10 min-w-[180px] rounded-lg border border-line-input bg-white px-3 text-sm outline-none focus:border-brand">
                <option value="">Semua jenis</option>
                @foreach ($labels as $value => $label)
                    <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="flex h-10 items-center gap-2 rounded-lg border border-line-input bg-white px-4 text-xs font-bold text-[#374151] transition hover:bg-slate-50">
                <x-icon name="filter" :size="15" />
                Filter
            </button>
            @if ($selectedIngredient > 0 || $selectedType !== '')
                <a href="{{ route('stock.movements') }}" class="flex h-10 items-center rounded-lg px-3 text-xs font-bold text-brand hover:text-brand-hover">Hapus filter</a>
            @endif
        </form>

        @if ($movements->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-xs">
                    <thead>
                        <tr class="border-b border-line bg-head-bg text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            <th class="px-3 py-3">Waktu</th>
                            <th class="px-3 py-3">Bahan</th>
                            <th class="px-3 py-3">Jenis</th>
                            <th class="px-3 py-3">Masuk</th>
                            <th class="px-3 py-3">Keluar</th>
                            <th class="px-3 py-3">Saldo</th>
                            <th class="px-3 py-3">Petugas</th>
                            <th class="px-3 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($movements as $movement)
                            <tr class="border-b border-line last:border-0 hover:bg-slate-50/60">
                                <td class="px-3 py-3 text-slate-500">{{ $movement->created_at?->translatedFormat('d M Y, H.i') ?? '—' }}</td>
                                <td class="px-3 py-3 font-bold text-[#142d70]">{{ $movement->ingredient?->name ?? '—' }}</td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $tones[$movement->movement_type] ?? 'bg-[#f1f5f9] text-[#64748b]' }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ $labels[$movement->movement_type] ?? $movement->movement_type }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 font-semibold text-[#15803d]">{{ bccomp((string) $movement->quantity_in, '0.000', 3) > 0 ? $quantity($movement->quantity_in) : '—' }}</td>
                                <td class="px-3 py-3 font-semibold text-[#b91c1c]">{{ bccomp((string) $movement->quantity_out, '0.000', 3) > 0 ? $quantity($movement->quantity_out) : '—' }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $quantity($movement->balance_before) }} → {{ $quantity($movement->balance_after) }}</td>
                                <td class="px-3 py-3 text-slate-500">{{ $movement->creator?->name ?? 'Sistem' }}</td>
                                <td class="px-3 py-3 text-slate-500">{{ $movement->note ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                <p class="text-[11px] text-slate-500">Menampilkan {{ $movements->firstItem() ?? 0 }}–{{ $movements->lastItem() ?? 0 }} dari {{ $movements->total() }} data</p>
                <div>{{ $movements->links() }}</div>
            </div>
        @else
            <x-empty-state icon="clock" title="Belum ada pergerakan stok" description="Pergerakan akan muncul setelah ada pembelian, penjualan, atau penyesuaian." />
        @endif
    </div>
@endsection
