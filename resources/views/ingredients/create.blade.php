@extends('layouts.app')

@section('title', 'Tambah Bahan Baku')

@section('content')
    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Tambah Bahan Baku</h1>
        <p class="mt-1 text-sm text-slate-500">Daftarkan bahan baru dan catat stok awalnya.</p>
    </div>

    <div class="mx-auto max-w-2xl rounded-2xl border border-line bg-white p-5 shadow-card sm:p-6">
        <h2 class="text-sm font-bold text-[#142d70]">Informasi Bahan Baku</h2>
        <p class="mt-0.5 text-[11px] text-slate-500">Stok awal akan tercatat sebagai pergerakan stok penyesuaian.</p>

        <form method="POST" action="{{ route('ingredients.store') }}" class="mt-5">
            @csrf
            @include('ingredients._form', ['fieldId' => 'create-page'])

            <div class="mt-7 flex flex-wrap gap-3">
                <button type="submit" class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover">
                    <x-icon name="check" :size="14" :stroke="2.4" />
                    Simpan Bahan
                </button>
                <a href="{{ route('ingredients.index') }}" class="rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#374151] transition hover:bg-slate-50">Batal</a>
            </div>
        </form>
    </div>
@endsection
