@extends('layouts.app')

@section('title', 'Ganti Password')

@section('content')
    <div class="mx-auto max-w-xl rounded-2xl border border-[#ecece8] bg-white p-6 shadow-[0_8px_24px_rgba(30,58,138,0.06)] sm:p-8">
        <h2 class="text-lg font-bold text-[#142d70]">Ubah Kata Sandi</h2>
        <p class="mt-1 text-xs text-slate-500">Masukkan kata sandi saat ini, lalu pilih kata sandi baru minimal 8 karakter.</p>

        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="current_password" class="mb-2 block text-xs font-bold text-slate-700">Kata Sandi Saat Ini</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required
                    class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('current_password') border-red-400 @enderror">
                @error('current_password')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-2 block text-xs font-bold text-slate-700">Kata Sandi Baru</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required
                    class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100 @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-2 block text-xs font-bold text-slate-700">Konfirmasi Kata Sandi Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                    class="w-full rounded-lg border border-[#dfe3e8] px-4 py-3 text-sm outline-none transition focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100">
            </div>

            <button type="submit"
                class="h-11 w-full rounded-lg bg-[#2563eb] px-4 text-sm font-bold text-white transition hover:bg-[#1d4ed8] focus:outline-none focus:ring-4 focus:ring-blue-200">
                Simpan Kata Sandi
            </button>
        </form>
    </div>
@endsection
