@php $rupiah = fn ($v) => 'Rp'.number_format((float) $v, 0, ',', '.'); @endphp

<div class="space-y-5">
    <div class="rounded-xl border border-line p-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <p class="text-sm font-extrabold text-[#142d70]">{{ $sale->transaction_code }}</p>
                <p class="mt-0.5 text-[11px] text-slate-500">{{ $sale->created_at->translatedFormat('d F Y, H.i') }} · {{ $sale->payment_method->label() }}</p>
            </div>
            @php
                $tone = match ($sale->status) {
                    \App\Enums\SaleStatus::Completed => 'bg-[#dcfce7] text-[#15803d]',
                    \App\Enums\SaleStatus::Cancelled => 'bg-[#fee2e2] text-[#b91c1c]',
                    default => 'bg-[#fef3c7] text-[#b45309]',
                };
            @endphp
            <span class="rounded-full px-3 py-1 text-[10px] font-bold {{ $tone }}">{{ $sale->status->label() }}</span>
        </div>
    </div>

    <div>
        <h4 class="mb-2 text-xs font-bold text-[#142d70]">Detail Item</h4>
        @if ($sale->type === 'manual')
            <p class="text-xs text-slate-600">Penjualan manual — tidak memiliki item menu.</p>
            <p class="mt-2 text-lg font-extrabold text-[#142d70]">{{ $rupiah($sale->total) }}</p>
        @else
            <div class="overflow-x-auto rounded-xl border border-line">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-line bg-head-bg text-[10px] font-bold uppercase text-slate-500">
                            <th class="px-3 py-2.5">Menu</th>
                            <th class="px-3 py-2.5">Qty</th>
                            <th class="px-3 py-2.5">Harga</th>
                            <th class="px-3 py-2.5">HPP/Porsi</th>
                            <th class="px-3 py-2.5 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr class="border-b border-line last:border-0">
                                <td class="px-3 py-2.5 font-semibold text-[#374151]">{{ $item->menu_name_snapshot }}</td>
                                <td class="px-3 py-2.5 text-slate-500">{{ number_format((float) $item->quantity, 3, ',', '.') }}</td>
                                <td class="px-3 py-2.5 text-slate-500">{{ $rupiah($item->unit_price_snapshot) }}</td>
                                <td class="px-3 py-2.5 text-slate-500">{{ $rupiah($item->hpp_snapshot) }}</td>
                                <td class="px-3 py-2.5 text-right font-bold text-[#142d70]">{{ $rupiah($item->subtotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 flex justify-end">
                <span class="text-sm font-extrabold text-[#142d70]">Total: {{ $rupiah($sale->total) }}</span>
            </div>
        @endif
    </div>

    @if ($sale->note)
        <p class="text-xs text-slate-500">Catatan: {{ $sale->note }}</p>
    @endif

    <div class="grid gap-4 sm:grid-cols-2">
        @can('update-sale-status')
            <form method="POST" action="{{ route('sales.update-status', $sale) }}" class="rounded-xl border border-line p-4">
                @csrf
                <h4 class="mb-3 text-xs font-bold text-[#142d70]">Ubah Status</h4>
                <select name="status" class="h-10 w-full rounded-lg border border-line-input bg-white px-3 text-sm outline-none focus:border-brand">
                    @foreach (\App\Enums\SaleStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($sale->status === $status)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <input type="text" name="note" placeholder="Catatan perubahan (opsional)"
                    class="mt-2 h-10 w-full rounded-lg border border-line-input px-3 text-sm outline-none focus:border-brand">
                <button type="submit" class="mt-3 h-9 w-full rounded-lg bg-brand text-xs font-bold text-white hover:bg-brand-hover">Perbarui Status</button>
            </form>
        @endcan

        <div class="rounded-xl border border-line p-4">
            <h4 class="mb-3 text-xs font-bold text-[#142d70]">Riwayat Status</h4>
            @forelse ($sale->statusHistories()->latest('created_at')->get() as $history)
                <div class="border-b border-line py-2 last:border-0">
                    <p class="text-xs font-semibold text-[#374151]">{{ $history->to_status }}</p>
                    <p class="text-[10px] text-slate-500">{{ $history->created_at?->translatedFormat('d M Y, H.i') }}</p>
                </div>
            @empty
                <p class="text-xs text-slate-500">Belum ada riwayat.</p>
            @endforelse
        </div>
    </div>
</div>
