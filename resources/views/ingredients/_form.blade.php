@php
    $isEdit = isset($ingredient);
    $fieldId = $fieldId ?? ($isEdit ? 'edit-'.$ingredient->id : 'create');
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label for="name-{{ $fieldId }}" class="mb-1.5 block text-xs font-bold text-[#374151]">Nama Bahan Baku</label>
        <input id="name-{{ $fieldId }}" name="name" type="text" value="{{ old('name', $ingredient->name ?? '') }}" placeholder="contoh: Daging Ayam" required
            class="h-12 w-full rounded-[10px] border border-line-input bg-white px-3.5 text-sm text-[#4b5563] outline-none transition focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe] @error('name') border-danger @enderror">
        @error('name')
            <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="base_unit-{{ $fieldId }}" class="mb-1.5 block text-xs font-bold text-[#374151]">Satuan Dasar</label>
        <input id="base_unit-{{ $fieldId }}" name="base_unit" type="text" list="satuan-bahan-{{ $fieldId }}" value="{{ old('base_unit', $ingredient->base_unit ?? '') }}" placeholder="contoh: gram" required
            class="h-12 w-full rounded-[10px] border border-line-input bg-white px-3.5 text-sm text-[#4b5563] outline-none transition focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe] @error('base_unit') border-danger @enderror">
        <datalist id="satuan-bahan-{{ $fieldId }}">
            @foreach ($units as $unit)
                <option value="{{ $unit }}"></option>
            @endforeach
        </datalist>
        @error('base_unit')
            <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
        @enderror
    </div>

    @unless ($isEdit)
        <div>
            <label for="current_stock-{{ $fieldId }}" class="mb-1.5 block text-xs font-bold text-[#374151]">Stok Awal</label>
            <input id="current_stock-{{ $fieldId }}" name="current_stock" type="number" step="0.001" min="0" value="{{ old('current_stock', '0') }}" required
                class="h-12 w-full rounded-[10px] border border-line-input bg-white px-3.5 text-sm text-[#4b5563] outline-none transition focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe] @error('current_stock') border-danger @enderror">
            @error('current_stock')
                <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
            @enderror
        </div>
    @endunless

    <div>
        <label for="minimum_stock-{{ $fieldId }}" class="mb-1.5 block text-xs font-bold text-[#374151]">Stok Minimum</label>
        <input id="minimum_stock-{{ $fieldId }}" name="minimum_stock" type="number" step="0.001" min="0" value="{{ old('minimum_stock', $ingredient->minimum_stock ?? '0') }}" required
            class="h-12 w-full rounded-[10px] border border-line-input bg-white px-3.5 text-sm text-[#4b5563] outline-none transition focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe] @error('minimum_stock') border-danger @enderror">
        @error('minimum_stock')
            <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="average_cost-{{ $fieldId }}" class="mb-1.5 block text-xs font-bold text-[#374151]">Harga Modal per Satuan (Rp)</label>
        <input id="average_cost-{{ $fieldId }}" name="average_cost" type="number" step="0.01" min="0" value="{{ old('average_cost', $ingredient->average_cost ?? '0') }}" required
            class="h-12 w-full rounded-[10px] border border-line-input bg-white px-3.5 text-sm text-[#4b5563] outline-none transition focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe] @error('average_cost') border-danger @enderror">
        @error('average_cost')
            <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
        @enderror
    </div>
</div>
