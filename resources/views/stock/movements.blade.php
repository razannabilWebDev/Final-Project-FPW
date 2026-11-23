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
    @endphp

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-[#142d70]">Riwayat Pergerakan Stok</h2>
            <p class="mt-1 text-xs text-slate-500">Setiap perubahan stok memiliki catatan saldo sebelum dan sesudah.</p>
        </div>
        @can('update-stock-physical')
            @if (Route::has('stock.adjustments.create'))
                <a href="{{ route('stock.adjustments.create') }}" class="rounded-lg bg-[#2563eb] px-4 py-2.5 text-xs font-bold text-white transition hover:bg-[#1d4ed8]">+ Penyesuaian Stok</a>
            @endif
        @endcan
    </div>

    <form method="GET" action="{{ route('stock.movements') }}" class="mb-4 flex flex-wrap items-end gap-3 rounded-2xl border border-[#ecece8] bg-white p-4 shadow-[0_8px_24px_rgba(30,58,138,0.06)]">
        <div class="min-w-[220px] flex-1">
            <label for="ingredient_id" class="mb-2 block text-xs font-bold text-slate-700">Filter Bahan</label>
            <select id="ingredient_id" name="ingredient_id" class="w-full rounded-lg border border-[#dfe3e8] bg-white px-4 py-2.5 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100">
                <option value="">Semua bahan</option>
                @foreach ($ingredients as $ingredient)
                    <option value="{{ $ingredient->id }}" @selected($selectedIngredient === $ingredient->id)>{{ $ingredient->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg border border-[#dfe3e8] px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50">Terapkan</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-[#ecece8] bg-white shadow-[0_8px_24px_rgba(30,58,138,0.06)]">
        @forelse ($movements as $movement)
            @if ($loop->first)
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] text-left text-xs">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Waktu</th>
                                <th class="px-5 py-3 font-semibold">Bahan</th>
                                <th class="px-5 py-3 font-semibold">Jenis</th>
                                <th class="px-5 py-3 font-semibold">Masuk</th>
                                <th class="px-5 py-3 font-semibold">Keluar</th>
                                <th class="px-5 py-3 font-semibold">Saldo</th>
                                <th class="px-5 py-3 font-semibold">Petugas</th>
                                <th class="px-5 py-3 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0f1f2]">
            @endif

            <tr class="hover:bg-slate-50/60">
                <td class="px-5 py-3.5 text-slate-500">{{ $movement->created_at?->format('d M Y H:i') }}</td>
                <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $movement->ingredient?->name }}</td>
                <td class="px-5 py-3.5">
                    <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $movement->movement_type === 'sale_out' || $movement->movement_type === 'waste' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                        {{ $labels[$movement->movement_type] ?? $movement->movement_type }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-green-700">{{ bccomp((string) $movement->quantity_in, '0', 3) > 0 ? number_format((float) $movement->quantity_in, 3, ',', '.') : '—' }}</td>
                <td class="px-5 py-3.5 text-red-700">{{ bccomp((string) $movement->quantity_out, '0', 3) > 0 ? number_format((float) $movement->quantity_out, 3, ',', '.') : '—' }}</td>
                <td class="px-5 py-3.5 text-slate-600">{{ number_format((float) $movement->balance_before, 3, ',', '.') }} → {{ number_format((float) $movement->balance_after, 3, ',', '.') }}</td>
                <td class="px-5 py-3.5 text-slate-500">{{ $movement->creator?->name ?? 'Sistem' }}</td>
                <td class="px-5 py-3.5 text-slate-500">{{ $movement->note ?? '—' }}</td>
            </tr>

            @if ($loop->last)
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-[#f0f1f2] px-5 py-4">{{ $movements->links() }}</div>
            @endif
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm font-semibold text-slate-600">Belum ada pergerakan stok</p>
                <p class="mt-1 text-xs text-slate-400">Pergerakan akan muncul setelah ada pembelian, penjualan, atau penyesuaian.</p>
            </div>
        @endforelse
    </div>
@endsection
