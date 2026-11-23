<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartFood Home') — SmartFood Home</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#fff9ed] text-slate-800 antialiased">
    @php
        $user = auth()->user();
        $navItems = [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'roles' => null],
            ['label' => 'Kelola Pengguna', 'route' => 'users.index', 'roles' => ['admin']],
            ['label' => 'Pengelolaan Menu', 'route' => 'menus.index', 'roles' => null],
            ['label' => 'Pengelolaan Resep', 'route' => 'recipes.index', 'roles' => ['admin', 'pengelola']],
            ['label' => 'Stok Bahan Baku', 'route' => 'ingredients.index', 'roles' => null],
            ['label' => 'Pembelian Bahan Baku', 'route' => 'purchases.index', 'roles' => ['admin', 'pengelola']],
            ['label' => 'Pencatatan Penjualan', 'route' => 'sales.index', 'roles' => null],
            ['label' => 'Biaya Operasional', 'route' => 'expenses.index', 'roles' => ['admin']],
            ['label' => 'Laporan Keuntungan', 'route' => 'reports.profit', 'roles' => ['admin']],
            ['label' => 'Rekomendasi Produksi', 'route' => 'forecast.index', 'roles' => null],
        ];
    @endphp

    <input type="checkbox" id="nav-toggle" class="peer hidden">
    <label for="nav-toggle" aria-label="Buka menu" class="fixed inset-0 z-30 hidden bg-slate-900/50 peer-checked:block md:hidden"></label>

    <aside class="fixed inset-y-0 left-0 z-40 flex w-[252px] -translate-x-full flex-col bg-[#142d70] text-white transition-transform duration-200 peer-checked:translate-x-0 md:translate-x-0">
        <div class="flex h-[76px] items-center gap-3 border-b border-white/10 px-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/15 text-lg font-bold">S</div>
            <div>
                <p class="text-sm font-bold tracking-tight">SmartFood Home</p>
                <p class="text-[11px] text-blue-200">Panel Manajemen</p>
            </div>
            <label for="nav-toggle" aria-label="Tutup menu" class="ml-auto cursor-pointer text-xl md:hidden">&times;</label>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
            <p class="mb-3 px-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-blue-300">Menu Utama</p>
            @foreach ($navItems as $item)
                @if ($item['roles'] === null || in_array($user->role->value, $item['roles'], true))
                    @if (Route::has($item['route']))
                        <a href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] font-medium transition {{ request()->routeIs($item['route']) ? 'bg-[#3154a5] text-white' : 'text-blue-100 hover:bg-white/10' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ request()->routeIs($item['route']) ? 'bg-[#93c5fd]' : 'bg-blue-400/60' }}"></span>
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] text-blue-200/40" title="Modul belum tersedia">
                            <span class="h-1.5 w-1.5 rounded-full bg-blue-400/30"></span>
                            {{ $item['label'] }}
                        </span>
                    @endif
                @endif
            @endforeach

            <p class="mb-3 mt-6 px-3 text-[10px] font-semibold uppercase tracking-[0.14em] text-blue-300">Akun</p>
            @if (Route::has('password.edit'))
                <a href="{{ route('password.edit') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] font-medium text-blue-100 transition hover:bg-white/10 {{ request()->routeIs('password.edit') ? 'bg-[#3154a5] text-white' : '' }}">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-400/60"></span>
                    Ganti Password
                </a>
            @endif
        </nav>

        <div class="border-t border-white/10 p-4">
            <div class="mb-3 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#93c5fd] text-xs font-bold text-[#142d70]">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-xs font-semibold">{{ $user->name }}</p>
                    <p class="text-[11px] text-blue-200">{{ $user->role->label() }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-xs font-semibold text-[#fca5a5] transition hover:bg-white/10">Keluar</button>
            </form>
        </div>
    </aside>

    <div class="md:pl-[252px]">
        <header class="sticky top-0 z-20 flex h-[76px] items-center gap-3 border-b border-[#ecece8] bg-white px-5 sm:px-8">
            <label for="nav-toggle" aria-label="Buka menu" class="cursor-pointer text-2xl text-[#142d70] md:hidden">&#9776;</label>
            <div>
                <p class="text-[11px] text-slate-500">SmartFood Home</p>
                <h1 class="text-[17px] font-bold tracking-tight text-[#142d70]">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-bold text-[#1d4ed8]">{{ $user->role->label() }}</span>
                <span class="hidden text-xs font-medium text-slate-600 sm:inline">{{ $user->name }}</span>
            </div>
        </header>

        <main class="mx-auto max-w-[1600px] px-5 py-7 sm:px-8">
            @if (session('success'))
                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>

</html>
