@extends('layouts.app')

@section('title', 'Pengelolaan Menu')
@section('breadcrumb', 'Pengelolaan Menu')

@section('content')
    @php
        $formatRupiah = fn ($amount): string => 'Rp'.number_format((float) $amount, 0, ',', '.');
    @endphp

    <div class="mb-7">
        <h2 class="text-[22px] font-extrabold tracking-tight text-[#142d70]">Pengelolaan Menu</h2>
        <p class="mt-1 text-sm text-slate-500">Kelola katalog, harga, dan ketersediaan menu.</p>
    </div>

    {{-- Ringkasan --}}
    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-stat-card label="Total Menu" :value="(string) $menuStats['total']" icon="book" tone="blue"
                     hint="Seluruh menu yang tercatat" />
        <x-stat-card label="Menu Aktif" :value="(string) $menuStats['aktif']" icon="check" tone="green"
                     hint="Ditampilkan kepada pelanggan" />
        <x-stat-card label="Menu Nonaktif" :value="(string) $menuStats['nonaktif']" icon="power" tone="red"
                     hint="Tidak ditampilkan" />
    </div>

    <div class="rounded-2xl border border-line bg-white p-5 shadow-card">
        <div class="mb-5">
            <h3 class="text-sm font-bold text-[#142d70]">Katalog Menu</h3>
            <p class="mt-0.5 text-[11px] text-slate-500">Atur menu yang ditampilkan untuk pelanggan.</p>
        </div>

        {{-- Toolbar --}}
        <div class="mb-5 flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('menus.index') }}" class="flex min-w-0 flex-1 flex-wrap items-center gap-3">
                <div class="relative min-w-[220px] flex-1">
                    <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-icon name="search" :size="16" />
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari data..."
                           aria-label="Cari menu"
                           class="h-10 w-full rounded-lg border border-line-input bg-white pl-10 pr-10 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
                    @if ($search !== '')
                        <a href="{{ route('menus.index', array_filter(['status' => $status])) }}" aria-label="Hapus pencarian"
                           class="absolute right-3 top-1/2 -translate-y-1/2 rounded p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                            <x-icon name="x" :size="14" />
                        </a>
                    @endif
                </div>

                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-icon name="filter" :size="15" />
                    </span>
                    <select name="status" aria-label="Filter status"
                            class="h-10 appearance-none rounded-lg border border-line-input bg-white pl-9 pr-9 text-sm font-semibold text-[#4b5563] outline-none transition focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
                        <option value="">Semua status</option>
                        <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                        <x-icon name="chevron-down" :size="14" />
                    </span>
                </div>

                <button type="submit"
                        class="flex h-10 items-center gap-2 rounded-lg border border-line-input bg-white px-4 text-xs font-bold text-[#4b5563] transition hover:border-brand hover:text-brand">
                    Filter
                </button>
            </form>

            @can('manage-menus')
                <button type="button" id="open-create-menu"
                        class="flex h-10 shrink-0 items-center gap-2 rounded-lg bg-brand px-4 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:-translate-y-px hover:bg-brand-hover">
                    <x-icon name="plus" :size="15" :stroke="2.4" />
                    Tambah Menu
                </button>
            @endcan
        </div>

        {{-- Grid kartu menu --}}
        @if ($menus->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2">
                {{-- `cardMenu` sengaja tidak bernama `$menu`: variabel loop Blade bocor
                     ke include modal di bawah dan membuat modal Tambah jadi form edit. --}}
                @foreach ($menus as $cardMenu)
                    <article class="flex flex-col overflow-hidden rounded-xl border border-line bg-white transition hover:-translate-y-px hover:shadow-card">
                        <div class="relative h-[145px] overflow-hidden bg-[#f1f5f9]">
                            <div class="absolute inset-0 flex flex-col items-center justify-center gap-1 text-slate-400">
                                <x-icon name="chef" :size="28" />
                                <span class="text-[11px] font-semibold">Foto menu</span>
                            </div>
                            @if ($cardMenu->image_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($cardMenu->image_path) }}"
                                     alt="Foto {{ $cardMenu->name }}" class="relative h-full w-full object-cover"
                                     onerror="this.remove()">
                            @endif
                            <div class="absolute right-3 top-3 z-10">
                                <x-badge :tone="$cardMenu->is_active ? 'success' : 'danger'">
                                    {{ $cardMenu->is_active ? 'Aktif' : 'Nonaktif' }}
                                </x-badge>
                            </div>
                        </div>

                        <div class="flex flex-1 flex-col p-4">
                            <p class="text-[11px] font-bold uppercase tracking-[0.1em] text-brand">Menu</p>
                            <h4 class="mt-1 truncate text-sm font-bold text-[#1f2937]">{{ $cardMenu->name }}</h4>
                            @if ($cardMenu->description)
                                <p class="mt-1 line-clamp-2 text-[11px] leading-relaxed text-slate-500">{{ $cardMenu->description }}</p>
                            @endif
                            <p class="mt-2 text-sm font-extrabold text-brand">{{ $formatRupiah($cardMenu->selling_price) }}</p>

                            <div class="mt-4 flex items-center gap-2">
                                <button type="button"
                                        class="open-detail flex h-9 flex-1 items-center justify-center gap-2 rounded-lg border border-line-input bg-white text-xs font-bold text-[#4b5563] transition hover:border-brand hover:text-brand"
                                        data-id="{{ $cardMenu->id }}"
                                        data-url="{{ route('menus.show', $cardMenu) }}?partial=1"
                                        aria-label="Lihat detail {{ $cardMenu->name }}">
                                    <x-icon name="eye" :size="15" />
                                    Detail
                                </button>
                                @can('manage-menus')
                                    <button type="button"
                                            class="open-edit flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-line-input bg-white text-slate-500 transition hover:border-brand hover:text-brand"
                                            data-id="{{ $cardMenu->id }}"
                                            data-url="{{ route('menus.edit', $cardMenu) }}?partial=1"
                                            aria-label="Ubah {{ $cardMenu->name }}">
                                        <x-icon name="pencil" :size="15" />
                                    </button>
                                    <button type="button"
                                            class="open-status flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-line-input bg-white text-danger transition hover:border-danger hover:bg-danger-soft"
                                            data-id="{{ $cardMenu->id }}"
                                            data-name="{{ $cardMenu->name }}"
                                            data-active="{{ $cardMenu->is_active ? '1' : '0' }}"
                                            aria-label="{{ $cardMenu->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $cardMenu->name }}">
                                        <x-icon name="trash" :size="15" />
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <x-empty-state icon="chef" title="Belum ada menu"
                           description="Tambahkan menu pertama untuk mulai mengelola katalog usaha." />
            @can('manage-menus')
                <div class="mt-1 text-center">
                    <button type="button" id="open-create-empty"
                            class="rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white transition hover:bg-brand-hover">
                        Tambah Menu
                    </button>
                </div>
            @endcan
        @endif

        {{ $menus->links('menus.partials.pagination') }}
    </div>

    {{-- Modal Tambah --}}
    @can('manage-menus')
        <x-modal id="create-menu-modal" title="Tambah Menu" icon="plus" size="md">
            <div id="create-menu-form">
                @include('menus.partials.form', ['isEdit' => false, 'menu' => null])
            </div>
            <x-slot:footer>
                <button type="button"
                        class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" form="create-menu-form-element"
                        class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-60">
                    <x-icon name="check" :size="14" :stroke="2.4" />
                    Simpan Menu
                </button>
            </x-slot:footer>
        </x-modal>

        {{-- Modal Ubah --}}
        <x-modal id="edit-menu-modal" title="Ubah Menu" icon="pencil" size="md">
            <div id="edit-menu-form">
                @if ($failedMenu)
                    @include('menus.partials.form', ['isEdit' => true, 'menu' => $failedMenu])
                @else
                    <x-empty-state icon="pencil" title="Memuat form..." description="Harap tunggu." />
                @endif
            </div>
            <x-slot:footer>
                <button type="button"
                        class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" form="edit-menu-form-element"
                        class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-60">
                    <x-icon name="check" :size="14" :stroke="2.4" />
                    Simpan Perubahan
                </button>
            </x-slot:footer>
        </x-modal>

        {{-- Modal Konfirmasi Status (bukan hapus permanen, sesuai PRD) --}}
        <x-modal id="status-menu-modal" title="Ubah Status Menu" icon="power" size="sm">
            <div class="flex flex-col items-center px-2 py-3 text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-danger-soft text-danger">
                    <x-icon name="trash" :size="24" />
                </span>
                <h4 class="mt-4 text-base font-bold text-[#1f2937]">Ubah status menu ini?</h4>
                <p id="status-menu-message" class="mt-2 max-w-sm text-xs leading-relaxed text-slate-500"></p>
                <p class="mt-2 max-w-sm text-[11px] leading-relaxed text-slate-400">
                    Data menu tidak dihapus permanen dan dapat diaktifkan kembali kapan saja.
                </p>
                <form id="menu-status-form" method="POST" class="hidden">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="1">
                </form>
            </div>
            <x-slot:footer>
                <button type="button"
                        class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" form="menu-status-form" id="status-menu-submit"
                        class="rounded-lg bg-danger px-4 py-2.5 text-xs font-bold text-white transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-60">
                    Ya, nonaktifkan
                </button>
            </x-slot:footer>
        </x-modal>
    @endcan

    {{-- Modal Detail --}}
    <x-modal id="detail-menu-modal" title="Detail Menu" icon="eye" size="md">
        <div id="detail-menu-content">
            <x-empty-state icon="eye" title="Memuat detail..." description="Harap tunggu." />
        </div>
        <x-slot:footer>
            <button type="button"
                    class="modal-close rounded-lg border border-line-input bg-white px-4 py-2.5 text-xs font-bold text-[#4b5563] transition hover:bg-slate-50">
                Batal
            </button>
        </x-slot:footer>
    </x-modal>

    <script>
        (function () {
            const createModal = document.getElementById('create-menu-modal');
            const editModal = document.getElementById('edit-menu-modal');
            const detailModal = document.getElementById('detail-menu-modal');
            const statusModal = document.getElementById('status-menu-modal');

            const createForm = document.getElementById('create-menu-form');
            const editForm = document.getElementById('edit-menu-form');
            const detailContent = document.getElementById('detail-menu-content');

            const createUrl = @json(route('menus.create', ['partial' => 1]));
            const editUrlTemplate = @json(route('menus.edit', ['menu' => '__MENU_ID__', 'partial' => 1]));
            const detailUrlTemplate = @json(route('menus.show', ['menu' => '__MENU_ID__', 'partial' => 1]));
            const statusUrlTemplate = @json(route('menus.status', ['menu' => '__MENU_ID__']));

            const loading = '<div class="py-10 text-center text-xs text-slate-500">Memuat data...</div>';
            const failed = '<div class="py-10 text-center text-xs font-semibold text-danger">Gagal memuat data. Silakan coba lagi.</div>';

            const loadFragment = async (url, target) => {
                target.innerHTML = loading;
                try {
                    const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!response.ok) throw new Error('Request failed');
                    target.innerHTML = await response.text();
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
                loadFragment(editUrlTemplate.replace('__MENU_ID__', id), editForm);
                editModal.openModal();
            };

            const openDetail = (id) => {
                loadFragment(detailUrlTemplate.replace('__MENU_ID__', id), detailContent);
                detailModal.openModal();
            };

            document.querySelectorAll('#open-create-menu, #open-create-empty').forEach((button) => {
                button.addEventListener('click', openCreate);
            });

            document.querySelectorAll('.open-edit').forEach((button) => {
                button.addEventListener('click', () => openEdit(button.dataset.id));
            });

            document.querySelectorAll('.open-detail').forEach((button) => {
                button.addEventListener('click', () => openDetail(button.dataset.id));
            });

            if (statusModal) {
                const statusForm = document.getElementById('menu-status-form');
                const statusMessage = document.getElementById('status-menu-message');
                const statusSubmit = document.getElementById('status-menu-submit');

                document.querySelectorAll('.open-status').forEach((button) => {
                    button.addEventListener('click', () => {
                        const isActive = button.dataset.active === '1';
                        statusForm.action = statusUrlTemplate.replace('__MENU_ID__', button.dataset.id);
                        statusForm.querySelector('input[name="is_active"]').value = isActive ? '0' : '1';
                        statusMessage.textContent = isActive
                            ? `Menu "${button.dataset.name}" akan dinonaktifkan dan tidak ditampilkan kepada pelanggan.`
                            : `Menu "${button.dataset.name}" akan diaktifkan kembali.`;
                        statusSubmit.textContent = isActive ? 'Ya, nonaktifkan' : 'Ya, aktifkan';
                        statusSubmit.className = isActive
                            ? 'rounded-lg bg-danger px-4 py-2.5 text-xs font-bold text-white transition hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-60'
                            : 'rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-60';
                        statusModal.openModal();
                    });
                });

                statusModal.addEventListener('submit', (event) => {
                    if (event.target.id === 'menu-status-form') setBusy(statusModal, true);
                });
            }

            [createModal, editModal].forEach((modal) => {
                if (!modal) return;
                modal.addEventListener('submit', (event) => {
                    if (event.target.id === 'create-menu-form-element' || event.target.id === 'edit-menu-form-element') {
                        setBusy(modal, true);
                    }
                });
            });

            // Buka kembali modal bila validasi gagal. Form sudah dirender server
            // bersama old input dan pesan error, jadi tidak di-fetch ulang.
            const failedModal = @json(old('form_modal'));
            const failedMenuId = @json(old('menu_id'));
            if (failedModal === 'create-menu-modal') createModal.openModal();
            if (failedModal === 'edit-menu-modal' && failedMenuId) editModal.openModal();
        })();
    </script>
@endsection
