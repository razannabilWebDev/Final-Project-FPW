@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $cards = [
            ['label' => 'Kelola Pengguna', 'description' => 'Tambah, ubah, dan nonaktifkan akun Pengelola serta Staf.', 'route' => 'users.index', 'roles' => ['admin']],
            ['label' => 'Pengelolaan Menu', 'description' => 'Lihat daftar menu yang tersedia di usaha.', 'route' => 'menus.index', 'roles' => null],
            ['label' => 'Pengelolaan Resep', 'description' => 'Lihat resep dan takaran bahan per porsi.', 'route' => 'recipes.index', 'roles' => ['admin', 'pengelola']],
            ['label' => 'Stok Bahan Baku', 'description' => 'Pantau persediaan bahan dan pergerakannya.', 'route' => 'ingredients.index', 'roles' => null],
            ['label' => 'Pembelian Bahan Baku', 'description' => 'Input nota dan verifikasi pembelian.', 'route' => 'purchases.index', 'roles' => ['admin', 'pengelola']],
            ['label' => 'Pencatatan Penjualan', 'description' => 'Catat pesanan baru dan pantau transaksi.', 'route' => 'sales.index', 'roles' => null],
            ['label' => 'Biaya Operasional', 'description' => 'Catat pengeluaran dan kategori biaya.', 'route' => 'expenses.index', 'roles' => ['admin']],
            ['label' => 'Laporan Keuntungan', 'description' => 'Lihat pendapatan, HPP, dan laba usaha.', 'route' => 'reports.profit', 'roles' => ['admin']],
            ['label' => 'Rekomendasi Produksi', 'description' => 'Lihat estimasi jumlah porsi harian.', 'route' => 'forecast.index', 'roles' => null],
        ];
    @endphp

    <div class="mb-7 rounded-2xl bg-gradient-to-br from-[#153577] to-[#2864cc] p-7 text-white shadow-[0_8px_24px_rgba(30,58,138,0.12)] sm:p-9">
        <p class="mb-2 text-[11px] font-bold uppercase tracking-[0.14em] text-blue-200">Dashboard {{ $user->role->label() }}</p>
        <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Halo, {{ $user->name }}!</h2>
        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-blue-100">Selamat datang di SmartFood Home. Pilih modul di bawah untuk mulai mengelola operasional usaha Anda.</p>
    </div>

    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-bold text-[#142d70]">Akses Modul</h3>
        <span class="text-[11px] text-slate-500">Menyesuaikan role akun Anda</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($cards as $card)
            @if ($card['roles'] === null || in_array($user->role->value, $card['roles'], true))
                <div class="rounded-2xl border border-[#ecece8] bg-white p-5 shadow-[0_8px_24px_rgba(30,58,138,0.06)]">
                    <h4 class="text-sm font-bold text-[#142d70]">{{ $card['label'] }}</h4>
                    <p class="mt-1.5 min-h-[40px] text-xs leading-relaxed text-slate-500">{{ $card['description'] }}</p>
                    <div class="mt-4">
                        @if (Route::has($card['route']))
                            <a href="{{ route($card['route']) }}" class="text-xs font-bold text-[#2563eb] hover:text-[#1d4ed8]">Buka modul &rarr;</a>
                        @else
                            <span class="text-[11px] font-semibold text-slate-400">Modul belum tersedia</span>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endsection
