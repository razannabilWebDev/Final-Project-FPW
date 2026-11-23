@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="rounded-2xl border border-[#ecece8] bg-white p-7 shadow-[0_8px_24px_rgba(30,58,138,0.06)] sm:p-9">
        <p class="mb-2 text-xs font-bold uppercase tracking-[0.12em] text-[#2563eb]">Selamat datang kembali</p>
        <h2 class="text-2xl font-extrabold tracking-tight text-[#142d70]">Masuk ke akun Anda</h2>
        <p class="mt-2 text-sm text-slate-500">Gunakan email dan kata sandi yang telah terdaftar.</p>

        <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5">
            @csrf

            <div>
                <label for="email" class="mb-2 block text-xs font-bold text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" autofocus
                    class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('email') border-red-400 @enderror"
                    placeholder="nama@email.com" required>
                @error('email')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-2 block text-xs font-bold text-slate-700">Kata Sandi</label>
                <input id="password" name="password" type="password" autocomplete="current-password"
                    class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('password') border-red-400 @enderror"
                    placeholder="Masukkan kata sandi" required>
                @error('password')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-xs text-slate-600">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-[#2563eb] focus:ring-blue-200">
                Ingat saya
            </label>

            <button type="submit"
                class="flex h-12 w-full items-center justify-center rounded-lg bg-[#2563eb] px-4 text-sm font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-[#1d4ed8] focus:outline-none focus:ring-4 focus:ring-blue-200">
                Masuk
            </button>
        </form>
    </div>
@endsection
