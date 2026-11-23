@extends('layouts.app')

@section('title', 'Stok Bahan Baku')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-[#142d70]">Daftar Bahan Baku</h2>
            <p class="mt-1 text-xs text-slate-500">Pantau persediaan dan pergerakan stok harian usaha.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($lowStockCount > 0)
                <span class="rounded-lg bg-amber-50 px-3 py-2.5 text-xs font-bold text-amber-700">{{ $lowStockCount }} bahan menipis</span>
            @endif
            @can('manage-stock')
                @if (Route::has('stock.movements'))
                    <a href="{{ route('stock.movements') }}" class="rounded-lg border border-[#dfe3e8] px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50">Riwayat Pergerakan</a>
                @endif
            @endcan
            @can('update-stock-physical')
                @if (Route::has('stock.adjustments.create'))
                    <a href="{{ route('stock.adjustments.create') }}" class="rounded-lg border border-[#dfe3e8] px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50">Sesuaikan Stok</a>
                @endif
            @endcan
            @can('manage-ingredients')
                @if (Route::has('ingredients.create'))
                    <a href="{{ route('ingredients.create') }}" class="rounded-lg bg-[#2563eb] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#1d4ed8]">+ Tambah Bahan</a>
                @endif
            @endcan
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-[#ecece8] bg-white shadow-[0_8px_24px_rgba(30,58,138,0.06)]">
        @forelse ($ingredients as $ingredient)
            @if ($loop->first)
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] text-left text-xs">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Nama Bahan</th>
                                <th class="px-5 py-3 font-semibold">Satuan</th>
                                <th class="px-5 py-3 font-semibold">Stok Saat Ini</th>
                                <th class="px-5 py-3 font-semibold">Minimum</th>
                                <th class="px-5 py-3 font-semibold">Harga Modal</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0f1f2]">
            @endif

            <tr class="hover:bg-slate-50/60">
                <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $ingredient->name }}</td>
                <td class="px-5 py-3.5 text-slate-500">{{ $ingredient->base_unit }}</td>
                <td class="px-5 py-3.5 font-semibold {{ bccomp((string) $ingredient->current_stock, (string) $ingredient->minimum_stock, 3) <= 0 ? 'text-amber-600' : 'text-slate-700' }}">
                    {{ number_format((float) $ingredient->current_stock, 3, ',', '.') }} {{ $ingredient->base_unit }}
                </td>
                <td class="px-5 py-3.5 text-slate-500">{{ number_format((float) $ingredient->minimum_stock, 3, ',', '.') }}</td>
                <td class="px-5 py-3.5 text-slate-500">Rp{{ number_format((float) $ingredient->average_cost, 0, ',', '.') }}</td>
                <td class="px-5 py-3.5">
                    @if ($ingredient->is_active)
                        <span class="rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-bold text-green-700">Aktif</span>
                    @else
                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-700">Nonaktif</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        @can('manage-ingredients')
                            <a href="{{ route('ingredients.edit', $ingredient) }}" class="rounded-lg border border-[#dfe3e8] px-3 py-1.5 text-[11px] font-bold text-slate-600 hover:bg-slate-50">Ubah</a>
                            <form method="POST" action="{{ route('ingredients.status', $ingredient) }}"
                                onsubmit="return confirm('{{ $ingredient->is_active ? 'Nonaktifkan bahan ini?' : 'Aktifkan kembali bahan ini?' }}')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" value="{{ $ingredient->is_active ? 1 : 0 }}">
                                <button type="submit" class="rounded-lg px-3 py-1.5 text-[11px] font-bold {{ $ingredient->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                    {{ $ingredient->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        @endcan
                        @can('record-waste')
                            @if (Route::has('stock.waste.create'))
                                <a href="{{ route('stock.waste.create', ['ingredient_id' => $ingredient->id]) }}" class="rounded-lg bg-amber-50 px-3 py-1.5 text-[11px] font-bold text-amber-700 hover:bg-amber-100">Susut</a>
                            @endif
                        @endcan
                    </div>
                </td>
            </tr>

            @if ($loop->last)
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-[#f0f1f2] px-5 py-4">{{ $ingredients->links() }}</div>
            @endif
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm font-semibold text-slate-600">Belum ada bahan baku</p>
                <p class="mt-1 text-xs text-slate-400">Tambahkan bahan baku pertama untuk mulai mencatat stok.</p>
            </div>
        @endforelse
    </div>
@endsection
