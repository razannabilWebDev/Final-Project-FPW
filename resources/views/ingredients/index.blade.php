@extends('layouts.app')

@section('title', 'Stok Bahan Baku')

@section('content')
    @php
        $quantity = static function ($value): string {
            $formatted = number_format((float) $value, 3, ',', '.');

            return rtrim(rtrim($formatted, '0'), ',');
        };
        $rupiah = static fn ($value): string => \App\Support\Rupiah::format($value);
    @endphp

    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Stok Bahan Baku</h1>
        <p class="mt-1 text-sm text-slate-500">Pantau jumlah dan pergerakan bahan baku.</p>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Bahan" :value="number_format($totalIngredients, 0, ',', '.')" icon="box" tone="blue" />
        <x-stat-card label="Stok Aman" :value="number_format($safeStockCount, 0, ',', '.')" icon="check" tone="green" />
        <x-stat-card label="Stok Menipis" :value="number_format($lowStockCount, 0, ',', '.')" icon="clock" tone="orange" />
        <x-stat-card label="Stok Habis" :value="number_format($outOfStockCount, 0, ',', '.')" icon="x" tone="red" />
    </div>

    <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-[#142d70]">Daftar Bahan Baku</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Menampilkan {{ $ingredients->count() }} dari {{ $ingredients->total() }} data</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @can('manage-stock')
                    @if (Route::has('stock.movements'))
                        <a href="{{ route('stock.movements') }}" class="flex h-10 items-center gap-1.5 rounded-lg border border-line-input bg-white px-3.5 text-xs font-bold text-[#374151] transition hover:bg-slate-50">
                            <x-icon name="clock" :size="15" />
                            Riwayat Pergerakan
                        </a>
                    @endif
                @endcan
                @can('update-stock-physical')
                    @if (Route::has('stock.adjustments.create'))
                        <a href="{{ route('stock.adjustments.create') }}" class="flex h-10 items-center gap-1.5 rounded-lg border border-line-input bg-white px-3.5 text-xs font-bold text-[#374151] transition hover:bg-slate-50">
                            <x-icon name="clipboard" :size="15" />
                            Sesuaikan Stok
                        </a>
                    @endif
                @endcan
                @can('manage-ingredients')
                    <button type="button" id="open-create-ingredient"
                        class="flex h-10 items-center gap-2 rounded-lg bg-brand px-4 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover">
                        <x-icon name="plus" :size="15" :stroke="2.4" />
                        Tambah Bahan
                    </button>
                @endcan
            </div>
        </div>

        <form method="GET" action="{{ route('ingredients.index') }}" class="mb-4 flex flex-wrap gap-3">
            <div class="relative min-w-[220px] flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="search" :size="16" />
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari data..."
                    class="h-10 w-full rounded-lg border border-line-input pl-9 pr-9 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
                @if ($search !== '')
                    <a href="{{ route('ingredients.index', ['status' => $status]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" aria-label="Hapus pencarian">
                        <x-icon name="x" :size="14" />
                    </a>
                @endif
            </div>
            <select name="status" class="h-10 rounded-lg border border-line-input bg-white px-3 text-sm outline-none focus:border-brand">
                <option value="">Semua status</option>
                @foreach (['aman' => 'Aman', 'menipis' => 'Menipis', 'habis' => 'Habis', 'nonaktif' => 'Nonaktif'] as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="flex h-10 items-center gap-2 rounded-lg border border-line-input bg-white px-4 text-xs font-bold text-[#374151] transition hover:bg-slate-50">
                <x-icon name="filter" :size="15" />
                Filter
            </button>
        </form>

        @if ($ingredients->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-xs">
                    <thead>
                        <tr class="border-b border-line bg-head-bg text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            <th class="px-3 py-3">Nama Bahan</th>
                            <th class="px-3 py-3">Jumlah</th>
                            <th class="px-3 py-3">Satuan</th>
                            <th class="px-3 py-3">Stok Min.</th>
                            <th class="px-3 py-3">Harga Modal / Satuan</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ingredients as $ingredient)
                            @php
                                $current = (string) $ingredient->current_stock;
                                $minimum = (string) $ingredient->minimum_stock;
                                $isOut = bccomp($current, '0.000', 3) <= 0;
                                $isLow = ! $isOut && bccomp($current, $minimum, 3) <= 0;
                                $stockLabel = $isOut ? 'Habis' : ($isLow ? 'Menipis' : 'Aman');
                                $stockTone = $isOut ? 'bg-danger-soft text-[#b91c1c]' : ($isLow ? 'bg-warning-soft text-[#b45309]' : 'bg-success-soft text-[#15803d]');
                            @endphp
                            <tr class="border-b border-line last:border-0 hover:bg-slate-50/60">
                                <td class="px-3 py-3 font-bold text-[#142d70]">{{ $ingredient->name }}</td>
                                <td class="px-3 py-3 font-semibold {{ $isLow || $isOut ? 'text-[#b45309]' : 'text-[#374151]' }}">{{ $quantity($current) }}</td>
                                <td class="px-3 py-3 text-slate-500">{{ $ingredient->base_unit }}</td>
                                <td class="px-3 py-3 text-slate-500">{{ $quantity($minimum) }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $rupiah($ingredient->average_cost) }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $stockTone }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $stockLabel }}
                                        </span>
                                        @unless ($ingredient->is_active)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f1f5f9] px-2.5 py-1 text-[11px] font-bold text-[#64748b]">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>Nonaktif
                                            </span>
                                        @endunless
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" class="open-detail flex h-[29px] w-[29px] items-center justify-center rounded-lg border border-line text-brand transition hover:bg-[#dbeafe]"
                                            aria-label="Lihat detail {{ $ingredient->name }}"
                                            data-name="{{ $ingredient->name }}"
                                            data-stock="{{ $quantity($current) }}"
                                            data-unit="{{ $ingredient->base_unit }}"
                                            data-minimum="{{ $quantity($minimum) }}"
                                            data-cost="{{ $rupiah($ingredient->average_cost) }}"
                                            data-status="{{ $stockLabel }}"
                                            data-updated="{{ $ingredient->updated_at?->translatedFormat('d M Y, H.i') ?? '—' }}">
                                            <x-icon name="eye" :size="16" />
                                        </button>
                                        @can('manage-ingredients')
                                            <button type="button" class="open-edit flex h-[29px] w-[29px] items-center justify-center rounded-lg border border-line text-brand transition hover:bg-[#dbeafe]"
                                                aria-label="Ubah {{ $ingredient->name }}"
                                                data-modal="edit-ingredient-{{ $ingredient->id }}">
                                                <x-icon name="pencil" :size="16" />
                                            </button>
                                            <button type="button" class="open-status flex h-[29px] w-[29px] items-center justify-center rounded-lg border border-line transition {{ $ingredient->is_active ? 'text-danger hover:bg-danger-soft' : 'text-[#16a34a] hover:bg-success-soft' }}"
                                                aria-label="{{ $ingredient->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $ingredient->name }}"
                                                data-url="{{ route('ingredients.status', $ingredient) }}"
                                                data-active="{{ $ingredient->is_active ? '1' : '0' }}"
                                                data-name="{{ $ingredient->name }}">
                                                <x-icon :name="$ingredient->is_active ? 'trash' : 'check'" :size="16" />
                                            </button>
                                        @endcan
                                        @can('record-waste')
                                            @if (Route::has('stock.waste.create'))
                                                <a href="{{ route('stock.waste.create', ['ingredient_id' => $ingredient->id]) }}"
                                                    class="flex h-[29px] w-[29px] items-center justify-center rounded-lg border border-line text-[#d97706] transition hover:bg-warning-soft"
                                                    aria-label="Catat susut {{ $ingredient->name }}">
                                                    <x-icon name="package" :size="16" />
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                <p class="text-[11px] text-slate-500">Menampilkan {{ $ingredients->firstItem() ?? 0 }}–{{ $ingredients->lastItem() ?? 0 }} dari {{ $ingredients->total() }} data</p>
                <div>{{ $ingredients->links() }}</div>
            </div>
        @else
            <x-empty-state icon="box" title="Belum ada bahan baku" description="Tambahkan bahan baku pertama untuk mulai mencatat stok." />
        @endif
    </div>

    {{-- Modal: Tambah Bahan --}}
    @can('manage-ingredients')
        <x-modal id="create-ingredient-modal" title="Tambah Bahan Baku" icon="plus" size="md">
            <form id="create-ingredient-form" method="POST" action="{{ route('ingredients.store') }}">
                @csrf
                <input type="hidden" name="modal" value="create-ingredient-modal">
                @include('ingredients._form', ['fieldId' => 'create-modal'])
            </form>
            <x-slot:footer>
                <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#374151]">Batal</button>
                <button type="submit" form="create-ingredient-form" class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 hover:bg-brand-hover">
                    <x-icon name="check" :size="14" :stroke="2.4" />
                    Simpan Bahan
                </button>
            </x-slot:footer>
        </x-modal>
    @endcan

    {{-- Modal: Detail Bahan --}}
    <x-modal id="detail-ingredient-modal" title="Detail Bahan Baku" icon="eye" size="md">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Nama</dt>
                <dd id="detail-name" class="mt-1 text-sm font-bold text-[#142d70]">—</dd>
            </div>
            <div>
                <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Status Stok</dt>
                <dd id="detail-status" class="mt-1 text-sm font-bold text-[#142d70]">—</dd>
            </div>
            <div>
                <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Stok Tersedia</dt>
                <dd id="detail-stock" class="mt-1 text-sm font-semibold text-[#374151]">—</dd>
            </div>
            <div>
                <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Stok Minimum</dt>
                <dd id="detail-minimum" class="mt-1 text-sm font-semibold text-[#374151]">—</dd>
            </div>
            <div>
                <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Harga Modal</dt>
                <dd id="detail-cost" class="mt-1 text-sm font-semibold text-[#374151]">—</dd>
            </div>
            <div>
                <dt class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Terakhir Diperbarui</dt>
                <dd id="detail-updated" class="mt-1 text-sm font-semibold text-[#374151]">—</dd>
            </div>
        </dl>
        <x-slot:footer>
            <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#374151]">Tutup</button>
        </x-slot:footer>
    </x-modal>

    {{-- Modal: Ubah Bahan --}}
    @can('manage-ingredients')
        @foreach ($ingredients as $ingredient)
            <x-modal id="edit-ingredient-{{ $ingredient->id }}" title="Ubah Bahan Baku" icon="pencil" size="md">
                <form id="edit-ingredient-form-{{ $ingredient->id }}" method="POST" action="{{ route('ingredients.update', $ingredient) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="modal" value="edit-ingredient-{{ $ingredient->id }}">
                    @include('ingredients._form', ['ingredient' => $ingredient, 'fieldId' => 'edit-modal-'.$ingredient->id])
                </form>
                <x-slot:footer>
                    <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#374151]">Batal</button>
                    <button type="submit" form="edit-ingredient-form-{{ $ingredient->id }}" class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 hover:bg-brand-hover">
                        <x-icon name="check" :size="14" :stroke="2.4" />
                        Simpan Perubahan
                    </button>
                </x-slot:footer>
            </x-modal>
        @endforeach
    @endcan

    {{-- Modal: Konfirmasi perubahan status --}}
    @can('manage-ingredients')
        <x-modal id="status-ingredient-modal" title="Ubah Status Bahan Baku" icon="trash" size="md">
            <div class="flex flex-col items-center text-center">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-danger-soft text-danger">
                    <x-icon name="trash" :size="20" />
                </span>
                <p class="mt-3 text-sm font-bold text-[#142d70]"><span id="status-modal-question">Nonaktifkan bahan ini?</span></p>
                <p class="mt-1.5 max-w-sm text-xs leading-relaxed text-slate-500">Data bahan tidak dihapus. Bahan yang dinonaktifkan tidak dapat dipakai dalam resep maupun pembelian baru.</p>
            </div>
            <form id="status-ingredient-form" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="is_active" value="0">
            </form>
            <x-slot:footer>
                <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#374151]">Batal</button>
                <button type="submit" form="status-ingredient-form" class="rounded-lg bg-danger px-4 py-2.5 text-xs font-bold text-white transition hover:bg-[#b91c1c]">
                    <span id="status-modal-submit">Ya, nonaktifkan</span>
                </button>
            </x-slot:footer>
        </x-modal>
    @endcan

    <script>
        (function () {
            const openCreate = document.getElementById('open-create-ingredient');
            const createModal = document.getElementById('create-ingredient-modal');
            if (openCreate && createModal) {
                openCreate.addEventListener('click', function (event) {
                    event.preventDefault();
                    createModal.openModal();
                });
            }

            document.querySelectorAll('.open-edit').forEach(function (button) {
                button.addEventListener('click', function () {
                    const modal = document.getElementById(button.dataset.modal);
                    if (modal) modal.openModal();
                });
            });

            const detailModal = document.getElementById('detail-ingredient-modal');
            document.querySelectorAll('.open-detail').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.getElementById('detail-name').textContent = button.dataset.name;
                    document.getElementById('detail-status').textContent = button.dataset.status;
                    document.getElementById('detail-stock').textContent = button.dataset.stock + ' ' + button.dataset.unit;
                    document.getElementById('detail-minimum').textContent = button.dataset.minimum + ' ' + button.dataset.unit;
                    document.getElementById('detail-cost').textContent = button.dataset.cost;
                    document.getElementById('detail-updated').textContent = button.dataset.updated;
                    detailModal.openModal();
                });
            });

            const statusModal = document.getElementById('status-ingredient-modal');
            const statusForm = document.getElementById('status-ingredient-form');
            document.querySelectorAll('.open-status').forEach(function (button) {
                button.addEventListener('click', function () {
                    const isActive = button.dataset.active === '1';
                    statusForm.action = button.dataset.url;
                    statusForm.querySelector('input[name="is_active"]').value = button.dataset.active;
                    document.getElementById('status-modal-question').textContent = (isActive ? 'Nonaktifkan' : 'Aktifkan') + ' ' + button.dataset.name + '?';
                    document.getElementById('status-modal-submit').textContent = isActive ? 'Ya, nonaktifkan' : 'Ya, aktifkan';
                    statusModal.openModal();
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') return;
                document.querySelectorAll('.modal-root:not(.hidden)').forEach(function (modal) {
                    modal.closeModal();
                });
            });

            @if (old('modal'))
                (function () {
                    const modal = document.getElementById(@json(old('modal')));
                    if (modal) modal.openModal();
                })();
            @endif
        })();
    </script>
@endsection
