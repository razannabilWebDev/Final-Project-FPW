@php
    $isEdit = $isEdit ?? false;
    $fieldPrefix = $fieldPrefix ?? 'menu';

    $inputClass = 'h-12 w-full rounded-[10px] border border-line-input bg-white px-4 text-sm text-[#1f2937] outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe]';
    $errorClass = 'border-danger focus:border-danger';
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="{{ $fieldPrefix }}-name" class="mb-2 block text-xs font-bold text-[#374151]">Nama Menu</label>
        <input id="{{ $fieldPrefix }}-name" name="name" type="text" value="{{ old('name', $menu->name ?? '') }}" autocomplete="off"
               placeholder="Masukkan nama menu" required
               class="{{ $inputClass }} @error('name') {{ $errorClass }} @enderror">
        @error('name')
            <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="{{ $fieldPrefix }}-description" class="mb-2 block text-xs font-bold text-[#374151]">Keterangan (opsional)</label>
        <textarea id="{{ $fieldPrefix }}-description" name="description" rows="3" placeholder="Tambahkan keterangan atau catatan..."
                  class="min-h-[96px] w-full rounded-[10px] border border-line-input bg-white px-4 py-3 text-sm text-[#1f2937] outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe] @error('description') {{ $errorClass }} @enderror">{{ old('description', $menu->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="{{ $fieldPrefix }}-selling_price" class="mb-2 block text-xs font-bold text-[#374151]">Harga Jual (Rp)</label>
        <input id="{{ $fieldPrefix }}-selling_price" name="selling_price" type="number" step="0.01" min="0"
               value="{{ old('selling_price', $menu->selling_price ?? '') }}" required inputmode="decimal"
               placeholder="0"
               class="{{ $inputClass }} @error('selling_price') {{ $errorClass }} @enderror">
        @error('selling_price')
            <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="{{ $fieldPrefix }}-image" class="mb-2 block text-xs font-bold text-[#374151]">Foto Menu (opsional)</label>
        <div class="rounded-[10px] border border-dashed border-[#b9c5d3] bg-white px-4 py-3 text-center">
            <x-icon name="upload" :size="20" class="mx-auto text-slate-400" />
            <p class="mt-1 text-[11px] font-semibold text-[#4b5563]">Unggah foto menu</p>
            <p class="mt-0.5 text-[11px] text-slate-400">PNG, JPG, atau WebP hingga 2 MB</p>
            <input id="{{ $fieldPrefix }}-image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
                   aria-label="Foto menu"
                   class="mt-3 block w-full text-[11px] text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-[#dbeafe] file:px-3 file:py-2 file:text-[11px] file:font-bold file:text-brand hover:file:bg-[#bfdbfe] @error('image') text-danger @enderror">
        </div>
        @error('image')
            <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
        @enderror
    </div>

    @if ($isEdit && $menu->image_path)
        <div class="sm:col-span-2">
            <p class="mb-2 text-xs font-bold text-[#374151]">Foto Saat Ini</p>
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($menu->image_path) }}"
                 alt="Foto {{ $menu->name }}" class="h-24 w-24 rounded-lg border border-line object-cover">
            <p class="mt-1.5 text-[11px] text-slate-400">Unggah foto baru untuk menggantinya.</p>
        </div>
    @endif
</div>
