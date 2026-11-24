@php
    $isEdit = isset($menu);
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="name" class="mb-2 block text-xs font-bold text-slate-700">Nama Menu</label>
        <input id="name" name="name" type="text" value="{{ old('name', $menu->name ?? '') }}" autocomplete="off" required
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('name') border-red-400 @enderror">
        @error('name')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="description" class="mb-2 block text-xs font-bold text-slate-700">Deskripsi (opsional)</label>
        <textarea id="description" name="description" rows="3"
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('description') border-red-400 @enderror">{{ old('description', $menu->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="selling_price" class="mb-2 block text-xs font-bold text-slate-700">Harga Jual (Rp)</label>
        <input id="selling_price" name="selling_price" type="number" step="0.01" min="0"
            value="{{ old('selling_price', $menu->selling_price ?? '') }}" required inputmode="decimal"
            class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('selling_price') border-red-400 @enderror">
        @error('selling_price')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="image" class="mb-2 block text-xs font-bold text-slate-700">Foto Menu (opsional)</label>
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
            class="w-full rounded-lg border border-dashed border-[#b9c5d3] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('image') border-red-400 @enderror">
        <p class="mt-1.5 text-[11px] text-slate-400">JPG, PNG, atau WebP, maksimal 2 MB.</p>
        @error('image')
            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @if ($isEdit && $menu->image_path)
        <div class="sm:col-span-2">
            <p class="mb-2 text-xs font-bold text-slate-700">Foto Saat Ini</p>
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($menu->image_path) }}"
                alt="Foto {{ $menu->name }}" class="h-28 w-28 rounded-lg object-cover">
            <p class="mt-1.5 text-[11px] text-slate-400">Unggah foto baru untuk menggantinya.</p>
        </div>
    @endif
</div>
