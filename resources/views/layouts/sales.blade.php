<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Penjualan') — SmartFood Home</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#fff9ed] text-[#1f2937] antialiased">
    <header class="border-b border-[#ecece8] bg-white">
        <div class="mx-auto flex h-16 max-w-[1400px] items-center justify-between px-5">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#142d70] text-white">
                    <x-icon name="home" :size="18" />
                </span>
                <span class="text-sm font-bold text-[#142d70]">SmartFood Home</span>
            </div>
            <span class="text-xs font-semibold text-slate-500">{{ Auth::user()?->name }}</span>
        </div>
    </header>

    <main class="mx-auto max-w-[1400px] px-5 py-7">
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
    </main>
</body>

</html>
