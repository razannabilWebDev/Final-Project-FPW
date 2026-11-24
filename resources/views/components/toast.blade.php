@php
    $notifications = [];

    if (session('success')) {
        $notifications[] = [
            'tone' => 'success',
            'title' => 'Berhasil',
            'message' => session('success'),
            'icon' => 'check',
        ];
    }

    if (session('error')) {
        $notifications[] = [
            'tone' => 'danger',
            'title' => 'Gagal',
            'message' => session('error'),
            'icon' => 'x',
        ];
    }
@endphp

@if ($notifications !== [])
    <div class="pointer-events-none fixed bottom-5 right-5 z-[60] flex w-[calc(100%-2.5rem)] max-w-sm flex-col gap-3"
         aria-live="polite">
        @foreach ($notifications as $notification)
            <div data-toast
                 class="toast-item pointer-events-auto flex items-start gap-3 rounded-xl border border-line bg-white px-4 py-3.5 shadow-toast">
                <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $notification['tone'] === 'success' ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }}">
                    <x-icon :name="$notification['icon']" :size="15" :stroke="2.4" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-[#1f2937]">{{ $notification['title'] }}</p>
                    <p class="mt-0.5 text-[11px] leading-relaxed text-slate-500">{{ $notification['message'] }}</p>
                </div>
                <button type="button" data-toast-close aria-label="Tutup notifikasi"
                        class="shrink-0 rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                    <x-icon name="x" :size="14" />
                </button>
            </div>
        @endforeach
    </div>
@endif
