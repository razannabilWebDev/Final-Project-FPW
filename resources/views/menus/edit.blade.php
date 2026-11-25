@extends('layouts.app')

@section('title', 'Ubah Menu')
@section('breadcrumb', 'Pengelolaan Menu')

@section('content')
    <div class="mx-auto max-w-3xl rounded-2xl border border-line bg-white p-6 shadow-card sm:p-8">
        <div class="mb-6 flex items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[#dbeafe] text-brand">
                <x-icon name="pencil" :size="19" />
            </span>
            <div>
                <h2 class="text-sm font-bold text-[#142d70]">Ubah Data Menu</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Perbarui informasi menu, harga jual, atau foto produk.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('menus.update', $menu) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('menus._form', ['isEdit' => true, 'fieldPrefix' => 'edit'])

            <div class="mt-7 flex flex-wrap justify-end gap-3 border-t border-line pt-5">
                <a href="{{ route('menus.show', $menu) }}"
                   class="rounded-lg border border-line-input bg-white px-5 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit"
                        class="flex items-center gap-2 rounded-lg bg-brand px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover">
                    <x-icon name="check" :size="14" :stroke="2.4" />
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
