@php
    $rupiah = fn ($amount): string => 'Rp'.number_format((float) $amount, 0, ',', '.');
@endphp

<div class="overflow-hidden rounded-xl border border-line bg-[#f1f5f9]">
    @if ($menu->image_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($menu->image_path) }}"
             alt="Foto {{ $menu->name }}" class="h-52 w-full object-cover"
             onerror="this.style.display='none';this.nextElementSibling.classList.remove('hidden')">
        <div class="hidden h-52 flex-col items-center justify-center gap-2 text-slate-400">
            <x-icon name="chef" :size="32" />
            <span class="text-[11px] font-semibold">Foto menu belum tersedia</span>
        </div>
    @else
        <div class="flex h-52 flex-col items-center justify-center gap-2 text-slate-400">
            <x-icon name="chef" :size="32" />
            <span class="text-[11px] font-semibold">Foto menu belum diunggah</span>
        </div>
    @endif
</div>

<dl class="mt-5 grid gap-4 sm:grid-cols-2">
    <div>
        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Nama</dt>
        <dd class="mt-1 text-sm font-bold text-[#1f2937]">{{ $menu->name }}</dd>
    </div>
    <div>
        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Status</dt>
        <dd class="mt-1">
            <x-badge :tone="$menu->is_active ? 'success' : 'danger'">
                {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
            </x-badge>
        </dd>
    </div>
    <div>
        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Harga Jual</dt>
        <dd class="mt-1 text-base font-extrabold text-brand">{{ $rupiah($menu->selling_price) }}</dd>
    </div>
    <div>
        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Terakhir Diperbarui</dt>
        <dd class="mt-1 text-sm font-semibold text-slate-600">{{ $menu->updated_at->translatedFormat('d F Y') }}</dd>
    </div>
    @if ($menu->description)
        <div class="sm:col-span-2">
            <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Keterangan</dt>
            <dd class="mt-1 text-sm leading-relaxed text-slate-600">{{ $menu->description }}</dd>
        </div>
    @endif
</dl>
