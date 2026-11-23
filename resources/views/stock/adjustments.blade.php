@extends('layouts.app')

@section('title', 'Penyesuaian Stok Fisik')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-[#142d70]">Penyesuaian Stok Fisik</h2>
            <p class="mt-1 text-xs text-slate-500">Catat selisih antara stok sistem dan hasil pemeriksaan fisik.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @can('manage-stock')
                @if (Route::has('stock.movements'))
                    <a href="{{ route('stock.movements') }}" class="rounded-lg border border-[#dfe3e8] px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50">Riwayat Pergerakan</a>
                @endif
            @endcan
            @can('record-waste')
                @if (Route::has('stock.waste.create'))
                    <a href="{{ route('stock.waste.create') }}" class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-2.5 text-xs font-bold text-amber-700 hover:bg-amber-100">Catat Susut/Rusak</a>
                @endif
            @endcan
        </div>
    </div>

    <div class="mx-auto max-w-2xl rounded-2xl border border-[#ecece8] bg-white p-6 shadow-[0_8px_24px_rgba(30,58,138,0.06)] sm:p-8">
        <h3 class="text-sm font-bold text-[#142d70]">Form Penyesuaian</h3>

        <form method="POST" action="{{ route('stock.adjustments.store') }}" class="mt-5" onsubmit="return confirm('Simpan penyesuaian stok ini?')">
            @csrf
            <div class="grid gap-5">
                <div>
                    <label for="ingredient_id" class="mb-2 block text-xs font-bold text-slate-700">Bahan Baku</label>
                    <select id="ingredient_id" name="ingredient_id" required
                        class="w-full rounded-lg border border-[#dfe3e8] bg-white px-4 py-3 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('ingredient_id') border-red-400 @enderror">
                        <option value="">Pilih bahan</option>
                        @foreach ($ingredients as $ingredient)
                            <option value="{{ $ingredient->id }}" @selected(old('ingredient_id') == $ingredient->id)>{{ $ingredient->name }} — stok sistem {{ number_format((float) $ingredient->current_stock, 3, ',', '.') }} {{ $ingredient->base_unit }}</option>
                        @endforeach
                    </select>
                    @error('ingredient_id')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="physical_quantity" class="mb-2 block text-xs font-bold text-slate-700">Jumlah Stok Fisik</label>
                    <input id="physical_quantity" name="physical_quantity" type="number" step="0.001" min="0" value="{{ old('physical_quantity') }}" required
                        class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('physical_quantity') border-red-400 @enderror">
                    @error('physical_quantity')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reason" class="mb-2 block text-xs font-bold text-slate-700">Alasan Penyesuaian</label>
                    <input id="reason" name="reason" type="text" maxlength="255" value="{{ old('reason') }}" placeholder="contoh: hasil hitung fisik harian" required
                        class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('reason') border-red-400 @enderror">
                    @error('reason')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-7 flex flex-wrap gap-3">
                <button type="submit" class="rounded-lg bg-[#2563eb] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#1d4ed8]">Simpan Penyesuaian</button>
                <a href="{{ route('ingredients.index') }}" class="rounded-lg border border-[#dfe3e8] px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50">Kembali</a>
            </div>
        </form>
    </div>
@endsection
