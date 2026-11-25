<div data-recipe-row data-index="{{ $index }}"
     class="grid grid-cols-[1fr_110px_90px_32px] items-start gap-2 rounded-lg border border-line bg-white p-2.5">
    <div>
        <select name="items[{{ $index }}][ingredient_id]" data-ingredient-select aria-label="Bahan"
                class="h-10 w-full rounded-lg border border-line-input bg-white px-2 text-xs text-[#1f2937] outline-none transition focus:border-brand focus:ring-4 focus:ring-[#dbeafe] @error("items.$index.ingredient_id") border-danger @enderror">
            <option value="">Pilih bahan</option>
            @foreach ($ingredients as $ingredient)
                <option value="{{ $ingredient->id }}" @selected((string) ($row['ingredient_id'] ?? '') === (string) $ingredient->id)>
                    {{ $ingredient->name }}
                </option>
            @endforeach
        </select>
        @error("items.$index.ingredient_id")
            <p class="mt-1 text-[11px] font-medium text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <input type="number" name="items[{{ $index }}][quantity]" step="0.001" min="0.001"
               value="{{ old("items.$index.quantity", $row['quantity'] ?? '') }}" placeholder="Takaran"
               aria-label="Takaran"
               class="h-10 w-full rounded-lg border border-line-input bg-white px-2 text-xs text-[#1f2937] outline-none transition focus:border-brand focus:ring-4 focus:ring-[#dbeafe] @error("items.$index.quantity") border-danger @enderror">
        @error("items.$index.quantity")
            <p class="mt-1 text-[11px] font-medium text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <select name="items[{{ $index }}][unit]" data-unit-input aria-label="Satuan"
                class="h-10 w-full rounded-lg border border-line-input bg-white px-2 text-center text-xs font-semibold text-[#4b5563] outline-none transition focus:border-brand focus:ring-4 focus:ring-[#dbeafe] @error("items.$index.unit") border-danger @enderror">
            <option value="">Satuan</option>
            @foreach ($units as $unit)
                <option value="{{ $unit }}" @selected((string) old("items.$index.unit", $row['unit'] ?? '') === (string) $unit)>
                    {{ $unit }}
                </option>
            @endforeach
        </select>
        @error("items.$index.unit")
            <p class="mt-1 text-[11px] font-medium text-danger">{{ $message }}</p>
        @enderror
    </div>

    <button type="button" data-remove-item aria-label="Hapus bahan"
            class="mt-1 flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-danger-soft hover:text-danger">
        <x-icon name="x" :size="15" />
    </button>
</div>
