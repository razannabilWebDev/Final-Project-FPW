@extends('layouts.app')

@section('title', 'Pengelolaan Menu')

@section('content')
    @php
        $formatRupiah = fn ($amount): string => 'Rp'.number_format((float) $amount, 0, ',', '.');
    @endphp

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-[#142d70]">Daftar Menu</h2>
            <p class="mt-1 text-xs text-slate-500">Kelola produk makanan dan minuman beserta harga jualnya.</p>
        </div>
        @can('manage-menus')
            <a href="{{ route('menus.create') }}"
                class="rounded-lg bg-[#2563eb] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#1d4ed8]">+ Tambah Menu</a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-2xl border border-[#ecece8] bg-white shadow-[0_8px_24px_rgba(30,58,138,0.06)]">
        @forelse ($menus as $menu)
            @if ($loop->first)
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] text-left text-xs">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Foto</th>
                                <th class="px-5 py-3 font-semibold">Nama Menu</th>
                                <th class="px-5 py-3 font-semibold">Harga Jual</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0f1f2]">
            @endif

            <tr class="hover:bg-slate-50/60">
                <td class="px-5 py-3">
                    @if ($menu->image_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($menu->image_path) }}"
                            alt="Foto {{ $menu->name }}" class="h-12 w-12 rounded-lg object-cover">
                    @else
                        <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-[11px] font-bold text-slate-400">Foto</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <p class="font-semibold text-slate-700">{{ $menu->name }}</p>
                    @if ($menu->description)
                        <p class="mt-0.5 max-w-xs truncate text-[11px] text-slate-400">{{ $menu->description }}</p>
                    @endif
                </td>
                <td class="px-5 py-3.5 font-semibold text-[#1d4ed8]">{{ $formatRupiah($menu->selling_price) }}</td>
                <td class="px-5 py-3.5">
                    @if ($menu->is_active)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-bold text-green-700"><span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>Aktif</span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-700"><span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>Nonaktif</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('menus.show', $menu) }}" class="rounded-lg border border-[#dfe3e8] px-3 py-1.5 text-[11px] font-bold text-slate-600 hover:bg-slate-50">Detail</a>
                        @can('manage-menus')
                            <a href="{{ route('menus.edit', $menu) }}" class="rounded-lg border border-[#dfe3e8] px-3 py-1.5 text-[11px] font-bold text-slate-600 hover:bg-slate-50">Ubah</a>
                            <form method="POST" action="{{ route('menus.status', $menu) }}"
                                onsubmit="return confirm('{{ $menu->is_active ? 'Nonaktifkan menu ini?' : 'Aktifkan kembali menu ini?' }}')">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_active" value="{{ $menu->is_active ? 1 : 0 }}">
                                <button type="submit"
                                    class="rounded-lg px-3 py-1.5 text-[11px] font-bold {{ $menu->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                    {{ $menu->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </td>
            </tr>

            @if ($loop->last)
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-[#f0f1f2] px-5 py-4">{{ $menus->links() }}</div>
            @endif
        @empty
            <div class="px-5 py-12 text-center">
                <p class="text-sm font-semibold text-slate-600">Belum ada menu</p>
                <p class="mt-1 text-xs text-slate-400">Tambahkan menu pertama untuk mulai mengelola produk usaha.</p>
            </div>
        @endforelse
    </div>
@endsection
