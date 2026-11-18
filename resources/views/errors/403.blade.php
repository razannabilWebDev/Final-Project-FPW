@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
    <div class="mx-auto max-w-lg rounded-2xl border border-red-100 bg-white p-8 text-center shadow-[0_8px_24px_rgba(30,58,138,0.06)]">
        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-red-50 text-2xl font-bold text-red-600">!</div>
        <h2 class="text-lg font-bold text-[#142d70]">Anda tidak memiliki akses</h2>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">Halaman ini hanya dapat diakses oleh role yang diizinkan. Hubungi Admin jika Anda memerlukan akses.</p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-block rounded-lg bg-[#2563eb] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#1d4ed8]">Kembali ke Dashboard</a>
    </div>
@endsection
