@extends('layouts.sales')

@section('title', 'Detail Transaksi')

@section('content')
    @php $rupiah = fn ($v) => 'Rp'.number_format((float) $v, 0, ',', '.'); @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">{{ $sale->transaction_code }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $sale->created_at->translatedFormat('d F Y, H.i') }} · {{ $sale->payment_method->label() }}</p>
        </div>
        <a href="{{ route('sales.index') }}" class="text-sm font-semibold text-[#2563eb] hover:text-[#1d4ed8]">&larr; Kembali</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-[#ecece8] bg-white p-5 shadow-sm lg:col-span-2">
            <h2 class="mb-4 text-sm font-bold text-[#142d70]">Detail Item</h2>
            @if ($sale->type === 'manual')
                <p class="text-sm text-slate-600">Penjualan manual — tidak memiliki item menu.</p>
                <p class="mt-3 text-xl font-extrabold text-[#142d70]">{{ $rupiah($sale->total) }}</p>
            @else
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#ecece8] text-[10px] font-bold uppercase text-slate-500">
                            <th class="py-2">Menu</th>
                            <th class="py-2">Qty</th>
                            <th class="py-2">Harga</th>
                            <th class="py-2">HPP/Porsi</th>
                            <th class="py-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $item)
                            <tr class="border-b border-[#f0f1f2]">
                                <td class="py-2.5 font-semibold text-[#374151]">{{ $item->menu_name_snapshot }}</td>
                                <td class="py-2.5 text-slate-500">{{ number_format((float) $item->quantity, 3, ',', '.') }}</td>
                                <td class="py-2.5 text-slate-500">{{ $rupiah($item->unit_price_snapshot) }}</td>
                                <td class="py-2.5 text-slate-500">{{ $rupiah($item->hpp_snapshot) }}</td>
                                <td class="py-2.5 text-right font-bold text-[#142d70]">{{ $rupiah($item->subtotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 flex justify-end border-t border-[#ecece8] pt-4">
                    <span class="text-sm font-extrabold text-[#142d70]">Total: {{ $rupiah($sale->total) }}</span>
                </div>
            @endif
            @if ($sale->note)
                <p class="mt-4 text-xs text-slate-500">Catatan: {{ $sale->note }}</p>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-[#ecece8] bg-white p-5 shadow-sm">
                <h2 class="mb-3 text-sm font-bold text-[#142d70]">Status Transaksi</h2>
                <p class="mb-4 text-lg font-extrabold text-[#142d70]">{{ $sale->status->label() }}</p>
                @can('update-sale-status')
                    <form method="POST" action="{{ route('sales.update-status', $sale) }}">
                        @csrf
                        <label for="status" class="mb-2 block text-xs font-bold text-[#374151]">Ubah Status</label>
                        <select id="status" name="status" class="h-11 w-full rounded-lg border border-[#dfe3e8] bg-white px-3 text-sm outline-none focus:border-[#2563eb]">
                            @foreach (\App\Enums\SaleStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($sale->status === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="note" placeholder="Catatan perubahan (opsional)"
                            class="mt-3 h-10 w-full rounded-lg border border-[#dfe3e8] px-3 text-sm outline-none focus:border-[#2563eb]">
                        <button type="submit" class="mt-4 h-10 w-full rounded-lg bg-[#2563eb] text-xs font-bold text-white hover:bg-[#1d4ed8]">Perbarui Status</button>
                    </form>
                @endcan
            </div>

            <div class="rounded-2xl border border-[#ecece8] bg-white p-5 shadow-sm">
                <h2 class="mb-3 text-sm font-bold text-[#142d70]">Riwayat Status</h2>
                @forelse ($sale->statusHistories()->latest('created_at')->get() as $history)
                    <div class="border-b border-[#f0f1f2] py-2 last:border-0">
                        <p class="text-xs font-semibold text-[#374151]">{{ $history->to_status }}</p>
                        <p class="text-[10px] text-slate-500">{{ $history->created_at?->translatedFormat('d M Y, H.i') }}</p>
                    </div>
                @empty
                    <p class="text-xs text-slate-500">Belum ada riwayat.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
