@extends('layouts.app')

@section('title', 'Detail Menu')
@section('breadcrumb', 'Pengelolaan Menu')

@section('content')
    <div class="mx-auto max-w-3xl rounded-2xl border border-line bg-white p-6 shadow-card sm:p-8">
        <div class="mb-6 flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#dbeafe] text-brand">
                <x-icon name="eye" :size="19" />
            </span>
            <div>
                <h2 class="text-sm font-bold text-[#142d70]">Detail Menu</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Detail produk yang dijual usaha.</p>
            </div>
        </div>

        @include('menus.partials.detail')

        <div class="mt-7 flex flex-wrap justify-end gap-3 border-t border-line pt-5">
            <a href="{{ route('menus.index') }}"
               class="rounded-lg border border-line-input bg-white px-5 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">
                Kembali
            </a>
            @can('manage-menus')
                <a href="{{ route('menus.edit', $menu) }}"
                   class="rounded-lg bg-brand px-5 py-2.5 text-xs font-bold text-white transition hover:bg-brand-hover">
                    Ubah Menu
                </a>
            @endcan
        </div>
    </div>
@endsection
