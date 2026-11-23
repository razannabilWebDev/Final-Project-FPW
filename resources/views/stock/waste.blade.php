@extends('layouts.app')

@section('title', 'Stok Susut/Rusak')

@section('content')
    <div class="mx-auto max-w-2xl rounded-2xl border border-[#ecece8] bg-white p-6 shadow-[0_8px_24px_rgba(30,58,138,0.06)] sm:p-8">
        <h2 class="text-lg font-bold text-[#142d70]">Catat Stok Susut/Rusak</h2>
        <p class="mt-1 text-xs text-slate-500">Kuantitas tidak boleh melebihi stok yang tersedia.</p>

        <form method="POST" action="{{ route('stock.waste.store') }}" class="mt-6" onsubmit="return confirm('Catat stok susut/rusak ini?')">
            @csrf
            <div class="grid gap-5">
                <div>
                    <label for="ingredient_id" class="mb-2 block text-xs font-bold text-slate-700">Bahan Baku</label>
                    <select id="ingredient_id" name="ingredient_id" required
                        class="w-full rounded-lg border border-[#dfe3e8] bg-white px-4 py-3 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('ingredient_id') border-red-400 @enderror">
                        <option value="">Pilih bahan</option>
                        @foreach ($ingredients as $ingredient)
                            <option value="{{ $ingredient->id }}" @selected(old('ingredient_id', request('ingredient_id')) == $ingredient->id)>{{ $ingredient->name }} — stok {{ number_format((float) $ingredient->current_stock, 3, ',', '.') }} {{ $ingredient->base_unit }}</option>
                        @endforeach
                    </select>
                    @error('ingredient_id')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="mb-2 block text-xs font-bold text-slate-700">Jumlah Susut/Rusak</label>
                    <input id="quantity" name="quantity" type="number" step="0.001" min="0.001" value="{{ old('quantity') }}" required
                        class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('quantity') border-red-400 @enderror">
                    @error('quantity')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reason" class="mb-2 block text-xs font-bold text-slate-700">Alasan</label>
                    <input id="reason" name="reason" type="text" maxlength="255" value="{{ old('reason') }}" placeholder="contoh: bahan busuk saat pemeriksaan" required
                        class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('reason') border-red-400 @enderror">
                    @error('reason')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-7 flex flex-wrap gap-3">
                <button type="submit" class="rounded-lg bg-[#dc2626] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#b91c1c]">Simpan Catatan</button>
                <a href="{{ route('ingredients.index') }}" class="rounded-lg border border-[#dfe3e8] px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50">Kembali</a>
            </div>
        </form>
    </div>
@endsection
