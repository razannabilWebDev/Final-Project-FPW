@extends('layouts.app')

@section('title', 'Dashboard Staf')

@section('content')
    <div class="relative mb-7 overflow-hidden rounded-2xl bg-gradient-to-r from-[#153577] to-[#2864cc] p-7 text-white shadow-card">
        <div class="pointer-events-none absolute -right-16 -top-20 h-72 w-72 rounded-full border border-white/10"></div>
        <div class="relative">
            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-blue-200">Dashboard Staf Operasional</p>
            <h2 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $greeting }}, {{ $user->name }}!</h2>
            <p class="mt-2 max-w-xl text-sm text-blue-100">Mulai aktivitas harian Anda: catat pesanan, perbarui stok, dan input nota pembelian.</p>
        </div>
    </div>

    {{-- Aksi cepat --}}
    <div class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $actions = [
                ['label' => 'Input Pesanan', 'icon' => 'cart', 'route' => 'sales.index'],
                ['label' => 'Update Stok Fisik', 'icon' => 'box', 'route' => 'ingredients.index'],
                ['label' => 'Input Nota Pembelian', 'icon' => 'receipt', 'route' => 'purchases.index'],
                ['label' => 'Lihat Daftar Menu', 'icon' => 'chef', 'route' => 'menus.index'],
            ];
        @endphp
        @foreach ($actions as $action)
            @if (Route::has($action['route']))
                <a href="{{ route($action['route']) }}"
                    class="flex items-center gap-3 rounded-2xl border border-line bg-white p-4 shadow-card transition hover:-translate-y-px hover:border-brand">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#dbeafe] text-brand">
                        <x-icon :name="$action['icon']" :size="19" />
                    </span>
                    <span class="text-xs font-bold text-[#142d70]">{{ $action['label'] }}</span>
                    <x-icon name="arrow-right" :size="15" class="ml-auto text-slate-400" />
                </a>
            @endif
        @endforeach
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Pesanan Perlu Diproses" :value="number_format($summary['salesPending'], 0, ',', '.')" icon="cart" tone="orange" hint="Menunggu tindak lanjut" />
        <x-stat-card label="Sedang Diproses" :value="number_format($summary['salesProcessing'], 0, ',', '.')" icon="clock" tone="blue" hint="Pesanan aktif" />
        <x-stat-card label="Menu Tersedia" :value="number_format($summary['menusCount'], 0, ',', '.')" icon="chef" tone="green" hint="Dapat dipesan" />
        <x-stat-card label="Bahan Stok Menipis" :value="number_format($summary['lowStock']->count(), 0, ',', '.')" icon="box" tone="red" hint="Perlu diperbarui" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Pesanan perlu diproses --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#142d70]">Pesanan Masuk</h3>
                @if (Route::has('sales.index'))
                    <a href="{{ route('sales.index') }}" class="text-[11px] font-bold text-brand hover:text-brand-hover">Lihat semua</a>
                @endif
            </div>
            @if ($summary['salesCount'] > 0)
                <p class="text-xs text-slate-500">{{ number_format($summary['salesPending'], 0, ',', '.') }} pesanan menunggu dan {{ number_format($summary['salesProcessing'], 0, ',', '.') }} sedang diproses. Buka modul pencatatan penjualan untuk memperbarui status.</p>
            @else
                <x-empty-state icon="cart" title="Belum ada pesanan" description="Pesanan baru akan tampil di sini setelah dicatat." />
            @endif
        </div>

        {{-- Estimasi porsi --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <h3 class="text-sm font-bold text-[#142d70]">Estimasi Porsi Harian</h3>
            <p class="mt-0.5 text-[11px] text-slate-500">Jumlah produksi yang disarankan</p>
            @if ($summary['forecast'])
                <p class="mt-6 text-center text-3xl font-extrabold text-[#142d70]">{{ number_format((float) ($summary['forecast']->predicted_quantity ?? 0), 0, ',', '.') }}</p>
                <p class="mt-1 text-center text-[11px] text-slate-500">total porsi</p>
            @else
                <div class="mt-6">
                    <x-empty-state icon="sparkles" title="Belum ada estimasi" description="Estimasi tampil setelah data penjualan tersedia." />
                </div>
            @endif
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        {{-- Daftar menu --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#142d70]">Daftar Menu</h3>
                @if (Route::has('menus.index'))
                    <a href="{{ route('menus.index') }}" class="text-[11px] font-bold text-brand hover:text-brand-hover">Lihat semua</a>
                @endif
            </div>
            @forelse ($summary['menus'] as $menu)
                <div class="flex items-center justify-between border-b border-line py-2.5 last:border-0">
                    <span class="text-xs font-bold text-[#374151]">{{ $menu->name }}</span>
                    <span class="text-xs font-semibold text-brand">Rp{{ number_format((float) $menu->selling_price, 0, ',', '.') }}</span>
                </div>
            @empty
                <x-empty-state icon="chef" title="Belum ada menu" description="Menu akan tampil setelah ditambahkan Admin." />
            @endforelse
        </div>

        {{-- Stok fisik --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#142d70]">Pembaruan Stok Fisik</h3>
                @if (Route::has('ingredients.index'))
                    <a href="{{ route('ingredients.index') }}" class="text-[11px] font-bold text-brand hover:text-brand-hover">Kelola stok</a>
                @endif
            </div>
            @forelse ($summary['lowStock'] as $item)
                <div class="flex items-center justify-between border-b border-line py-2.5 last:border-0">
                    <span class="text-xs font-semibold text-[#374151]">{{ $item->name }}</span>
                    <span class="rounded-full bg-[#fef3c7] px-2.5 py-1 text-[10px] font-bold text-[#b45309]">{{ number_format((float) $item->current_stock, 1, ',', '.') }} {{ $item->base_unit }}</span>
                </div>
            @empty
                <x-empty-state icon="box" title="Stok aman" description="Semua bahan berada di atas stok minimum." />
            @endforelse
        </div>
    </div>
@endsection
