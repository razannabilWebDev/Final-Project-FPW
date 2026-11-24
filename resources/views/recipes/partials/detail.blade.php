@php
    $rupiah = fn ($value): string => \App\Support\Rupiah::format($value);
    $formatQuantity = function ($value): string {
        $value = (string) ($value ?? '');

        return str_contains($value, '.') ? rtrim(rtrim($value, '0'), '.') : $value;
    };
@endphp

<div class="overflow-hidden rounded-xl border border-line bg-[#f1f5f9]">
    @if ($recipe->menu?->image_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($recipe->menu->image_path) }}"
             alt="Foto {{ $recipe->menu->name }}" class="h-44 w-full object-cover"
             onerror="this.style.display='none';this.nextElementSibling.classList.remove('hidden')">
        <div class="hidden h-44 flex-col items-center justify-center gap-2 text-slate-400">
            <x-icon name="chef" :size="30" />
        </div>
    @else
        <div class="flex h-44 flex-col items-center justify-center gap-2 text-slate-400">
            <x-icon name="chef" :size="30" />
            <span class="text-[11px] font-semibold">Foto menu belum diunggah</span>
        </div>
    @endif
</div>

<dl class="mt-5 grid gap-4 sm:grid-cols-2">
    <div>
        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Nama Menu</dt>
        <dd class="mt-1 text-sm font-bold text-[#1f2937]">{{ $recipe->menu?->name ?? '-' }}</dd>
    </div>
    <div>
        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Versi Resep</dt>
        <dd class="mt-1"><x-badge tone="info">Versi {{ $recipe->version }}</x-badge></dd>
    </div>
    @can('manage-recipe-costs')
        <div>
            <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Total Modal / Porsi</dt>
            <dd class="mt-1 text-base font-extrabold text-[#d97706]">{{ $rupiah($cost) }}</dd>
        </div>
    @endcan
    <div>
        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Harga Jual</dt>
        <dd class="mt-1 text-sm font-bold text-brand">{{ $rupiah($recipe->menu?->selling_price ?? 0) }}</dd>
    </div>
    <div>
        <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Terakhir Diperbarui</dt>
        <dd class="mt-1 text-sm font-semibold text-slate-600">{{ $recipe->updated_at->translatedFormat('d F Y') }}</dd>
    </div>
</dl>

@if ($recipe->notes)
    <div class="mt-4 rounded-lg border border-line bg-white px-3 py-2.5">
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Keterangan</p>
        <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $recipe->notes }}</p>
    </div>
@endif

<div class="mt-5">
    <p class="mb-2 text-xs font-bold text-[#142d70]">Komposisi Bahan</p>
    <div class="overflow-x-auto rounded-lg border border-line">
        <table class="w-full min-w-[420px] text-left text-xs">
            <thead class="bg-head-bg text-[11px] uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-3 py-2">Bahan</th>
                    <th class="px-3 py-2">Takaran</th>
                    @can('manage-recipe-costs')
                        <th class="px-3 py-2 text-right">Harga Modal</th>
                        <th class="px-3 py-2 text-right">Subtotal</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse ($detailedItems as $item)
                    <tr class="border-t border-line">
                        <td class="px-3 py-2 font-semibold text-[#1f2937]">{{ $item['name'] }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $formatQuantity($item['quantity']) }} {{ $item['unit'] }}</td>
                        @can('manage-recipe-costs')
                            <td class="px-3 py-2 text-right text-slate-500">{{ $rupiah($item['average_cost']) }}</td>
                            <td class="px-3 py-2 text-right font-bold text-[#4b5563]">{{ $rupiah($item['subtotal']) }}</td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-6 text-center text-xs text-slate-500">Belum ada bahan pada resep ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="mt-2 text-[11px] leading-relaxed text-slate-400">
        HPP dihitung dari takaran (satuan dasar bahan) × harga modal rata-rata bahan (D4). Perubahan resep tidak mengubah HPP transaksi historis.
    </p>
</div>
