@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <p class="mb-3 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.12em] text-brand">
        <span class="h-1.5 w-1.5 rounded-full bg-brand"></span>
        Smartfood Home
    </p>
    <h2 class="text-[30px] font-extrabold leading-tight tracking-[-0.03em] text-[#172033]">Selamat Datang Kembali!</h2>
    <p class="mt-2 text-sm text-slate-500">Masuk untuk mengelola usaha kuliner Anda.</p>

    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-xs font-bold text-[#374151]">Email</label>
            <div class="relative">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="user" :size="17" />
                </span>
                <input id="email" name="email" type="email" value="{{ old('email', 'admin@smartfood.id') }}" autocomplete="username" autofocus
                    class="h-12 w-full rounded-[10px] border border-[#dfe3e8] bg-white pl-10 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe] @error('email') border-danger @enderror"
                    placeholder="nama@email.com" required>
            </div>
            @error('email')
                <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-2 block text-xs font-bold text-[#374151]">Password</label>
            <div class="relative">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="lock" :size="17" />
                </span>
                <input id="password" name="password" type="password" autocomplete="current-password"
                    class="h-12 w-full rounded-[10px] border border-[#dfe3e8] bg-white pl-10 pr-11 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe] @error('password') border-danger @enderror"
                    placeholder="Masukkan password" required>
                <button type="button" id="toggle-password" aria-label="Tampilkan password"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-600">
                    <x-icon name="eye" :size="17" />
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between gap-3">
            <label class="flex items-center gap-2 text-xs text-[#4b5563]">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-blue-200">
                Ingat saya
            </label>
            <a href="{{ route('password.request') }}" class="text-xs font-bold text-brand hover:text-brand-hover">Lupa password?</a>
        </div>

        <button type="submit"
            class="flex h-[49px] w-full items-center justify-center gap-2 rounded-[10px] bg-brand px-4 text-sm font-bold text-white shadow-lg shadow-blue-200 transition hover:-translate-y-px hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-blue-200">
            Masuk ke Dashboard
            <x-icon name="arrow-right" :size="16" :stroke="2.2" />
        </button>
    </form>

    <div class="mt-5 flex items-start gap-3 rounded-xl border border-[#dbeafe] bg-[#eff6ff] px-4 py-3.5">
        <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#dbeafe] text-brand">
            <x-icon name="shield" :size="15" />
        </span>
        <div>
            <p class="text-xs font-bold text-[#1d4ed8]">Akses aman sesuai peran</p>
            <p class="mt-0.5 text-[11px] leading-relaxed text-[#4b5563]">Fitur yang tampil disesuaikan dengan hak akses akun Anda.</p>
        </div>
    </div>

    <p class="mt-8 text-center text-[11px] text-slate-400">© {{ now()->year }} SmartFood Home · Bantuan &amp; Privasi</p>

    <script>
        document.getElementById('toggle-password')?.addEventListener('click', function () {
            const input = document.getElementById('password');
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            this.setAttribute('aria-label', showing ? 'Tampilkan password' : 'Sembunyikan password');
        });
    </script>
@endsection
