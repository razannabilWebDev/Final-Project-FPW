@extends('layouts.app')

@section('title', 'Biaya Operasional')

@section('content')
    @php
        $rupiah = fn ($v) => 'Rp'.number_format((float) $v, 0, ',', '.');
        $lainnya = max(0, $total - $fixed);
    @endphp

    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Biaya Operasional</h1>
        <p class="mt-1 text-sm text-slate-500">Catat seluruh pengeluaran operasional usaha.</p>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-stat-card label="Total Pengeluaran" :value="$rupiah($total)" icon="wallet" tone="blue" hint="Sesuai filter periode" />
        <x-stat-card label="Biaya Tetap" :value="$rupiah($fixed)" icon="receipt" tone="orange" hint="Sewa, gaji, listrik/air" />
        <x-stat-card label="Biaya Lainnya" :value="$rupiah($lainnya)" icon="trending" tone="purple" hint="Kategori lainnya" />
    </div>

    <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-[#142d70]">Riwayat Pengeluaran</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Menampilkan {{ $expenses->count() }} dari {{ $expenses->total() }} data</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="open-category-modal" class="flex items-center gap-2 rounded-lg border border-line-input bg-white px-3 py-2.5 text-xs font-bold text-[#374151] hover:border-brand">
                    <x-icon name="settings" :size="14" />
                    Kelola Kategori
                </button>
                <button type="button" id="open-expense-modal" class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 hover:bg-brand-hover">
                    <x-icon name="plus" :size="15" :stroke="2.4" />
                    Tambah Biaya
                </button>
            </div>
        </div>

        {{-- Filter: period tabs + date range + kategori + search --}}
        <div class="mb-4 space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                @foreach (['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $key => $label)
                    <a href="{{ route('expenses.index', array_filter(['period' => $key, 'category' => request('category')])) }}"
                       class="rounded-lg px-3 py-1.5 text-xs font-bold transition {{ request('period') === $key ? 'bg-brand text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
                @if (request('period') || request('from') || request('to'))
                    <a href="{{ route('expenses.index') }}" class="text-xs font-bold text-brand hover:text-brand-hover">Reset</a>
                @endif
            </div>

            <form method="GET" class="flex flex-wrap gap-3">
                <div class="relative min-w-[180px] flex-1">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-icon name="search" :size="16" />
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari data..."
                        class="h-10 w-full rounded-lg border border-line-input pl-9 pr-3 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
                </div>
                <select name="category" class="h-10 rounded-lg border border-line-input bg-white px-3 text-sm outline-none focus:border-brand">
                    <option value="">Semua kategori</option>
                    @foreach ($allCategories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <div class="flex items-center gap-2">
                    <input type="date" name="from" value="{{ request('from') }}" aria-label="Dari tanggal"
                        class="h-10 rounded-lg border border-line-input px-2 text-xs outline-none focus:border-brand">
                    <span class="text-xs text-slate-400">s/d</span>
                    <input type="date" name="to" value="{{ request('to') }}" aria-label="Sampai tanggal"
                        class="h-10 rounded-lg border border-line-input px-2 text-xs outline-none focus:border-brand">
                </div>
                <button type="submit" class="h-10 rounded-lg border border-line-input bg-white px-4 text-xs font-bold text-[#374151]">Filter</button>
            </form>
        </div>

        @if ($expenses->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-line bg-head-bg text-[10px] font-bold uppercase tracking-wide text-slate-500">
                            <th class="px-3 py-3">Tanggal</th>
                            <th class="px-3 py-3">Keterangan</th>
                            <th class="px-3 py-3">Kategori</th>
                            <th class="px-3 py-3">Nominal</th>
                            <th class="px-3 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $expense)
                            <tr class="border-b border-line last:border-0">
                                <td class="px-3 py-3 text-slate-500">{{ $expense->expense_date->translatedFormat('d M Y') }}</td>
                                <td class="px-3 py-3 font-semibold text-[#374151]">{{ $expense->description ?? '-' }}</td>
                                <td class="px-3 py-3"><span class="rounded-full bg-[#ede9fe] px-2.5 py-1 text-[10px] font-bold text-[#7c3aed]">{{ $expense->category?->name }}</span></td>
                                <td class="px-3 py-3 font-bold text-[#142d70]">{{ $rupiah($expense->amount) }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="edit-expense text-brand hover:text-brand-hover" aria-label="Ubah"
                                                data-url="{{ route('expenses.update', $expense) }}"
                                                data-expense="{{ json_encode(['expense_date' => $expense->expense_date->toDateString(), 'expense_category_id' => $expense->expense_category_id, 'amount' => (string) $expense->amount, 'description' => $expense->description]) }}">
                                            <x-icon name="pencil" :size="15" />
                                        </button>
                                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Hapus biaya ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-danger hover:text-red-700" aria-label="Hapus"><x-icon name="trash" :size="15" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $expenses->links() }}</div>
        @else
            <x-empty-state icon="receipt" title="Belum ada biaya" description="Biaya operasional yang dicatat akan tampil di sini." />
        @endif
    </div>

    {{-- Modal Tambah/Edit Biaya --}}
    @php unset($expense); @endphp
    <x-modal id="expense-modal" title="Tambah Biaya" icon="receipt" size="md">
        <div id="expense-form-container">@include('expenses.partials.expense-form')</div>
        <x-slot:footer>
            <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#374151]">Batal</button>
            <button type="submit" form="expense-form" class="rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white hover:bg-brand-hover">Simpan Biaya</button>
        </x-slot:footer>
    </x-modal>

    {{-- Modal Kelola Kategori --}}
    <x-modal id="category-modal" title="Kelola Kategori" icon="settings" size="md">
        <div class="space-y-4">
            <div id="category-form-container">@include('expenses.partials.category-form')</div>
            <div class="border-t border-line pt-4">
                <p class="mb-2 text-[11px] font-bold uppercase tracking-wide text-slate-500">Daftar Kategori</p>
                <div class="space-y-2">
                    @foreach ($allCategories as $cat)
                        <div class="flex items-center justify-between rounded-lg border border-line px-3 py-2">
                            <div>
                                <p class="text-xs font-semibold text-[#374151] {{ $cat->is_active ? '' : 'line-through text-slate-400' }}">{{ $cat->name }}</p>
                                <p class="text-[10px] text-slate-400">{{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" class="edit-category text-brand hover:text-brand-hover" aria-label="Ubah kategori"
                                        data-url="{{ route('expense-categories.update', $cat) }}"
                                        data-category="{{ json_encode(['name' => $cat->name, 'description' => $cat->description, 'is_active' => $cat->is_active]) }}">
                                    <x-icon name="pencil" :size="14" />
                                </button>
                                <form method="POST" action="{{ route('expense-categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-danger hover:text-red-700" aria-label="Hapus kategori"><x-icon name="trash" :size="14" /></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <x-slot:footer>
            <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#374151]">Tutup</button>
            <button type="submit" form="category-form" class="rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white hover:bg-brand-hover">Simpan Kategori</button>
        </x-slot:footer>
    </x-modal>

    <script>
        (function () {
            const expenseModal = document.getElementById('expense-modal');
            const expenseContainer = document.getElementById('expense-form-container');
            const expenseTitle = document.getElementById('expense-modal-title');
            const openExpense = document.getElementById('open-expense-modal');
            const createFormHtml = expenseContainer.innerHTML;

            if (openExpense) {
                openExpense.addEventListener('click', function () {
                    expenseContainer.innerHTML = createFormHtml;
                    if (expenseTitle) expenseTitle.textContent = 'Tambah Biaya';
                    expenseModal.openModal();
                });
            }

            document.querySelectorAll('.edit-expense').forEach(function (button) {
                button.addEventListener('click', function () {
                    const data = JSON.parse(button.dataset.expense);
                    expenseContainer.innerHTML = createFormHtml;
                    const f = document.getElementById('expense-form');
                    if (f) {
                        f.action = button.dataset.url;
                        const method = f.querySelector('input[name="_method"]');
                        if (method) method.value = 'PUT';
                        document.getElementById('expense_date').value = data.expense_date;
                        document.getElementById('expense_category_id').value = data.expense_category_id;
                        document.getElementById('amount').value = data.amount;
                        document.getElementById('description').value = data.description ?? '';
                    }
                    if (expenseTitle) expenseTitle.textContent = 'Ubah Biaya';
                    expenseModal.openModal();
                });
            });

            const categoryModal = document.getElementById('category-modal');
            const openCategory = document.getElementById('open-category-modal');
            if (openCategory) {
                openCategory.addEventListener('click', function () { categoryModal.openModal(); });
            }
            document.querySelectorAll('.edit-category').forEach(function (button) {
                button.addEventListener('click', function () {
                    const data = JSON.parse(button.dataset.category);
                    const form = document.getElementById('category-form');
                    if (form) {
                        form.action = button.dataset.url;
                        const method = form.querySelector('input[name="_method"]');
                        if (method) method.value = 'PUT';
                        document.getElementById('cat_name').value = data.name;
                        document.getElementById('cat_description').value = data.description ?? '';
                        const active = form.querySelector('input[name="is_active"]');
                        if (active) active.checked = !!data.is_active;
                    }
                    categoryModal.openModal();
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') return;
                document.querySelectorAll('.modal-root:not(.hidden)').forEach(function (modal) { modal.closeModal(); });
            });

            @if(request()->query('modal') === 'expense-modal')
                document.addEventListener('DOMContentLoaded', function () { expenseModal.openModal(); });
            @endif
            @if(request()->query('modal') === 'category-modal')
                document.addEventListener('DOMContentLoaded', function () { categoryModal.openModal(); });
            @endif
        })();
    </script>
@endsection
