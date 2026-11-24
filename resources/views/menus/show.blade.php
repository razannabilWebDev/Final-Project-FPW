@extends('layouts.app')

@section('title', 'Detail Menu')

@section('content')
    @php
        $formatRupiah = fn ($amount): string => 'Rp'.number_format((float) $amount, 0, ',', '.');
    @endphp

    <div class="mx-auto max-w-2xl rounded-2xl border border-[#ecece8] bg-white p-6 shadow-[0_8px_24px_rgba(30,58,138,0.06)] sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-[#142d70]">{{ $menu->name }}</h2>
                <p class="mt-1 text-xs text-slate-500">Detail produk yang dijual usaha.</p>
            </div>
            @if ($menu->is_active)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-bold text-green-700"><span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>Aktif</span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-700"><span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>Nonaktif</span>
            @endif
        </div>

        <div class="mt-6">
            @if ($menu->image_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($menu->image_path) }}"
                    alt="Foto {{ $menu->name }}" class="h-56 w-full rounded-xl object-cover">
            @else
                <div class="flex h-56 w-full items-center justify-center rounded-xl bg-slate-100 text-sm font-semibold text-slate-400">Foto menu belum diunggah</div>
            @endif
        </div>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Harga Jual</dt>
                <dd class="mt-1 text-base font-bold text-[#1d4ed8]">{{ $formatRupiah($menu->selling_price) }}</dd>
            </div>
            <div>
                <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Status Menu</dt>
                <dd class="mt-1 text-sm font-semibold text-slate-700">{{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Deskripsi</dt>
                <dd class="mt-1 text-sm text-slate-600">{{ $menu->description ?: 'Tidak ada deskripsi.' }}</dd>
            </div>
        </dl>

        <div class="mt-7 flex flex-wrap gap-3">
            <a href="{{ route('menus.index') }}" class="rounded-lg border border-[#dfe3e8] px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50">Kembali</a>
            @can('manage-menus')
                <a href="{{ route('menus.edit', $menu) }}" class="rounded-lg bg-[#2563eb] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#1d4ed8]">Ubah Menu</a>
                <form method="POST" action="{{ route('menus.status', $menu) }}"
                    onsubmit="return confirm('{{ $menu->is_active ? 'Nonaktifkan menu ini?' : 'Aktifkan kembali menu ini?' }}')">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $menu->is_active ? 1 : 0 }}">
                    <button type="submit"
                        class="rounded-lg px-5 py-2.5 text-xs font-bold {{ $menu->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                        {{ $menu->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            @endcan
        </div>
    </div>
@endsection
