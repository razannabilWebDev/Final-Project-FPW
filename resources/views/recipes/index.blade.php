@extends('layouts.app')

@section('title', 'Pengelolaan Resep')
@section('breadcrumb', 'Pengelolaan Resep')

@section('content')
    @php
        $rupiah = fn ($value): string => \App\Support\Rupiah::format($value);
    @endphp

    <div class="mb-7">
        <h2 class="text-[22px] font-extrabold tracking-tight text-[#142d70]">Pengelolaan Resep</h2>
        <p class="mt-1 text-sm text-slate-500">Kontrol komposisi dan modal setiap menu.</p>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-stat-card label="Total Resep" :value="(string) $stats['total']" icon="clipboard" tone="blue"
                     hint="Menu dengan resep aktif" />
        <x-stat-card label="Rata-rata Modal" :value="$rupiah($stats['average_cost'])" icon="package" tone="orange"
                     hint="HPP per porsi (D4)" />
        <x-stat-card label="Potensi Margin" :value="number_format($stats['margin'], 1, ',', '.').'%'" icon="trending"
                     tone="green" hint="Rata-rata margin per menu" />
    </div>

    <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
        <div class="mb-5">
            <h3 class="text-sm font-bold text-[#142d70]">Daftar Resep Menu</h3>
            <p class="mt-0.5 text-[11px] text-slate-500">Menampilkan {{ $menus->count() }} dari {{ $menus->total() }} resep.</p>
        </div>

        <div class="mb-5 flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('recipes.index') }}" class="flex min-w-0 flex-1 flex-wrap items-center gap-3">
                <div class="relative min-w-[220px] flex-1">
                    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-icon name="search" :size="16" />
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari data..." aria-label="Cari resep"
                           class="h-10 w-full rounded-lg border border-line-input bg-white pl-10 pr-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
                </div>
                <button type="submit"
                        class="flex h-10 items-center gap-2 rounded-lg border border-line-input bg-white px-4 text-xs font-bold text-[#4b5563] transition hover:border-brand hover:text-brand">
                    <x-icon name="filter" :size="15" />
                    Filter
                </button>
            </form>

            @can('manage-recipes')
                <button type="button" id="open-create-recipe"
                        class="flex h-10 shrink-0 items-center gap-2 rounded-lg bg-brand px-4 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:-translate-y-px hover:bg-brand-hover">
                    <x-icon name="plus" :size="15" :stroke="2.4" />
                    Tambah Resep
                </button>
            @endcan
        </div>

        @if ($menus->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-xs">
                    <thead>
                        <tr class="border-b border-line bg-head-bg text-[11px] font-bold uppercase tracking-wide text-slate-500">
                            <th class="px-3 py-3">Menu</th>
                            <th class="px-3 py-3">Jumlah Bahan</th>
                            <th class="px-3 py-3">Total Modal</th>
                            <th class="px-3 py-3">Harga Jual</th>
                            <th class="px-3 py-3">Est. Laba</th>
                            <th class="px-3 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($menus as $recipeMenu)
                            @php
                                $recipe = $latestRecipes->get($recipeMenu->id);
                                $cost = (float) ($allCosts[$recipeMenu->id] ?? 0);
                                $profit = (float) $recipeMenu->selling_price - $cost;
                            @endphp
                            <tr class="border-b border-line last:border-0 hover:bg-slate-50/60">
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($recipeMenu->image_path)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($recipeMenu->image_path) }}"
                                                 alt="" class="h-9 w-9 rounded-lg object-cover"
                                                 onerror="this.style.visibility='hidden'">
                                        @else
                                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#f1f5f9] text-slate-400">
                                                <x-icon name="chef" :size="16" />
                                            </span>
                                        @endif
                                        <div>
                                            <p class="font-bold text-[#1f2937]">{{ $recipeMenu->name }}</p>
                                            <p class="mt-0.5 text-[11px] text-slate-400">Versi {{ $recipe?->version ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-slate-600">{{ $recipe?->items->count() ?? 0 }} bahan</td>
                                <td class="px-3 py-3 font-semibold text-[#d97706]">{{ $rupiah($cost) }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $rupiah($recipeMenu->selling_price) }}</td>
                                <td class="px-3 py-3 font-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">{{ $rupiah($profit) }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" class="open-detail-recipe rounded-lg border border-line-input p-1.5 text-brand transition hover:border-brand hover:bg-blue-50"
                                                data-id="{{ $recipe?->id }}"
                                                data-url="{{ route('recipes.show', $recipe) }}?partial=1"
                                                aria-label="Lihat resep {{ $recipeMenu->name }}">
                                            <x-icon name="eye" :size="15" />
                                        </button>
                                        @can('manage-recipes')
                                            <button type="button" class="open-edit-recipe rounded-lg border border-line-input p-1.5 text-slate-500 transition hover:border-brand hover:text-brand"
                                                    data-id="{{ $recipe?->id }}"
                                                    data-url="{{ route('recipes.edit', $recipe) }}?partial=1"
                                                    aria-label="Ubah resep {{ $recipeMenu->name }}">
                                                <x-icon name="pencil" :size="15" />
                                            </button>
                                            <button type="button" class="open-delete-recipe rounded-lg border border-line-input p-1.5 text-danger transition hover:border-danger hover:bg-danger-soft"
                                                    data-id="{{ $recipe?->id }}"
                                                    data-name="{{ $recipeMenu->name }}"
                                                    aria-label="Hapus resep {{ $recipeMenu->name }}">
                                                <x-icon name="trash" :size="15" />
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $menus->links('menus.partials.pagination') }}
        @else
            <x-empty-state icon="clipboard" title="Belum ada resep"
                           description="Tambahkan resep untuk menghitung modal per porsi suatu menu." />
            @can('manage-recipes')
                <div class="mt-1 text-center">
                    <button type="button" id="open-create-recipe-empty"
                            class="rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white transition hover:bg-brand-hover">
                        Tambah Resep
                    </button>
                </div>
            @endcan
        @endif
    </div>

    @can('manage-recipes')
        <x-modal id="create-recipe-modal" title="Tambah Resep" icon="plus" size="lg">
            <div id="create-recipe-form">
                @include('recipes.partials.form', [
                    'isEdit' => false,
                    'menu' => null,
                    'formId' => 'create-recipe-form-element',
                    'menus' => $formMenus,
                    'ingredients' => $formIngredients,
                    'units' => $units,
                ])
            </div>
            <x-slot:footer>
                <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">Batal</button>
                <button type="submit" form="create-recipe-form-element"
                        class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-60">
                    <x-icon name="check" :size="14" :stroke="2.4" />
                    Simpan Resep
                </button>
            </x-slot:footer>
        </x-modal>

        <x-modal id="edit-recipe-modal" title="Ubah Resep" icon="pencil" size="lg">
            <div id="edit-recipe-form">
                <x-empty-state icon="pencil" title="Memuat form..." description="Harap tunggu." />
            </div>
            <x-slot:footer>
                <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">Batal</button>
                <button type="submit" form="edit-recipe-form-element"
                        class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-60">
                    <x-icon name="check" :size="14" :stroke="2.4" />
                    Simpan Perubahan
                </button>
            </x-slot:footer>
        </x-modal>

        <x-modal id="delete-recipe-modal" title="Hapus Resep" icon="trash" size="sm">
            <div class="flex flex-col items-center px-2 py-3 text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-danger-soft text-danger">
                    <x-icon name="trash" :size="24" />
                </span>
                <h4 class="mt-4 text-base font-bold text-[#1f2937]">Hapus resep ini?</h4>
                <p id="delete-recipe-message" class="mt-2 max-w-sm text-xs leading-relaxed text-slate-500"></p>
                <p class="mt-2 max-w-sm text-[11px] leading-relaxed text-slate-400">
                    Seluruh versi resep menu akan dinonaktifkan. Riwayat transaksi dan HPP yang sudah tercatat tidak berubah.
                </p>
                <form id="delete-recipe-form" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            <x-slot:footer>
                <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">Batal</button>
                <button type="submit" form="delete-recipe-form" id="delete-recipe-submit"
                        class="rounded-lg bg-danger px-4 py-2.5 text-xs font-bold text-white transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-60">
                    Ya, hapus resep
                </button>
            </x-slot:footer>
        </x-modal>
    @endcan

    <x-modal id="detail-recipe-modal" title="Detail Resep" icon="eye" size="lg">
        <div id="detail-recipe-content">
            <x-empty-state icon="eye" title="Memuat detail..." description="Harap tunggu." />
        </div>
        <x-slot:footer>
            <button type="button" class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">Batal</button>
        </x-slot:footer>
    </x-modal>

    <script>
        (function () {
            const createModal = document.getElementById('create-recipe-modal');
            const editModal = document.getElementById('edit-recipe-modal');
            const detailModal = document.getElementById('detail-recipe-modal');
            const deleteModal = document.getElementById('delete-recipe-modal');
            const createForm = document.getElementById('create-recipe-form');
            const editForm = document.getElementById('edit-recipe-form');
            const detailContent = document.getElementById('detail-recipe-content');

            const createUrl = @json(route('recipes.create', ['partial' => 1]));
            const editUrlTemplate = @json(route('recipes.edit', ['recipe' => '__RECIPE_ID__', 'partial' => 1]));
            const detailUrlTemplate = @json(route('recipes.show', ['recipe' => '__RECIPE_ID__', 'partial' => 1]));
            const deleteUrlTemplate = @json(route('recipes.destroy', ['recipe' => '__RECIPE_ID__']));

            const loading = '<div class="py-10 text-center text-xs text-slate-500">Memuat data...</div>';
            const failed = '<div class="py-10 text-center text-xs font-semibold text-danger">Gagal memuat data. Silakan coba lagi.</div>';
            const baseUnits = @json($formIngredients->mapWithKeys(fn ($ingredient): array => [(string) $ingredient->id => $ingredient->base_unit]));

            const initRecipeItems = (root) => {
                const container = root.matches('[data-recipe-items]')
                    ? root
                    : root.querySelector('[data-recipe-items]');
                if (!container || container.dataset.ready === '1') return;
                container.dataset.ready = '1';

                // Pasang listener di form agar tombol "Tambah Bahan" (di dalam
                // container) tetap tertangkap, termasuk form hasil fetch AJAX.
                const listenerRoot = container.closest('form') ?? container;
                const template = container.querySelector('[data-item-template]');
                const syncUnit = (row) => {
                    const select = row.querySelector('[data-ingredient-select]');
                    const unit = row.querySelector('[data-unit-input]');
                    if (select && unit) unit.value = baseUnits[select.value] ?? '';
                };

                container.querySelectorAll('[data-recipe-row]').forEach(syncUnit);

                listenerRoot.addEventListener('change', (event) => {
                    if (event.target.matches('[data-ingredient-select]')) {
                        syncUnit(event.target.closest('[data-recipe-row]'));
                    }
                });

                listenerRoot.addEventListener('click', (event) => {
                    const remove = event.target.closest('[data-remove-item]');
                    if (remove) {
                        remove.closest('[data-recipe-row]').remove();
                        return;
                    }

                    const add = event.target.closest('[data-add-item]');
                    if (add && template) {
                        const indexes = [...container.querySelectorAll('[data-recipe-row]')]
                            .map((row) => parseInt(row.dataset.index, 10) || 0);
                        const index = indexes.length ? Math.max(...indexes) + 1 : 0;
                        container.insertAdjacentHTML('beforeend', template.innerHTML.replace(/__INDEX__/g, index));
                        syncUnit(container.lastElementChild);
                    }
                });
            };

            const initRecipeForm = (formRoot) => {
                if (!formRoot) return;
                const container = formRoot.querySelector('[data-recipe-items]');
                if (container) initRecipeItems(container);
            };

            initRecipeForm(createForm);

            const loadFragment = async (url, target) => {
                target.innerHTML = loading;
                try {
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!response.ok) throw new Error('Request failed');
                    target.innerHTML = await response.text();
                    target.querySelectorAll('form').forEach(initRecipeForm);
                } catch (error) {
                    target.innerHTML = failed;
                }
            };

            const setBusy = (modal, busy) => {
                modal.dataset.busy = busy ? 'true' : 'false';
                modal.querySelectorAll('button[type="submit"]').forEach((button) => { button.disabled = busy; });
            };

            const openCreate = () => {
                loadFragment(createUrl, createForm);
                createModal.openModal();
            };

            const openEdit = (id) => {
                loadFragment(editUrlTemplate.replace('__RECIPE_ID__', id), editForm);
                editModal.openModal();
            };

            const openDetail = (id) => {
                loadFragment(detailUrlTemplate.replace('__RECIPE_ID__', id), detailContent);
                detailModal.openModal();
            };

            document.querySelectorAll('#open-create-recipe, #open-create-recipe-empty').forEach((button) => {
                button.addEventListener('click', openCreate);
            });

            document.querySelectorAll('.open-edit-recipe').forEach((button) => {
                button.addEventListener('click', () => openEdit(button.dataset.id));
            });

            document.querySelectorAll('.open-detail-recipe').forEach((button) => {
                button.addEventListener('click', () => openDetail(button.dataset.id));
            });

            if (deleteModal) {
                const deleteForm = document.getElementById('delete-recipe-form');
                const deleteMessage = document.getElementById('delete-recipe-message');

                document.querySelectorAll('.open-delete-recipe').forEach((button) => {
                    button.addEventListener('click', () => {
                        deleteForm.action = deleteUrlTemplate.replace('__RECIPE_ID__', button.dataset.id);
                        deleteMessage.textContent = `Resep "${button.dataset.name}" beserta seluruh versinya akan dihapus.`;
                        deleteModal.openModal();
                    });
                });

                deleteModal.addEventListener('submit', (event) => {
                    if (event.target.id === 'delete-recipe-form') setBusy(deleteModal, true);
                });
            }

            [createModal, editModal].forEach((modal) => {
                if (!modal) return;
                modal.addEventListener('submit', (event) => {
                    if (event.target.id === 'create-recipe-form-element' || event.target.id === 'edit-recipe-form-element') {
                        setBusy(modal, true);
                    }
                });
            });

            const failedModal = @json(old('form_modal'));
            const failedId = @json(old('recipe_id'));
            if (failedModal === 'create-recipe-modal') openCreate();
            if (failedModal === 'edit-recipe-modal' && failedId) openEdit(failedId);
        })();
    </script>
@endsection
