@extends('layouts.app')

@section('title', 'Penyesuaian Stok Fisik')

@section('content')
    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Penyesuaian Stok Fisik</h1>
        <p class="mt-1 text-sm text-slate-500">Catat selisih antara stok sistem dan hasil pemeriksaan fisik.</p>
    </div>

    <div class="mx-auto max-w-3xl rounded-2xl border border-line bg-white p-5 shadow-card sm:p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-line pb-4">
            <div>
                <h2 class="text-sm font-bold text-[#142d70]">Form Penyesuaian</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Selisih akan tercatat sebagai pergerakan stok penyesuaian.</p>
            </div>
            @can('manage-stock')
                @if (Route::has('stock.movements'))
                    <a href="{{ route('stock.movements') }}" class="flex h-10 items-center gap-1.5 rounded-lg border border-line-input bg-white px-3.5 text-xs font-bold text-[#374151] transition hover:bg-slate-50">
                        <x-icon name="clock" :size="15" />
                        Riwayat Pergerakan
                    </a>
                @endif
            @endcan
        </div>

        <form method="POST" action="{{ route('stock.adjustments.store') }}" onsubmit="return confirm('Simpan penyesuaian stok ini?')">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="ingredient_id" class="mb-1.5 block text-xs font-bold text-[#374151]">Bahan Baku</label>
                    <select id="ingredient_id" name="ingredient_id" required
                        class="h-12 w-full rounded-[10px] border border-line-input bg-white px-3.5 text-sm text-[#4b5563] outline-none transition focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe] @error('ingredient_id') border-danger @enderror">
                        <option value="">Pilih bahan</option>
                        @foreach ($ingredients as $ingredient)
                            <option value="{{ $ingredient->id }}" @selected(old('ingredient_id') == $ingredient->id)>{{ $ingredient->name }} — stok sistem {{ number_format((float) $ingredient->current_stock, 3, ',', '.') }} {{ $ingredient->base_unit }}</option>
                        @endforeach
                    </select>
                    @error('ingredient_id')
                        <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="physical_quantity" class="mb-1.5 block text-xs font-bold text-[#374151]">Jumlah Stok Fisik</label>
                    <input id="physical_quantity" name="physical_quantity" type="number" step="0.001" min="0" value="{{ old('physical_quantity') }}" placeholder="contoh: 18.5" required
                        class="h-12 w-full rounded-[10px] border border-line-input bg-white px-3.5 text-sm text-[#4b5563] outline-none transition focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe] @error('physical_quantity') border-danger @enderror">
                    @error('physical_quantity')
                        <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reason" class="mb-1.5 block text-xs font-bold text-[#374151]">Alasan Penyesuaian</label>
                    <input id="reason" name="reason" type="text" maxlength="255" value="{{ old('reason') }}" placeholder="contoh: hasil hitung fisik harian" required
                        class="h-12 w-full rounded-[10px] border border-line-input bg-white px-3.5 text-sm text-[#4b5563] outline-none transition focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe] @error('reason') border-danger @enderror">
                    @error('reason')
                        <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover">
                    <x-icon name="check" :size="14" :stroke="2.4" />
                    Simpan Penyesuaian
                </button>
                <a href="{{ route('ingredients.index') }}" class="flex h-10 items-center rounded-lg border border-line-input bg-white px-4 text-xs font-bold text-[#374151] transition hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
@endsection
