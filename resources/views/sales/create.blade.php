@extends('layouts.app')

@section('title', 'Input Pesanan')

@section('content')
    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Input Pesanan</h1>
        <p class="mt-1 text-sm text-slate-500">Catat penjualan berbasis menu atau penjualan manual.</p>
    </div>

    <div class="rounded-2xl border border-line bg-white p-6 shadow-card">
        @include('sales.partials.create-form')

        <div class="mt-5">
            <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali ke daftar</a>
        </div>
    </div>
@endsection
