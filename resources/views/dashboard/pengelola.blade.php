@extends('layouts.app')

@section('title', 'Dashboard Pengelola')

@section('content')
    <div class="relative mb-7 overflow-hidden rounded-2xl bg-gradient-to-r from-[#153577] to-[#2864cc] p-7 text-white shadow-card">
        <div class="pointer-events-none absolute -right-16 -top-20 h-72 w-72 rounded-full border border-white/10"></div>
        <div class="relative">
            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-blue-200">Dashboard Pengelola</p>
            <h2 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">Halo, {{ $user->name }}!</h2>
            <p class="mt-2 max-w-xl text-sm text-blue-100">Pantau aktivitas penjualan, pesanan masuk, pembelian, stok, dan estimasi produksi usaha.</p>
        </div>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Transaksi" :value="number_format($summary['salesCount'], 0, ',', '.')" icon="receipt" tone="blue" hint="Seluruh transaksi tercatat" />
        <x-stat-card label="Pesanan Masuk" :value="number_format($summary['salesPending'], 0, ',', '.')" icon="cart" tone="orange" hint="Menunggu diproses" />
        <x-stat-card label="Pembelian Perlu Diverifikasi" :value="number_format($summary['purchasesPending'], 0, ',', '.')" icon="package" tone="purple" hint="Nota menunggu verifikasi" />
        <x-stat-card label="Bahan Stok Menipis" :value="number_format($summary['lowStock']->count(), 0, ',', '.')" icon="box" tone="red" hint="Perlu perhatian" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- Aktivitas penjualan --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-[#142d70]">Aktivitas Penjualan</h3>
                    <p class="mt-0.5 text-[11px] text-slate-500">Ringkasan pesanan masuk dan status transaksi</p>
                </div>
                @if (Route::has('sales.index'))
                    <a href="{{ route('sales.index') }}" class="text-[11px] font-bold text-brand hover:text-brand-hover">Lihat semua</a>
                @endif
            </div>
            @if ($summary['salesCount'] > 0)
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-line bg-[#f8fafc] p-4">
                        <p class="text-[11px] text-slate-500">Sedang diproses</p>
                        <p class="mt-1 text-xl font-extrabold text-[#d97706]">{{ number_format($summary['salesProcessing'], 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-xl border border-line bg-[#f8fafc] p-4">
                        <p class="text-[11px] text-slate-500">Menunggu</p>
                        <p class="mt-1 text-xl font-extrabold text-[#142d70]">{{ number_format($summary['salesPending'], 0, ',', '.') }}</p>
                    </div>
                </div>
            @else
                <x-empty-state icon="chart" title="Belum ada aktivitas" description="Aktivitas penjualan akan tampil setelah pesanan dicatat." />
            @endif
        </div>

        {{-- Estimasi produksi --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <h3 class="text-sm font-bold text-[#142d70]">Estimasi Produksi Harian</h3>
            <p class="mt-0.5 text-[11px] text-slate-500">Jumlah porsi yang disarankan</p>
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
        {{-- Status pembelian --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#142d70]">Status Pembelian</h3>
                @if (Route::has('purchases.index'))
                    <a href="{{ route('purchases.index') }}" class="text-[11px] font-bold text-brand hover:text-brand-hover">Lihat semua</a>
                @endif
            </div>
            @forelse ($summary['purchasesVerification'] as $purchase)
                <div class="flex items-center justify-between border-b border-line py-2.5 last:border-0">
                    <div>
                        <p class="text-xs font-bold text-[#374151]">{{ $purchase->purchase_code }}</p>
                        <p class="text-[10px] text-slate-500">{{ $purchase->supplier_name ?? '-' }} · {{ $purchase->purchase_date_label }}</p>
                    </div>
                    @php
                        $tone = match ($purchase->status) { 'approved' => 'bg-[#dcfce7] text-[#15803d]', 'rejected' => 'bg-[#fee2e2] text-[#b91c1c]', default => 'bg-[#fef3c7] text-[#b45309]' };
                        $label = match ($purchase->status) { 'approved' => 'Disetujui', 'rejected' => 'Ditolak', default => 'Menunggu' };
                    @endphp
                    <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $tone }}">{{ $label }}</span>
                </div>
            @empty
                <x-empty-state icon="package" title="Belum ada pembelian" description="Riwayat pembelian akan tampil di sini." />
            @endforelse
        </div>

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
                    <div>
                        <p class="text-xs font-bold text-[#374151]">{{ $menu->name }}</p>
                        <p class="text-[10px] text-slate-500">Rp{{ number_format((float) $menu->selling_price, 0, ',', '.') }}</p>
                    </div>
                    <span class="rounded-full bg-[#dcfce7] px-2.5 py-1 text-[10px] font-bold text-[#15803d]">Tersedia</span>
                </div>
            @empty
                <x-empty-state icon="chef" title="Belum ada menu" description="Menu yang ditambahkan Admin akan tampil di sini." />
            @endforelse
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-line bg-white p-5 shadow-card">
        <h3 class="mb-4 text-sm font-bold text-[#142d70]">Kondisi Stok Bahan</h3>
        @forelse ($summary['lowStock'] as $item)
            <div class="flex items-center justify-between border-b border-line py-2.5 last:border-0">
                <span class="text-xs font-semibold text-[#374151]">{{ $item->name }}</span>
                <span class="rounded-full bg-[#fef3c7] px-2.5 py-1 text-[10px] font-bold text-[#b45309]">{{ number_format((float) $item->current_stock, 1, ',', '.') }} {{ $item->base_unit }}</span>
            </div>
        @empty
            <x-empty-state icon="box" title="Stok dalam kondisi aman" description="Semua bahan berada di atas stok minimum." />
        @endforelse
    </div>
@endsection
