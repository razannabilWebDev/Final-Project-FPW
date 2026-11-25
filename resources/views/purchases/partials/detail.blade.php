@php
    $rupiah = static fn ($value): string => 'Rp'.number_format((float) $value, 0, ',', '.');
    $quantity = static function ($value): string {
        $formatted = number_format((float) $value, 3, ',', '.');

        return rtrim(rtrim($formatted, '0'), ',');
    };
    $tone = match ($purchase->status) {
        \App\Enums\PurchaseStatus::Approved => 'bg-success-soft text-[#15803d]',
        \App\Enums\PurchaseStatus::Rejected => 'bg-danger-soft text-[#b91c1c]',
        default => 'bg-warning-soft text-[#b45309]',
    };
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Kode Pembelian</p>
        <p class="mt-1 text-sm font-bold text-[#142d70]">{{ $purchase->purchase_code }}</p>
    </div>
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Status</p>
        <p class="mt-1">
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $tone }}">
                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $purchase->status->label() }}
            </span>
        </p>
    </div>
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Tanggal</p>
        <p class="mt-1 text-sm font-semibold text-[#374151]">{{ $purchase->purchase_date?->translatedFormat('d M Y') ?? '—' }}</p>
    </div>
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Supplier</p>
        <p class="mt-1 text-sm font-semibold text-[#374151]">{{ $purchase->supplier_name ?? '—' }}</p>
    </div>
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Dicatat oleh</p>
        <p class="mt-1 text-sm font-semibold text-[#374151]">{{ $purchase->creator?->name ?? '—' }}</p>
    </div>
    <div>
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Diverifikasi oleh</p>
        <p class="mt-1 text-sm font-semibold text-[#374151]">
            {{ $purchase->verifier?->name ?? '—' }}
            @if ($purchase->verified_at)
                <span class="block text-[11px] font-normal text-slate-500">{{ $purchase->verified_at->translatedFormat('d M Y, H.i') }}</span>
            @endif
        </p>
    </div>
</div>

<div class="mt-5">
    <p class="mb-2 text-xs font-bold text-[#374151]">Item Pembelian</p>
    <div class="overflow-x-auto rounded-xl border border-line">
        <table class="w-full min-w-[520px] text-left text-[11px]">
            <thead>
                <tr class="border-b border-line bg-head-bg font-bold uppercase tracking-wide text-slate-500">
                    <th class="px-3 py-2">Bahan</th>
                    <th class="px-3 py-2">Jumlah</th>
                    <th class="px-3 py-2">Harga Satuan</th>
                    <th class="px-3 py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->items as $item)
                    <tr class="border-b border-line last:border-0">
                        <td class="px-3 py-2 font-semibold text-[#374151]">{{ $item->ingredient?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $quantity($item->quantity) }} {{ $item->unit }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $rupiah($item->unit_cost) }}</td>
                        <td class="px-3 py-2 text-right font-semibold text-[#374151]">{{ $rupiah($item->subtotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-head-bg">
                    <td colspan="3" class="px-3 py-2.5 text-right text-xs font-bold text-[#142d70]">Total</td>
                    <td class="px-3 py-2.5 text-right text-xs font-extrabold text-brand">{{ $rupiah($purchase->total) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if ($purchase->rejection_note)
    <div class="mt-4 rounded-xl border border-[#fde68a] bg-[#fffbeb] px-4 py-3">
        <p class="text-xs font-bold text-[#b45309]">Alasan penolakan</p>
        <p class="mt-1 text-xs text-[#78350f]">{{ $purchase->rejection_note }}</p>
    </div>
@endif

@if ($purchase->receipt_path)
    <div class="mt-4">
        <a href="{{ route('purchases.receipt', $purchase) }}" target="_blank" rel="noopener"
            class="inline-flex items-center gap-2 text-xs font-bold text-brand hover:text-brand-hover">
            <x-icon name="eye" :size="15" />
            Lihat bukti nota
        </a>
    </div>
@endif

@can('verify-purchases')
    @if ($purchase->status === \App\Enums\PurchaseStatus::Pending)
        <div class="mt-5 border-t border-line pt-5">
            <p class="mb-3 text-xs font-bold text-[#374151]">Verifikasi Pembelian</p>
            <form id="approve-purchase-{{ $purchase->id }}" method="POST" action="{{ route('purchases.verify', $purchase) }}">
                @csrf
                <input type="hidden" name="decision" value="approved">
            </form>
            <form id="reject-purchase-{{ $purchase->id }}" method="POST" action="{{ route('purchases.verify', $purchase) }}">
                @csrf
                <input type="hidden" name="decision" value="rejected">
                <label for="rejection_note_{{ $purchase->id }}" class="mb-1.5 block text-[11px] font-bold text-[#374151]">Alasan penolakan</label>
                <textarea id="rejection_note_{{ $purchase->id }}" name="rejection_note" rows="2" maxlength="500" required placeholder="Contoh: jumlah tidak sesuai nota"
                    class="w-full rounded-[10px] border border-line-input px-3.5 py-2.5 text-xs outline-none focus:border-brand focus:ring-[3px] focus:ring-[#dbeafe]"></textarea>
            </form>
            <div class="mt-3 flex flex-wrap gap-3">
                <button type="submit" form="approve-purchase-{{ $purchase->id }}" class="flex items-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-blue-200 hover:bg-brand-hover">
                    <x-icon name="check" :size="14" :stroke="2.4" />
                    Setujui
                </button>
                <button type="submit" form="reject-purchase-{{ $purchase->id }}" class="rounded-lg bg-danger px-4 py-2.5 text-xs font-bold text-white transition hover:bg-[#b91c1c]">Tolak</button>
            </div>
        </div>
    @endif
@endcan
