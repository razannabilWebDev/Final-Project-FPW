@php
    $isEdit = isset($ingredient);
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="name" class="mb-2 block text-xs font-bold text-slate-700">Nama Bahan</label>
        <input id="name" name="name" type="text" value="{{ old('name', $ingredient->name ?? '') }}" required
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('name') border-red-400 @enderror">
        @error('name')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="base_unit" class="mb-2 block text-xs font-bold text-slate-700">Satuan Dasar</label>
        <input id="base_unit" name="base_unit" type="text" list="satuan-bahan" value="{{ old('base_unit', $ingredient->base_unit ?? '') }}" placeholder="contoh: gram" required
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('base_unit') border-red-400 @enderror">
        <datalist id="satuan-bahan">
            @foreach ($units as $unit)
                <option value="{{ $unit }}"></option>
            @endforeach
        </datalist>
        @error('base_unit')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @unless ($isEdit)
        <div>
            <label for="current_stock" class="mb-2 block text-xs font-bold text-slate-700">Stok Awal</label>
            <input id="current_stock" name="current_stock" type="number" step="0.001" min="0" value="{{ old('current_stock', '0') }}" required
                class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('current_stock') border-red-400 @enderror">
            @error('current_stock')
                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endunless

    <div>
        <label for="minimum_stock" class="mb-2 block text-xs font-bold text-slate-700">Stok Minimum</label>
        <input id="minimum_stock" name="minimum_stock" type="number" step="0.001" min="0" value="{{ old('minimum_stock', $ingredient->minimum_stock ?? '0') }}" required
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('minimum_stock') border-red-400 @enderror">
        @error('minimum_stock')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="average_cost" class="mb-2 block text-xs font-bold text-slate-700">Harga Modal per Satuan (Rp)</label>
        <input id="average_cost" name="average_cost" type="number" step="0.01" min="0" value="{{ old('average_cost', $ingredient->average_cost ?? '0') }}" required
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('average_cost') border-red-400 @enderror">
        @error('average_cost')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
