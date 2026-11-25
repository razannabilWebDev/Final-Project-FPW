@php
    $items = old('items', [['menu_id' => '', 'quantity' => 1]]);
    $menus = $menus ?? collect();
@endphp

<form method="POST" action="{{ route('sales.store') }}" id="sale-form" class="space-y-5">
    @csrf

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="type" class="mb-2 block text-xs font-bold text-[#374151]">Jenis Penjualan</label>
            <select id="type" name="type" class="h-11 w-full rounded-[10px] border border-line-input bg-white px-3 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
                <option value="menu" @selected(old('type', 'menu') === 'menu')>Berdasarkan Menu</option>
                <option value="manual" @selected(old('type') === 'manual')>Penjualan Manual</option>
            </select>
        </div>
        <div>
            <label for="payment_method" class="mb-2 block text-xs font-bold text-[#374151]">Metode Pembayaran</label>
            <select id="payment_method" name="payment_method" class="h-11 w-full rounded-[10px] border border-line-input bg-white px-3 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
                <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>Tunai</option>
                <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer</option>
                <option value="qris" @selected(old('payment_method') === 'qris')>QRIS</option>
            </select>
        </div>
    </div>

    <div id="menu-section">
        <h4 class="mb-2 text-xs font-bold text-[#142d70]">Item Menu</h4>
        <p class="mb-3 text-[11px] text-slate-500">Pilih menu dari daftar. Harga jual otomatis mengikuti harga master menu.</p>
        <div id="items" class="space-y-3">
            @foreach ($items as $i => $item)
                <div class="item-row grid gap-3 rounded-xl border border-line p-3 sm:grid-cols-[2fr_1fr_auto]">
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-500">Menu</label>
                        <select name="items[{{ $i }}][menu_id]" required
                            class="sale-menu h-10 w-full rounded-lg border border-line-input bg-white px-3 text-sm outline-none focus:border-brand">
                            <option value="">Pilih menu</option>
                            @foreach ($menus as $menu)
                                <option value="{{ $menu->id }}"
                                    data-name="{{ $menu->name }}"
                                    data-price="{{ $menu->selling_price }}"
                                    @selected(old('items.'.$i.'.menu_id', $item['menu_id'] ?? '') == $menu->id)>
                                    {{ $menu->name }} — Rp{{ number_format((float) $menu->selling_price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        <p class="item-price mt-1 text-[11px] font-semibold text-brand">
                            @if (($item['menu_id'] ?? '') !== '')
                                @php $chosen = $menus->firstWhere('id', (int) ($item['menu_id'] ?? 0)); @endphp
                                {{ $chosen ? 'Harga: Rp'.number_format((float) $chosen->selling_price, 0, ',', '.') : '' }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="mb-1 block text-[11px] font-semibold text-slate-500">Qty</label>
                        <input type="number" min="0.001" step="0.001" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" required
                            class="h-10 w-full rounded-lg border border-line-input px-3 text-sm outline-none focus:border-brand">
                    </div>
                    <div class="hidden">
                        <input type="hidden" name="items[{{ $i }}][menu_name]" value="{{ $item['menu_name'] ?? '' }}" class="item-menu-name">
                        <input type="hidden" name="items[{{ $i }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" class="item-unit-price">
                    </div>
                    <div class="flex items-end justify-end">
                        <button type="button" class="remove-item flex h-10 w-10 items-center justify-center rounded-lg border border-line text-danger transition hover:bg-danger-soft" aria-label="Hapus item">
                            <x-icon name="trash" :size="16" />
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <template id="sale-item-template">
            <div class="item-row grid gap-3 rounded-xl border border-line p-3 sm:grid-cols-[2fr_1fr_auto]">
                <div>
                    <label class="mb-1 block text-[11px] font-semibold text-slate-500">Menu</label>
                    <select name="items[__INDEX__][menu_id]" required
                        class="sale-menu h-10 w-full rounded-lg border border-line-input bg-white px-3 text-sm outline-none focus:border-brand">
                        <option value="">Pilih menu</option>
                        @foreach ($menus as $menu)
                            <option value="{{ $menu->id }}"
                                data-name="{{ $menu->name }}"
                                data-price="{{ $menu->selling_price }}">
                                {{ $menu->name }} — Rp{{ number_format((float) $menu->selling_price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    <p class="item-price mt-1 text-[11px] font-semibold text-brand"></p>
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-semibold text-slate-500">Qty</label>
                    <input type="number" min="0.001" step="0.001" name="items[__INDEX__][quantity]" value="1" required
                        class="h-10 w-full rounded-lg border border-line-input px-3 text-sm outline-none focus:border-brand">
                </div>
                <div class="hidden">
                    <input type="hidden" name="items[__INDEX__][menu_name]" class="item-menu-name">
                    <input type="hidden" name="items[__INDEX__][unit_price]" class="item-unit-price">
                </div>
                <div class="flex items-end justify-end">
                    <button type="button" class="remove-item flex h-10 w-10 items-center justify-center rounded-lg border border-line text-danger transition hover:bg-danger-soft" aria-label="Hapus item">
                        <x-icon name="trash" :size="16" />
                    </button>
                </div>
            </div>
        </template>

        <button type="button" id="add-item" class="mt-2 text-xs font-bold text-brand hover:text-brand-hover">+ Tambah item</button>
    </div>

    <div id="manual-section" class="hidden">
        <label for="total_nominal" class="mb-2 block text-xs font-bold text-[#374151]">Nominal Pendapatan</label>
        <input type="number" min="0" step="0.01" id="total_nominal" name="total_nominal" value="{{ old('total_nominal') }}" placeholder="0"
            class="h-11 w-full rounded-[10px] border border-line-input px-3 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">
        <p class="mt-1.5 text-[11px] text-slate-500">Penjualan manual tidak memiliki item dan tidak mengurangi stok.</p>
    </div>

    <div>
        <label for="note" class="mb-2 block text-xs font-bold text-[#374151]">Catatan (opsional)</label>
        <textarea id="note" name="note" rows="3" placeholder="Tambahkan catatan..."
            class="w-full rounded-[10px] border border-line-input px-3 py-2.5 text-sm outline-none focus:border-brand focus:ring-4 focus:ring-[#dbeafe]">{{ old('note') }}</textarea>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-danger-soft bg-[#fef2f2] px-4 py-3 text-xs text-[#b91c1c]">
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        (function () {
            const form = document.getElementById('sale-form');
            if (!form) return;

            const type = document.getElementById('type');
            const menuSection = document.getElementById('menu-section');
            const manualSection = document.getElementById('manual-section');
            const items = document.getElementById('items');
            const template = document.getElementById('sale-item-template');

            const toggle = () => {
                if (!type || !menuSection || !manualSection) return;
                const manual = type.value === 'manual';
                menuSection.classList.toggle('hidden', manual);
                manualSection.classList.toggle('hidden', !manual);
            };
            type?.addEventListener('change', toggle);
            toggle();

            const syncRow = (row) => {
                const select = row.querySelector('.sale-menu');
                if (!select) return;
                const option = select.selectedOptions[0];
                const hasMenu = !!option && !!option.value;
                row.querySelector('.item-menu-name').value = hasMenu ? (option.dataset.name ?? '') : '';
                row.querySelector('.item-unit-price').value = hasMenu ? (option.dataset.price ?? '') : '';
                row.querySelector('.item-price').textContent = hasMenu
                    ? 'Harga: Rp' + Number(option.dataset.price ?? 0).toLocaleString('id-ID')
                    : '';
            };

            // Sinkronisasi baris awal saat halaman dimuat (termasuk old input).
            items?.querySelectorAll('.item-row').forEach(syncRow);

            form.addEventListener('change', function (event) {
                if (!event.target.classList?.contains('sale-menu')) return;
                syncRow(event.target.closest('.item-row'));
            });

            let index = {{ count($items) }};
            document.getElementById('add-item')?.addEventListener('click', function () {
                if (!items || !template) return;
                items.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', String(index++)));
            });

            form.addEventListener('click', function (event) {
                const remove = event.target.closest('.remove-item');
                if (!remove) return;
                const rows = items?.querySelectorAll('.item-row') ?? [];
                if (rows.length > 1) remove.closest('.item-row').remove();
            });
        })();
    </script>
</form>
