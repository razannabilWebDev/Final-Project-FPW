@php
    $notifications = $notifications ?? collect();
    $total = $notifications->sum('count');
    $iconForTone = static fn (string $tone): string => match ($tone) {
        'danger' => 'receipt',
        'warning' => 'box',
        default => 'cart',
    };
    $colorForTone = static fn (string $tone): string => match ($tone) {
        'danger' => 'bg-[#fee2e2] text-[#b91c1c]',
        'warning' => 'bg-[#fef3c7] text-[#b45309]',
        default => 'bg-[#dbeafe] text-[#1d4ed8]',
    };
@endphp

<div id="notification-root" class="relative">
    <button type="button" id="notification-toggle" aria-label="Notifikasi" aria-expanded="false"
            class="relative flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-50 hover:text-navy">
        <x-icon name="bell" :size="18" />
        @if ($total > 0)
            <span class="absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-danger px-1 text-[10px] font-bold text-white ring-2 ring-white">
                {{ $total > 9 ? '9+' : $total }}
            </span>
        @endif
    </button>

    <div id="notification-panel"
         class="absolute right-0 top-12 z-50 hidden w-[320px] rounded-xl border border-line bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-line px-4 py-3">
            <p class="text-xs font-bold text-[#142d70]">Notifikasi</p>
            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">{{ $total }} item</span>
        </div>

        <div class="max-h-[340px] overflow-y-auto p-2">
            @forelse ($notifications as $notification)
                <a href="{{ $notification['route'] }}"
                   class="flex items-start gap-3 rounded-lg px-2.5 py-2.5 transition hover:bg-slate-50">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $colorForTone($notification['tone']) }}">
                        <x-icon :name="$iconForTone($notification['tone'])" :size="15" />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-[12px] font-semibold leading-snug text-[#374151]">{{ $notification['label'] }}</span>
                        <span class="mt-0.5 block text-[10px] text-slate-400">Klik untuk melihat detail</span>
                    </span>
                    <x-icon name="arrow-right" :size="13" class="mt-1 shrink-0 text-slate-300" />
                </a>
            @empty
                <div class="px-4 py-8 text-center">
                    <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-[#dcfce7] text-[#15803d]">
                        <x-icon name="check" :size="18" />
                    </span>
                    <p class="mt-3 text-xs font-semibold text-[#374151]">Semua sudah tertangani</p>
                    <p class="mt-1 text-[11px] text-slate-500">Tidak ada yang perlu ditindaklanjuti saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    (function () {
        const root = document.getElementById('notification-root');
        const toggle = document.getElementById('notification-toggle');
        const panel = document.getElementById('notification-panel');
        if (!root || !toggle || !panel) return;

        const setOpen = (open) => {
            panel.classList.toggle('hidden', !open);
            toggle.setAttribute('aria-expanded', String(open));
        };

        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            setOpen(panel.classList.contains('hidden'));
        });

        document.addEventListener('click', function (event) {
            if (!root.contains(event.target)) setOpen(false);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') setOpen(false);
        });
    })();
</script>
