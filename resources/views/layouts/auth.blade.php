<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartFood Home')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#fff9ed] text-slate-800 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-gradient-to-br from-[#153577] to-[#1e3a8a] p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-20 h-96 w-96 rounded-full border border-white/10"></div>

            <div class="relative flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 text-xl font-bold">S</div>
                <span class="text-lg font-bold tracking-tight">SmartFood Home</span>
            </div>

            <div class="relative max-w-md">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-blue-200">Manajemen Usaha Makanan Rumahan</p>
                <h1 class="text-4xl font-extrabold leading-tight tracking-tight">Semua operasional usaha, dalam satu sistem.</h1>
                <p class="mt-5 text-sm leading-relaxed text-blue-100">Catat penjualan, kelola menu dan resep, pantau stok, serta lihat rekomendasi produksi untuk usaha makanan rumahan Anda.</p>
            </div>

            <p class="relative text-xs text-blue-200">© {{ now()->year }} SmartFood Home</p>
        </div>

        <main class="flex min-h-screen items-center justify-center px-5 py-10 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#142d70] text-lg font-bold text-white">S</div>
                        <span class="text-base font-bold tracking-tight text-[#142d70]">SmartFood Home</span>
                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
