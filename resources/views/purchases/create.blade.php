@extends('layouts.app')

@section('title', 'Input Nota Pembelian')
@section('breadcrumb', 'Pembelian Bahan Baku')

@section('content')
    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Input Nota Pembelian</h1>
        <p class="mt-1 text-sm text-slate-500">Catat pembelian bahan baku. Verifikasi dilakukan oleh Admin atau Pengelola.</p>
    </div>

    <div class="rounded-2xl border border-line bg-white p-6 shadow-card">
        @include('purchases.partials.create-form', ['suppliers' => $suppliers])

        <div class="mt-6 flex flex-wrap items-center gap-3 border-t border-line pt-5">
            <button type="submit" form="purchase-form" class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover">
                <x-icon name="check" :size="14" :stroke="2.4" />
                Simpan Pembelian
            </button>
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali ke dashboard</a>
        </div>
    </div>

    <script>
        (function () {
            const itemContainer = document.getElementById('purchase-items');
            const itemTemplate = document.getElementById('purchase-item-template');
            let itemIndex = 1;
            document.getElementById('add-purchase-item')?.addEventListener('click', function () {
                if (!itemContainer || !itemTemplate) return;
                itemContainer.insertAdjacentHTML('beforeend', itemTemplate.innerHTML.replaceAll('__INDEX__', String(itemIndex++)));
            });

            document.addEventListener('change', function (event) {
                if (!event.target.classList?.contains('purchase-ingredient')) return;
                const row = event.target.closest('.purchase-item-row');
                const unit = event.target.selectedOptions[0]?.dataset.unit ?? '';
                if (row) row.querySelector('.purchase-unit').value = unit;
            });

            document.addEventListener('click', function (event) {
                const remove = event.target.closest('.remove-purchase-item');
                if (!remove) return;
                const rows = document.querySelectorAll('.purchase-item-row');
                if (rows.length > 1) remove.closest('.purchase-item-row').remove();
            });
        })();
    </script>
@endsection
