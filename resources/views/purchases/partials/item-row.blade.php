<div class="purchase-item-row grid gap-3 rounded-xl border border-line bg-[#f8fafc] p-3 sm:grid-cols-[2fr_1fr_0.8fr_1fr_auto]">
    <div>
        <label class="mb-1 block text-[11px] font-bold text-[#374151]">Bahan Baku</label>
        <select name="items[{{ $index }}][ingredient_id]" class="purchase-ingredient h-11 w-full rounded-lg border border-line-input bg-white px-2.5 text-xs outline-none focus:border-brand" required>
            <option value="">Pilih bahan</option>
            @foreach ($ingredients as $ingredient)
                <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->base_unit }}" @selected(old('items.'.$index.'.ingredient_id') == $ingredient->id)>{{ $ingredient->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-[11px] font-bold text-[#374151]">Jumlah</label>
        <input type="number" name="items[{{ $index }}][quantity]" step="0.001" min="0.001" value="{{ old('items.'.$index.'.quantity') }}" class="h-11 w-full rounded-lg border border-line-input bg-white px-2.5 text-xs outline-none focus:border-brand" required>
    </div>
    <div>
        <label class="mb-1 block text-[11px] font-bold text-[#374151]">Satuan</label>
        <input type="text" name="items[{{ $index }}][unit]" value="{{ old('items.'.$index.'.unit', $ingredients->first()?->base_unit ?? '') }}" class="purchase-unit h-11 w-full rounded-lg border border-line-input bg-[#f1f5f9] px-2.5 text-xs text-slate-600 outline-none" readonly required>
    </div>
    <div>
        <label class="mb-1 block text-[11px] font-bold text-[#374151]">Harga Satuan (Rp)</label>
        <input type="number" name="items[{{ $index }}][unit_cost]" step="0.01" min="0.01" value="{{ old('items.'.$index.'.unit_cost') }}" class="h-11 w-full rounded-lg border border-line-input bg-white px-2.5 text-xs outline-none focus:border-brand" required>
    </div>
    <div class="flex items-end">
        <button type="button" class="remove-purchase-item flex h-11 w-11 items-center justify-center rounded-lg border border-line text-danger transition hover:bg-danger-soft" aria-label="Hapus item">
            <x-icon name="trash" :size="16" />
        </button>
    </div>
</div>
