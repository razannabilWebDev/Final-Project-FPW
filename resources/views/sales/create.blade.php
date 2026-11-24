@extends('layouts.app')

@section('title', 'Input Pesanan')

@section('content')
    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Input Pesanan</h1>
        <p class="mt-1 text-sm text-slate-500">Catat penjualan berbasis menu atau penjualan manual.</p>
    </div>

    <div class="rounded-2xl border border-line bg-white p-6 shadow-card">
        @include('sales.partials.create-form')

        <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-line pt-5">
            <button type="submit" form="sale-form" class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover">
                <x-icon name="check" :size="14" :stroke="2.4" />
                Simpan Pesanan
            </button>

        </div>

        <div class="mt-5">
            <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali ke daftar</a>
        </div>
    </div>
@endsection
