<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartFood Home') — SmartFood Home</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-cream text-[#1f2937] antialiased">
    @php
        $__breadcrumbDefault = trim(view()->yieldContent('title')) ?: 'Dashboard';
        $user = auth()->user();
        $navItems = [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home', 'roles' => null],
            ['label' => 'Kelola Pengguna', 'route' => 'users.index', 'icon' => 'users', 'roles' => ['admin']],
            ['label' => 'Pengelolaan Menu', 'route' => 'menus.index', 'icon' => 'book', 'roles' => null],
            ['label' => 'Pengelolaan Resep', 'route' => 'recipes.index', 'icon' => 'clipboard', 'roles' => ['admin', 'pengelola']],
            ['label' => 'Stok Bahan Baku', 'route' => 'ingredients.index', 'icon' => 'box', 'roles' => null],
            ['label' => 'Pembelian Bahan Baku', 'route' => 'purchases.index', 'icon' => 'cart', 'roles' => ['admin', 'pengelola']],
            ['label' => 'Pencatatan Penjualan', 'route' => 'sales.index', 'icon' => 'receipt', 'roles' => null],
            ['label' => 'Biaya Operasional', 'route' => 'expenses.index', 'icon' => 'wallet', 'roles' => ['admin']],
            ['label' => 'Laporan Keuntungan', 'route' => 'reports.profit', 'icon' => 'chart', 'roles' => ['admin']],
            ['label' => 'Rekomendasi Produksi', 'route' => 'forecast.index', 'icon' => 'sparkles', 'roles' => null],
        ];
    @endphp

    <input type="checkbox" id="nav-toggle" class="peer hidden">
    <label for="nav-toggle" aria-label="Buka menu"
           class="fixed inset-0 z-30 hidden bg-slate-900/50 peer-checked:block min-[901px]:hidden"></label>

    <aside class="fixed inset-y-0 left-0 z-40 flex w-[252px] -translate-x-full flex-col bg-navy text-white transition-transform duration-200 peer-checked:translate-x-0 min-[901px]:translate-x-0">
        <div class="flex h-[76px] items-center gap-3 border-b border-white/10 px-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-brand shadow-lg">
                <x-icon name="home" :size="20" :stroke="2.2" />
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold tracking-tight">SmartFood Home</p>
                <p class="text-[11px] text-blue-200">Panel Manajemen</p>
            </div>
            <label for="nav-toggle" aria-label="Tutup menu"
                   class="ml-auto cursor-pointer text-xl min-[901px]:hidden">&times;</label>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
            <p class="mb-3 px-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-blue-300">Menu Utama</p>
            @foreach ($navItems as $item)
                @if ($item['roles'] === null || in_array($user->role->value, $item['roles'], true))
                    @php($isActive = request()->routeIs($item['route']))
                    @if (Route::has($item['route']))
                        <a href="{{ route($item['route']) }}"
                           class="relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] font-medium transition {{ $isActive ? 'bg-[#3154a5] text-white' : 'text-blue-100 hover:bg-white/10' }}">
                            @if ($isActive)
                                <span class="absolute left-0 top-1/2 h-[21px] w-[3px] -translate-y-1/2 rounded-r bg-accent-light"
                                      aria-hidden="true"></span>
                            @endif
                            <x-icon :name="$item['icon']" :size="17"
                                    class="{{ $isActive ? 'text-accent-light' : 'text-blue-300/80' }}" />
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] text-blue-200/40"
                              title="Modul belum tersedia">
                            <x-icon :name="$item['icon']" :size="17" />
                            {{ $item['label'] }}
                        </span>
                    @endif
                @endif
            @endforeach

            <p class="mb-3 mt-6 border-t border-white/10 px-3 pt-5 text-[11px] font-semibold uppercase tracking-[0.14em] text-blue-300">
                Akun
            </p>
            @if (Route::has('password.edit'))
                @php($isPasswordActive = request()->routeIs('password.edit'))
                <a href="{{ route('password.edit') }}"
                   class="relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-[13px] font-medium transition {{ $isPasswordActive ? 'bg-[#3154a5] text-white' : 'text-blue-100 hover:bg-white/10' }}">
                    @if ($isPasswordActive)
                        <span class="absolute left-0 top-1/2 h-[21px] w-[3px] -translate-y-1/2 rounded-r bg-accent-light"
                              aria-hidden="true"></span>
                    @endif
                    <x-icon name="settings" :size="17"
                            class="{{ $isPasswordActive ? 'text-accent-light' : 'text-blue-300/80' }}" />
                    Ganti Password
                </a>
            @endif
        </nav>

        <div class="border-t border-white/10 p-4">
            <div class="mb-3 flex items-center gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent-light text-xs font-bold text-navy">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-semibold">{{ $user->name }}</p>
                    <p class="text-[11px] text-blue-200">{{ $user->role->label() }}</p>
                </div>
                <span class="text-lg leading-none text-blue-200" aria-hidden="true">&rsaquo;</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-[12px] font-semibold text-[#fca5a5] transition hover:bg-white/10">
                    <x-icon name="logout" :size="15" />
                    Keluar dari akun
                </button>
            </form>
        </div>
    </aside>

    <div class="min-[901px]:pl-[252px]">
        <header class="sticky top-0 z-20 flex h-[76px] items-center gap-3 border-b border-line bg-white px-5 sm:px-[30px]">
            <label for="nav-toggle" aria-label="Buka menu"
                   class="cursor-pointer text-2xl text-navy min-[901px]:hidden">&#9776;</label>
            <div class="min-w-0">
                <p class="truncate text-[11px] text-slate-500">
                    SmartFood Home <span class="mx-1 text-slate-400" aria-hidden="true">&rsaquo;</span>
                    @yield('breadcrumb', $__breadcrumbDefault ?? 'Dashboard')
                </p>
                <h1 class="truncate text-[17px] font-bold tracking-tight text-navy">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="ml-auto flex shrink-0 items-center gap-2 sm:gap-3">
                <span class="hidden rounded-full border border-line-input bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-600 sm:inline-flex">
                    Tampilan: <span class="ml-1 font-bold text-[#1d4ed8]">{{ $user->role->label() }}</span>
                </span>
                <x-notification-dropdown :notifications="$navbarNotifications ?? collect()" />
                <div class="flex items-center gap-2">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#dbeafe] text-[11px] font-bold text-[#1d4ed8]">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <div class="hidden leading-tight sm:block">
                        <p class="text-[12px] font-bold text-[#1f2937]">{{ $user->name }}</p>
                        <p class="text-[11px] text-slate-500">{{ $user->role->label() }}</p>
                    </div>
                    <x-icon name="chevron-down" :size="14" class="hidden text-slate-400 sm:block" />
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-[1600px] px-5 py-6 sm:px-[30px] sm:py-[26px]">
            <x-toast />
            @yield('content')
        </main>
    </div>
</body>

</html>
