@extends('layouts.app')

@section('title', 'Pencatatan Penjualan')

@section('content')
    @php
        $rupiah = fn ($v) => 'Rp'.number_format((float) $v, 0, ',', '.');
        $completed = $sales->getCollection()->where('status', 'completed')->sum('total');
    @endphp

    <div class="mb-6">
        <h1 class="text-[22px] font-extrabold tracking-tight text-[#172033]">Pencatatan Penjualan</h1>
        <p class="mt-1 text-sm text-slate-500">Kelola pesanan dan transaksi pelanggan.</p>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Transaksi" :value="number_format($sales->total(), 0, ',', '.')" icon="receipt" tone="blue" />
        <x-stat-card label="Pendapatan Selesai" :value="$rupiah($completed)" icon="wallet" tone="green" />
        <x-stat-card label="Menunggu" :value="number_format($sales->getCollection()->where('status', 'pending')->count(), 0, ',', '.')" icon="cart" tone="orange" />
        <x-stat-card label="Diproses" :value="number_format($sales->getCollection()->where('status', 'processing')->count(), 0, ',', '.')" icon="trending" tone="blue" />
    </div>

    <div class="rounded-2xl border border-[#ecece8] bg-white p-5 shadow-sm">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-[#142d70]">Daftar Pesanan</h2>
                <p class="mt-0.5 text-[11px] text-slate-500">Menampilkan {{ $sales->count() }} dari {{ $sales->total() }} transaksi</p>
            </div>
            @can('create-sales')
                <a href="{{ route('sales.create') }}"
                    class="flex items-center gap-2 rounded-lg bg-[#2563eb] px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-[#1d4ed8]">
                    <x-icon name="plus" :size="15" :stroke="2.4" />
                    Input Pesanan
                </a>
            @endcan
        </div>

        <form method="GET" class="mb-4 flex flex-wrap gap-3">
            <div class="relative min-w-[220px] flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="search" :size="16" />
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari data..."
                    class="h-10 w-full rounded-lg border border-[#dfe3e8] pl-9 pr-3 text-sm outline-none focus:border-[#2563eb] focus:ring-4 focus:ring-blue-100">
            </div>
            <select name="status" class="h-10 rounded-lg border border-[#dfe3e8] bg-white px-3 text-sm outline-none focus:border-[#2563eb]">
                <option value="">Semua status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="h-10 rounded-lg border border-[#dfe3e8] bg-white px-4 text-xs font-bold text-[#374151]">Filter</button>
        </form>

        @if ($sales->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#ecece8] bg-[#f8fafc] text-[10px] font-bold uppercase tracking-wide text-slate-500">
                            <th class="px-3 py-3">ID Transaksi</th>
                            <th class="px-3 py-3">Tanggal</th>
                            <th class="px-3 py-3">Pesanan</th>
                            <th class="px-3 py-3">Total</th>
                            <th class="px-3 py-3">Metode</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            @php
                                $tone = match ($sale->status) {
                                    \App\Enums\SaleStatus::Completed => 'bg-[#dcfce7] text-[#15803d]',
                                    \App\Enums\SaleStatus::Cancelled => 'bg-[#fee2e2] text-[#b91c1c]',
                                    default => 'bg-[#fef3c7] text-[#b45309]',
                                };
                            @endphp
                            <tr class="border-b border-[#f0f1f2] last:border-0">
                                <td class="px-3 py-3 font-bold text-[#142d70]">{{ $sale->transaction_code }}</td>
                                <td class="px-3 py-3 text-slate-500">{{ $sale->created_at->translatedFormat('d M Y, H.i') }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $sale->type === 'manual' ? 'Penjualan manual' : $sale->items->count().' item' }}</td>
                                <td class="px-3 py-3 font-semibold text-[#374151]">{{ $rupiah($sale->total) }}</td>
                                <td class="px-3 py-3 text-slate-500">{{ $sale->payment_method->label() }}</td>
                                <td class="px-3 py-3"><span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $tone }}">{{ $sale->status->label() }}</span></td>
                                <td class="px-3 py-3">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-[#2563eb] hover:text-[#1d4ed8]" aria-label="Lihat detail">
                                        <x-icon name="eye" :size="16" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $sales->links() }}</div>
        @else
            <x-empty-state icon="receipt" title="Belum ada transaksi" description="Penjualan yang dicatat akan tampil di sini." />
        @endif
    </div>
@endsection
