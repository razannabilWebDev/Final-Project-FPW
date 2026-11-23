<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartFood Home')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-surface text-[#1f2937] antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-2">
        {{-- Panel branding --}}
        <div class="relative hidden flex-col overflow-hidden bg-gradient-to-br from-[#142d70] via-[#153577] to-[#1e3a8a] p-10 text-white lg:flex lg:justify-between xl:p-14">
            <div class="pointer-events-none absolute -right-28 -top-28 h-[28rem] w-[28rem] rounded-full border border-white/5"></div>
            <div class="pointer-events-none absolute -bottom-40 -left-24 h-[32rem] w-[32rem] rounded-full bg-white/[0.03]"></div>
            <div class="pointer-events-none absolute bottom-10 right-16 h-72 w-72 rounded-full border border-white/5"></div>

            <div class="relative flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-[#2563eb] shadow-lg">
                        <x-icon name="home" :size="22" :stroke="2.2" />
                    </div>
                    <div>
                        <p class="text-base font-bold leading-tight tracking-tight">SmartFood</p>
                        <p class="text-[11px] text-blue-200">Home</p>
                    </div>
                </div>
                <span class="rounded-full border border-white/25 px-4 py-1.5 text-[11px] font-medium text-blue-100">Manajemen kuliner rumahan</span>
            </div>

            <div class="relative max-w-xl">
                <p class="mb-4 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.16em] text-blue-200">
                    <x-icon name="sparkles" :size="15" />
                    Lebih rapi, lebih untung
                </p>
                <h1 class="text-[clamp(2.1rem,3.2vw,3.2rem)] font-extrabold leading-[1.12] tracking-[-0.035em]">
                    Kelola Usaha Kuliner Lebih<br>
                    <span class="text-accent-light">Mudah dan Terorganisir</span>
                </h1>
                <p class="mt-5 max-w-md text-sm leading-relaxed text-blue-100/90">
                    Satu ruang kerja untuk mengelola menu, stok, pesanan, hingga melihat keuntungan usaha Anda.
                </p>
            </div>

            <div class="relative mt-8 max-w-xl">
                <div class="relative overflow-hidden rounded-2xl shadow-2xl shadow-black/30">
                    <img src="https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=1000&q=80"
                        alt="Hidangan kuliner rumahan" class="h-52 w-full object-cover xl:h-60"
                        onerror="this.style.display='none'">
                    <span class="absolute right-3 top-3 flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-[11px] font-bold text-[#15803d] shadow-md">
                        <x-icon name="check" :size="12" :stroke="2.6" />
                        Stok aman hari ini
                    </span>
                    <div class="absolute bottom-3 left-3 flex items-center gap-3 rounded-xl bg-white px-4 py-2.5 shadow-lg">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#dcfce7] text-[#16a34a]">
                            <x-icon name="trending" :size="17" />
                        </span>
                        <span>
                            <span class="block text-[10px] font-medium text-slate-500">Penjualan minggu ini</span>
                            <span class="block text-sm font-extrabold text-[#142d70]">+18,4%</span>
                        </span>
                    </div>
                </div>
                <p class="mt-2 text-[10px] text-blue-200/70">Foto oleh Baya Syafii di Unsplash</p>
            </div>
        </div>

        {{-- Panel form --}}
        <main class="flex min-h-screen items-center justify-center bg-white px-5 py-10 sm:px-10 lg:min-h-0">
            <div class="w-full max-w-[420px]">
                <div class="mb-8 lg:hidden">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#142d70] text-white">
                            <x-icon name="home" :size="20" />
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#142d70]">SmartFood</p>
                            <p class="text-[11px] text-slate-500">Home</p>
                        </div>
                    </div>
                </div>

                @if (session('success'))
                    <div class="mb-5 rounded-xl border border-[#dcfce7] bg-[#f0fdf4] px-4 py-3 text-sm text-[#15803d]" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 rounded-xl border border-[#fee2e2] bg-[#fef2f2] px-4 py-3 text-sm text-[#b91c1c]" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
