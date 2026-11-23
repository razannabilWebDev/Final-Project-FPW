@extends('layouts.app')

@section('title', 'Tambah Bahan Baku')

@section('content')
    <div class="mx-auto max-w-2xl rounded-2xl border border-[#ecece8] bg-white p-6 shadow-[0_8px_24px_rgba(30,58,138,0.06)] sm:p-8">
        <h2 class="text-lg font-bold text-[#142d70]">Tambah Bahan Baku Baru</h2>
        <p class="mt-1 text-xs text-slate-500">Stok awal akan tercatat sebagai pergerakan stok penyesuaian.</p>

        <form method="POST" action="{{ route('ingredients.store') }}" class="mt-6">
            @csrf
            @include('ingredients._form')

            <div class="mt-7 flex flex-wrap gap-3">
                <button type="submit" class="rounded-lg bg-[#2563eb] px-5 py-2.5 text-xs font-bold text-white transition hover:bg-[#1d4ed8]">Simpan Bahan</button>
                <a href="{{ route('ingredients.index') }}" class="rounded-lg border border-[#dfe3e8] px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
@endsection
