@php
    $isEdit = $isEdit ?? false;
    $prefix = $prefix ?? 'recipe';

    // Tampilan: 2.000 → 2, 2.500 → 2.5. Penyimpanan tetap decimal(12,3).
    $formatQuantity = function ($value): string {
        $value = (string) ($value ?? '');

        return str_contains($value, '.') ? rtrim(rtrim($value, '0'), '.') : $value;
    };

    $oldItems = old('items');
    if (is_array($oldItems) && $oldItems !== []) {
        $rows = $oldItems;
    } elseif ($isEdit) {
        $rows = $recipe->items->map(fn ($item): array => [
            'ingredient_id' => $item->ingredient_id,
            'quantity' => $formatQuantity($item->quantity),
            'unit' => $item->unit,
        ])->all();
    } else {
        $rows = [['ingredient_id' => '', 'quantity' => '', 'unit' => '']];
    }

    $inputClass = 'h-12 w-full rounded-[10px] border border-line-input bg-white px-4 text-sm text-[#1f2937] outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe]';
@endphp

<div class="grid gap-5">
    @if (! $isEdit)
        <div>
            <label for="{{ $prefix }}-menu" class="mb-2 block text-xs font-bold text-[#374151]">Menu</label>
            <select id="{{ $prefix }}-menu" name="menu_id" required
                    class="{{ $inputClass }} @error('menu_id') border-danger @enderror">
                <option value="">Pilih menu</option>
                @foreach ($menus as $option)
                    <option value="{{ $option->id }}" @selected(old('menu_id') == $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
            @error('menu_id')
                <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <div>
        <label for="{{ $prefix }}-notes" class="mb-2 block text-xs font-bold text-[#374151]">Keterangan (opsional)</label>
        <textarea id="{{ $prefix }}-notes" name="notes" rows="2" placeholder="Tambahkan catatan resep..."
                  class="min-h-[80px] w-full rounded-[10px] border border-line-input bg-white px-4 py-3 text-sm text-[#1f2937] outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe] @error('notes') border-danger @enderror">{{ old('notes', $recipe->notes ?? '') }}</textarea>
        @error('notes')
            <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <p class="mb-2 text-xs font-bold text-[#374151]">Bahan &amp; Takaran per Porsi</p>

        <div data-recipe-items class="space-y-3">
            <div class="flex justify-end">
                <button type="button" data-add-item
                        class="flex items-center gap-1 rounded-lg border border-line-input bg-white px-3 py-1.5 text-[11px] font-bold text-brand transition hover:border-brand">
                    <x-icon name="plus" :size="13" :stroke="2.4" />
                    Tambah Bahan
                </button>
            </div>

            @foreach ($rows as $index => $row)
                @include('recipes.partials.item-row', [
                    'index' => $index,
                    'row' => $row,
                    'ingredients' => $ingredients,
                    'units' => $units,
                    'prefix' => $prefix,
                ])
            @endforeach

            <template data-item-template>
                @include('recipes.partials.item-row', [
                    'index' => '__INDEX__',
                    'row' => ['ingredient_id' => '', 'quantity' => '', 'unit' => ''],
                    'ingredients' => $ingredients,
                    'units' => $units,
                    'prefix' => $prefix,
                ])
            </template>
        </div>

        @error('items')
            <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
        @enderror
    </div>

    @if ($isEdit)
        <p class="rounded-lg border border-[#fde68a] bg-insight px-3 py-2 text-[11px] leading-relaxed text-[#b45309]">
            Versi saat ini: {{ $recipe->version }}. Perubahan takaran atau komposisi akan menyimpan versi baru; riwayat HPP transaksi tidak berubah.
        </p>
    @endif
</div>
