@extends('layouts.app')

@section('title', 'Input Pesanan')

@section('content')
    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Input Pesanan</h1>
        <p class="mt-1 text-sm text-slate-500">Catat penjualan berbasis menu atau penjualan manual.</p>
    </div>

    <form method="POST" action="{{ route('sales.store') }}" id="sale-form" class="rounded-2xl border border-[#ecece8] bg-white p-6 shadow-sm">
        @csrf

        <div class="mb-6 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="type" class="mb-2 block text-xs font-bold text-[#374151]">Jenis Penjualan</label>
                <select id="type" name="type" class="h-12 w-full rounded-[10px] border border-[#dfe3e8] bg-white px-3 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100">
                    <option value="menu" @selected(old('type', 'menu') === 'menu')>Berdasarkan Menu</option>
                    <option value="manual" @selected(old('type') === 'manual')>Penjualan Manual</option>
                </select>
            </div>
            <div>
                <label for="payment_method" class="mb-2 block text-xs font-bold text-[#374151]">Metode Pembayaran</label>
                <select id="payment_method" name="payment_method" class="h-12 w-full rounded-[10px] border border-[#dfe3e8] bg-white px-3 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100">
                    <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>Tunai</option>
                    <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer</option>
                    <option value="qris" @selected(old('payment_method') === 'qris')>QRIS</option>
                </select>
            </div>
        </div>

        <div id="menu-section">
            <h2 class="mb-3 text-sm font-bold text-[#142d70]">Item Menu</h2>
            <div id="items">
                @php $items = old('items', [['menu_id' => '', 'menu_name' => '', 'unit_price' => '', 'quantity' => 1]]); @endphp
                @foreach ($items as $i => $item)
                    <div class="item-row mb-3 grid gap-3 rounded-xl border border-[#ecece8] p-3 sm:grid-cols-[2fr_1fr_1fr_auto]">
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-500">Nama Menu</label>
                            <input type="text" name="items[{{ $i }}][menu_name]" value="{{ $item['menu_name'] ?? '' }}" placeholder="Nama menu" required
                                class="h-10 w-full rounded-lg border border-[#dfe3e8] px-3 text-sm outline-none focus:border-[#2563eb]">
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-500">ID Menu</label>
                            <input type="number" min="1" name="items[{{ $i }}][menu_id]" value="{{ $item['menu_id'] ?? '' }}" placeholder="ID" required
                                class="h-10 w-full rounded-lg border border-[#dfe3e8] px-3 text-sm outline-none focus:border-[#2563eb]">
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-500">Harga</label>
                            <input type="number" min="0" step="0.01" name="items[{{ $i }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" placeholder="0" required
                                class="h-10 w-full rounded-lg border border-[#dfe3e8] px-3 text-sm outline-none focus:border-[#2563eb]">
                        </div>
                        <div>
                            <label class="mb-1 block text-[11px] font-semibold text-slate-500">Qty</label>
                            <input type="number" min="0.001" step="0.001" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" required
                                class="h-10 w-full rounded-lg border border-[#dfe3e8] px-3 text-sm outline-none focus:border-[#2563eb]">
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-item" class="mt-1 text-xs font-bold text-[#2563eb] hover:text-[#1d4ed8]">+ Tambah item</button>
        </div>

        <div id="manual-section" class="hidden">
            <label for="total_nominal" class="mb-2 block text-xs font-bold text-[#374151]">Nominal Pendapatan</label>
            <input type="number" min="0" step="0.01" id="total_nominal" name="total_nominal" value="{{ old('total_nominal') }}" placeholder="0"
                class="h-12 w-full rounded-[10px] border border-[#dfe3e8] px-3 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100">
            <p class="mt-1.5 text-[11px] text-slate-500">Penjualan manual tidak memiliki item dan tidak mengurangi stok.</p>
        </div>

        <div class="mt-6">
            <label for="note" class="mb-2 block text-xs font-bold text-[#374151]">Catatan (opsional)</label>
            <textarea id="note" name="note" rows="3" placeholder="Tambahkan catatan..."
                class="w-full rounded-[10px] border border-[#dfe3e8] px-3 py-2.5 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100">{{ old('note') }}</textarea>
        </div>

        @if ($errors->any())
            <div class="mt-5 rounded-xl border border-[#fee2e2] bg-[#fef2f2] px-4 py-3 text-xs text-[#b91c1c]">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="flex h-11 items-center gap-2 rounded-lg bg-[#2563eb] px-5 text-sm font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-[#1d4ed8]">
                <x-icon name="check" :size="16" :stroke="2.4" />
                Simpan Penjualan
            </button>
            <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Batal</a>
        </div>
    </form>

    <script>
        const type = document.getElementById('type');
        const menuSection = document.getElementById('menu-section');
        const manualSection = document.getElementById('manual-section');

        function toggleSections() {
            const manual = type.value === 'manual';
            menuSection.classList.toggle('hidden', manual);
            manualSection.classList.toggle('hidden', !manual);
        }
        type.addEventListener('change', toggleSections);
        toggleSections();

        let index = {{ count($items) }};
        document.getElementById('add-item').addEventListener('click', function () {
            const wrapper = document.createElement('div');
            wrapper.className = 'item-row mb-3 grid gap-3 rounded-xl border border-[#ecece8] p-3 sm:grid-cols-[2fr_1fr_1fr_auto]';
            wrapper.innerHTML = `
                <div><label class="mb-1 block text-[11px] font-semibold text-slate-500">Nama Menu</label>
                    <input type="text" name="items[${index}][menu_name]" placeholder="Nama menu" required class="h-10 w-full rounded-lg border border-[#dfe3e8] px-3 text-sm outline-none focus:border-[#2563eb]"></div>
                <div><label class="mb-1 block text-[11px] font-semibold text-slate-500">ID Menu</label>
                    <input type="number" min="1" name="items[${index}][menu_id]" placeholder="ID" required class="h-10 w-full rounded-lg border border-[#dfe3e8] px-3 text-sm outline-none focus:border-[#2563eb]"></div>
                <div><label class="mb-1 block text-[11px] font-semibold text-slate-500">Harga</label>
                    <input type="number" min="0" step="0.01" name="items[${index}][unit_price]" placeholder="0" required class="h-10 w-full rounded-lg border border-[#dfe3e8] px-3 text-sm outline-none focus:border-[#2563eb]"></div>
                <div><label class="mb-1 block text-[11px] font-semibold text-slate-500">Qty</label>
                    <input type="number" min="0.001" step="0.001" name="items[${index}][quantity]" value="1" required class="h-10 w-full rounded-lg border border-[#dfe3e8] px-3 text-sm outline-none focus:border-[#2563eb]"></div>`;
            document.getElementById('items').appendChild(wrapper);
            index++;
        });
    </script>
@endsection
