@extends('layouts.app')

@section('title', 'Rekomendasi Produksi')

@section('content')
    <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Rekomendasi Produksi</h2>
            <p class="mt-1 text-sm text-slate-500">Estimasi porsi harian dan kebutuhan bahan berdasarkan riwayat penjualan.</p>
        </div>
        @can('manage-forecast')
            @if ($forecast === null)
                <form method="POST" action="{{ route('forecast.generate') }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex h-10 items-center gap-2 rounded-[10px] bg-brand px-4 text-xs font-bold text-white transition hover:bg-brand-hover">
                        <x-icon name="sparkles" :size="15" />
                        Buat Prediksi
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('forecast.generate') }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex h-10 items-center gap-2 rounded-[10px] border border-line bg-white px-4 text-xs font-bold text-[#374151] transition hover:bg-slate-50">
                        Perbarui Prediksi
                    </button>
                </form>
            @endif
        @endcan
    </div>

    @if ($forecast === null)
        <x-empty-state icon="sparkles" title="Belum ada prediksi"
            description="Admin dapat membuat prediksi produksi dari riwayat penjualan yang sudah selesai." />
    @else
        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <x-stat-card label="Total Porsi Rekomendasi"
                :value="number_format((float) $forecast->items->sum(fn ($item) => (float) ($item->adjusted_quantity ?? $item->predicted_quantity)), 0, ',', '.')"
                icon="sparkles" tone="blue" hint="Termasuk penyesuaian Admin" />
            <x-stat-card label="Periode Historis"
                :value="$forecast->period_start?->translatedFormat('d M Y') . ' – ' . $forecast->period_end?->translatedFormat('d M Y')"
                icon="calendar" tone="purple" :hint="'Jendela ' . $forecast->data_window . ' hari'" />
            <x-stat-card label="Kecukupan Data"
                :value="$forecast->data_status === 'cukup' ? 'Data cukup' : 'Data terbatas'"
                icon="chart" :tone="$forecast->data_status === 'cukup' ? 'green' : 'orange'"
                :hint="'Minimal ' . $forecast->min_days . ' hari dengan penjualan'" />
        </div>

        @if ($forecast->data_status === 'terbatas')
            <div class="mb-6 rounded-xl border border-[#fde68a] bg-[#fffbeb] px-4 py-3 text-xs text-[#92400e]" role="alert">
                Data historis masih terbatas. Angka di bawah hanya indikasi dan belum dapat dianggap akurat.
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-line bg-white shadow-card">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-bold text-[#142d70]">Estimasi Porsi per Menu</h3>
                <p class="mt-0.5 text-[11px] text-slate-500">Prediksi statistik dan nilai setelah penyesuaian disimpan terpisah.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-bold">Menu</th>
                            <th class="px-5 py-3 text-right font-bold">Prediksi</th>
                            <th class="px-5 py-3 text-right font-bold">Penyesuaian</th>
                            <th class="px-5 py-3 font-bold">Catatan</th>
                            @can('manage-forecast')
                                <th class="px-5 py-3 font-bold">Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($forecast->items as $item)
                            <tr>
                                <td class="px-5 py-3 font-semibold text-[#172033]">
                                    {{ $menuNames[$item->menu_id] ?? 'Menu #'.$item->menu_id }}
                                </td>
                                <td class="px-5 py-3 text-right text-slate-600">
                                    {{ number_format((float) $item->predicted_quantity, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-right font-bold text-[#142d70]">
                                    {{ $item->adjusted_quantity === null ? '—' : number_format((float) $item->adjusted_quantity, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-xs text-slate-500">{{ $item->adjustment_note ?: '—' }}</td>
                                @can('manage-forecast')
                                    <td class="px-5 py-3">
                                        <form method="POST" action="{{ route('forecast.adjust', $item) }}" class="flex flex-wrap items-end gap-2">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="adjusted_quantity" min="0" step="0.001" required
                                                value="{{ old('adjusted_quantity', $item->adjusted_quantity ?? $item->predicted_quantity) }}"
                                                class="h-9 w-24 rounded-lg border border-[#dfe3e8] px-2 text-xs outline-none focus:border-brand">
                                            <input type="text" name="adjustment_note" maxlength="500"
                                                placeholder="Alasan (opsional)"
                                                value="{{ old('adjustment_note', $item->adjustment_note) }}"
                                                class="h-9 w-40 rounded-lg border border-[#dfe3e8] px-2 text-xs outline-none focus:border-brand">
                                            <button type="submit"
                                                class="h-9 rounded-lg bg-brand px-3 text-xs font-bold text-white transition hover:bg-brand-hover">
                                                Simpan
                                            </button>
                                        </form>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-center text-xs text-slate-500">
                                    Belum ada penjualan berbasis menu pada periode historis ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-card">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-bold text-[#142d70]">Estimasi Kebutuhan Bahan</h3>
                <p class="mt-0.5 text-[11px] text-slate-500">Porsi rekomendasi dikali takaran resep per porsi.</p>
            </div>
            @if ($estimate['missing_recipes'] !== [])
                <div class="mx-5 mt-4 rounded-xl border border-[#fde68a] bg-[#fffbeb] px-4 py-3 text-xs text-[#92400e]">
                    Resep belum tersedia untuk: {{ implode(', ', $estimate['missing_recipes']) }}. Bahan untuk menu tersebut tidak dihitung.
                </div>
            @endif
            @if ($estimate['items'] === [])
                <div class="px-5 py-8">
                    <x-empty-state icon="package" title="Belum ada estimasi bahan"
                        description="Estimasi muncul setelah resep tersedia untuk menu yang diprediksi." />
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-bold">Bahan</th>
                                <th class="px-5 py-3 text-right font-bold">Kebutuhan</th>
                                <th class="px-5 py-3 font-bold">Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($estimate['items'] as $ingredient)
                                <tr>
                                    <td class="px-5 py-3 font-semibold text-[#172033]">{{ $ingredient['name'] }}</td>
                                    <td class="px-5 py-3 text-right font-bold text-[#142d70]">
                                        {{ number_format((float) $ingredient['quantity'], 1, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3 text-xs text-slate-500">{{ $ingredient['unit'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
@endsection
