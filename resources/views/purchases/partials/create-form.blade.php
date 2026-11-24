<form id="purchase-form" method="POST" action="{{ route('purchases.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="modal" value="create-purchase-modal">

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="purchase_date" class="mb-1.5 block text-xs font-bold text-[#374151]">Tanggal Pembelian</label>
            <input id="purchase_date" name="purchase_date" type="date" value="{{ old('purchase_date', now()->timezone('Asia/Jakarta')->toDateString()) }}" required
                class="h-12 w-full rounded-[10px] border border-line-input bg-white px-3.5 text-sm outline-none focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe] @error('purchase_date') border-danger @enderror">
            @error('purchase_date')
                <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="supplier_name" class="mb-1.5 block text-xs font-bold text-[#374151]">Nama Supplier (opsional)</label>
            <input id="supplier_name" name="supplier_name" type="text" list="supplier-list" value="{{ old('supplier_name') }}" placeholder="contoh: Toko Berkah" maxlength="255"
                class="h-12 w-full rounded-[10px] border border-line-input bg-white px-3.5 text-sm outline-none focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe]">
            <datalist id="supplier-list">
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier }}"></option>
                @endforeach
            </datalist>
        </div>
    </div>

    <div class="mt-5">
        <div class="mb-2 flex items-center justify-between">
            <p class="text-xs font-bold text-[#374151]">Item Pembelian</p>
            <button type="button" id="add-purchase-item" class="flex items-center gap-1.5 text-[11px] font-bold text-brand hover:text-brand-hover">
                <x-icon name="plus" :size="14" :stroke="2.4" />
                Tambah Item
            </button>
        </div>
        <div id="purchase-items" class="grid gap-3">
            @include('purchases.partials.item-row', ['index' => 0, 'ingredients' => $ingredients])
        </div>
        <template id="purchase-item-template">
            @include('purchases.partials.item-row', ['index' => '__INDEX__', 'ingredients' => $ingredients])
        </template>
        @error('items')
            <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
        @enderror
        @error('items.*.ingredient_id')
            <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
        @enderror
    </div>

    <div class="mt-5">
        <label for="receipt" class="mb-1.5 block text-xs font-bold text-[#374151]">Bukti Nota (opsional)</label>
        <input id="receipt" name="receipt" type="file" accept="image/jpeg,image/png"
            class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border file:border-line-input file:bg-white file:px-3 file:py-2 file:text-xs file:font-bold file:text-[#374151]">
        <p class="mt-1.5 text-[11px] text-slate-500">PNG atau JPG, maksimal 5 MB. Bukti nota bukan pengganti item terstruktur.</p>
        @error('receipt')
            <p class="mt-1.5 text-[11px] font-semibold text-danger">{{ $message }}</p>
        @enderror
    </div>
</form>
