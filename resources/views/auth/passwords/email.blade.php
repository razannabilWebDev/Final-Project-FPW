@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <h1 class="text-2xl font-extrabold tracking-tight text-[#142d70]">Lupa kata sandi?</h1>
    <p class="mt-2 text-sm leading-relaxed text-slate-500">
        Masukkan alamat email akun Anda. Kami akan mengirim tautan untuk mengatur ulang kata sandi.
    </p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-7 space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-xs font-bold text-[#374151]">Alamat Email</label>
            <input id="email" name="email" type="email" autocomplete="email" autofocus required
                value="{{ old('email') }}" placeholder="nama@email.com"
                class="h-12 w-full rounded-[10px] border border-[#dfe3e8] bg-white px-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe] @error('email') border-danger @enderror">
            @error('email')
                <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="h-12 w-full rounded-[10px] bg-brand px-4 text-sm font-bold text-white transition hover:bg-brand-hover focus:outline-none focus:ring-4 focus:ring-blue-200">
            Kirim Tautan Penggantian
        </button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-500">
        <a href="{{ route('login') }}" class="font-bold text-brand hover:text-brand-hover">Kembali ke halaman masuk</a>
    </p>
@endsection
