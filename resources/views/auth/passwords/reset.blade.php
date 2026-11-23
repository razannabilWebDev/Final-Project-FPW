@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi')

@section('content')
    <h1 class="text-2xl font-extrabold tracking-tight text-[#142d70]">Atur Ulang Kata Sandi</h1>
    <p class="mt-2 text-sm leading-relaxed text-slate-500">
        Buat kata sandi baru untuk akun Anda. Minimal 8 karakter.
    </p>

    <form method="POST" action="{{ route('password.reset.store') }}" class="mt-7 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
            <label for="email_display" class="mb-2 block text-xs font-bold text-[#374151]">Alamat Email</label>
            <input id="email_display" type="email" value="{{ $email }}" readonly
                class="h-12 w-full cursor-not-allowed rounded-[10px] border border-[#e5e7eb] bg-slate-50 px-4 text-sm text-slate-500 outline-none">
        </div>

        <div>
            <label for="password" class="mb-2 block text-xs font-bold text-[#374151]">Kata Sandi Baru</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required
                class="h-12 w-full rounded-[10px] border border-[#dfe3e8] bg-white px-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe] @error('password') border-danger @enderror">
            @error('password')
                <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-2 block text-xs font-bold text-[#374151]">Konfirmasi Kata Sandi Baru</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                class="h-12 w-full rounded-[10px] border border-[#dfe3e8] bg-white px-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
        </div>

        <button type="submit"
            class="h-12 w-full rounded-[10px] bg-brand px-4 text-sm font-bold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-blue-200">
            Simpan Kata Sandi Baru
        </button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-500">
        <a href="{{ route('login') }}" class="font-bold text-brand hover:text-brand-hover">Kembali ke halaman masuk</a>
    </p>
@endsection
