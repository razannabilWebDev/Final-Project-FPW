@extends('layouts.app')

@section('title', 'Pembelian Bahan Baku')

@section('content')
    @php
        $rupiah = static fn ($value): string => 'Rp'.number_format((float) $value, 0, ',', '.');
        $tone = static function (\App\Enums\PurchaseStatus $status): string {
            return match ($status) {
                \App\Enums\PurchaseStatus::Approved => 'bg-success-soft text-[#15803d]',
                \App\Enums\PurchaseStatus::Rejected => 'bg-danger-soft text-[#b91c1c]',
                default => 'bg-warning-soft text-[#b45309]',
            };
        };
    @endphp

    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Pembelian Bahan Baku</h1>
        <p class="mt-1 text-sm text-slate-500">Catat dan verifikasi transaksi pembelian bahan baku.</p>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-stat-card label="Total Pembelian" :value="$rupiah($totalPurchase)" icon="cart" tone="blue" />
        <x-stat-card label="Jumlah Transaksi" :value="number_format($transactionCount, 0, ',', '.')" icon="receipt" tone="purple" />
        <x-stat-card label="Menunggu Verifikasi" :value="number_format($pendingCount, 0, ',', '.')" icon="clock" tone="orange" />
    </div>

    <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-[#142d70]">Riwayat Pembelian</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Menampilkan {{ $purchases->count() }} dari {{ $purchases->total() }} transaksi</p>
            </div>
            @can('create-purchases')
                <button type="button" id="open-create-purchase"
                    class="flex h-10 items-center gap-2 rounded-lg bg-brand px-4 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover">
                    <x-icon name="plus" :size="15" :stroke="2.4" />
                    Input Pembelian
                </button>
            @endcan
        </div>

        <form method="GET" action="{{ route('purchases.index') }}" class="mb-4 flex flex-wrap gap-3">
            <div class="relative min-w-[220px] flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="search" :size="16" />
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari data..."
                    class="h-10 w-full rounded-lg border border-line-input pl-9 pr-3 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
            </div>
            <select name="status" class="h-10 min-w-[170px] rounded-lg border border-line-input bg-white px-3 text-sm outline-none focus:border-brand">
                <option value="">Semua status</option>
                @foreach ($statuses as $item)
                    <option value="{{ $item->value }}" @selected($status === $item->value)>{{ $item->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="flex h-10 items-center gap-2 rounded-lg border border-line-input bg-white px-4 text-xs font-bold text-[#374151] transition hover:bg-slate-50">
                <x-icon name="filter" :size="15" />
                Filter
            </button>
        </form>

        @if ($purchases->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left text-xs">
                    <thead>
                        <tr class="border-b border-line bg-head-bg text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            <th class="px-3 py-3">Tanggal</th>
                            <th class="px-3 py-3">Kode</th>
                            <th class="px-3 py-3">Nama Bahan</th>
                            <th class="px-3 py-3">Supplier</th>
                            <th class="px-3 py-3">Jumlah</th>
                            <th class="px-3 py-3">Total Harga</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchases as $purchase)
                            @php
                                $firstItem = $purchase->items->first();
                                $itemName = $firstItem?->ingredient?->name ?? '—';
                                if ($purchase->items_count > 1) {
                                    $itemName .= ' (+'.($purchase->items_count - 1).')';
                                }
                            @endphp
                            <tr class="border-b border-line last:border-0 hover:bg-slate-50/60">
                                <td class="px-3 py-3 text-slate-500">{{ $purchase->purchase_date?->translatedFormat('d M Y') ?? '—' }}</td>
                                <td class="px-3 py-3 font-bold text-[#142d70]">{{ $purchase->purchase_code }}</td>
                                <td class="px-3 py-3 font-semibold text-[#374151]">{{ $itemName }}</td>
                                <td class="px-3 py-3 text-slate-500">{{ $purchase->supplier_name ?? '—' }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $purchase->items_count }} item</td>
                                <td class="px-3 py-3 font-semibold text-[#374151]">{{ $rupiah($purchase->total) }}</td>
                                <td class="px-3 py-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $tone($purchase->status) }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $purchase->status->label() }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <button type="button" class="open-detail flex h-[29px] w-[29px] items-center justify-center rounded-lg border border-line text-brand transition hover:bg-[#dbeafe]"
                                        aria-label="Lihat detail {{ $purchase->purchase_code }}"
                                        data-id="{{ $purchase->id }}">
                                        <x-icon name="eye" :size="16" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                <p class="text-[11px] text-slate-500">Menampilkan {{ $purchases->firstItem() ?? 0 }}–{{ $purchases->lastItem() ?? 0 }} dari {{ $purchases->total() }} data</p>
                <div>{{ $purchases->links() }}</div>
            </div>
        @else
            <x-empty-state icon="receipt" title="Belum ada pembelian" description="Pembelian yang dicatat akan tampil di sini setelah tersedia." />
        @endif
    </div>

    {{-- Modal: Input Pembelian --}}
    @can('create-purchases')
        <x-modal id="create-purchase-modal" title="Input Pembelian" icon="plus" size="lg">
            @include('purchases.partials.create-form')
            <x-slot:footer>
                <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#374151]">Batal</button>
                <button type="submit" form="purchase-form" class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 hover:bg-brand-hover">
                    <x-icon name="check" :size="14" :stroke="2.4" />
                    Simpan Pembelian
                </button>
            </x-slot:footer>
        </x-modal>
    @endcan

    {{-- Modal: Detail dan Verifikasi --}}
    <x-modal id="detail-purchase-modal" title="Detail Pembelian" icon="receipt" size="lg">
        <div id="detail-purchase-content">
            <x-empty-state icon="receipt" title="Memuat detail..." description="Harap tunggu." />
        </div>
        <x-slot:footer>
            <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#374151]">Tutup</button>
        </x-slot:footer>
    </x-modal>

    <script>
        (function () {
            const openCreate = document.getElementById('open-create-purchase');
            const createModal = document.getElementById('create-purchase-modal');
            if (openCreate && createModal) {
                openCreate.addEventListener('click', function (event) {
                    event.preventDefault();
                    createModal.openModal();
                });
            }

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

            const detailModal = document.getElementById('detail-purchase-modal');
            const detailContent = document.getElementById('detail-purchase-content');
            async function loadDetail(id) {
                detailContent.innerHTML = '<div class="py-10 text-center text-xs text-slate-500">Memuat detail...</div>';
                detailModal.openModal();
                try {
                    const response = await fetch(@json(route('purchases.index')) + '/' + id, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    detailContent.innerHTML = await response.text();
                } catch (error) {
                    detailContent.innerHTML = '<div class="py-10 text-center text-xs text-danger">Gagal memuat detail.</div>';
                }
            }

            document.querySelectorAll('.open-detail').forEach(function (button) {
                button.addEventListener('click', function () { loadDetail(button.dataset.id); });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') return;
                document.querySelectorAll('.modal-root:not(.hidden)').forEach(function (modal) { modal.closeModal(); });
            });

            @if (old('modal'))
                (function () {
                    const modal = document.getElementById(@json(old('modal')));
                    if (modal) modal.openModal();
                })();
            @endif

            @if (request()->query('modal') === 'detail-purchase-modal' && request()->filled('id'))
                loadDetail(@json(request()->query('id')));
            @endif
        })();
    </script>
@endsection
