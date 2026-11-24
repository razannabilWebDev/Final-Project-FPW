@extends('layouts.app')

@section('title', 'Tambah Resep')
@section('breadcrumb', 'Pengelolaan Resep')

@section('content')
    <div class="mx-auto max-w-3xl rounded-2xl border border-line bg-white p-6 shadow-card sm:p-8">
        <div class="mb-6 flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#dbeafe] text-brand">
                <x-icon name="plus" :size="19" />
            </span>
            <div>
                <h2 class="text-sm font-bold text-[#142d70]">Tambah Resep</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Lengkapi komposisi dan takaran per porsi.</p>
            </div>
        </div>

        @include('recipes.partials.form', ['isEdit' => false, 'menu' => null, 'formId' => 'create-recipe-page-form', 'prefix' => 'create-page'])

        <div class="mt-7 flex flex-wrap justify-end gap-3 border-t border-line pt-5">
            <a href="{{ route('recipes.index') }}"
               class="rounded-lg border border-line-input bg-white px-5 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">Batal</a>
            <button type="submit" form="create-recipe-page-form"
                    class="flex items-center gap-2 rounded-lg bg-brand px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover">
                <x-icon name="check" :size="14" :stroke="2.4" />
                Simpan Resep
            </button>
        </div>
    </div>
@endsection
