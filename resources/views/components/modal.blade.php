@props([
    'id',
    'title',
    'subtitle' => 'Lengkapi informasi dengan benar.',
    'icon' => 'receipt',
    'size' => 'lg',
])

@php
    $width = match ($size) {
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        default => 'max-w-3xl',
    };
@endphp

<div id="{{ $id }}"
     class="modal-root fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6"
     role="dialog"
     aria-modal="true"
     aria-labelledby="{{ $id }}-title"
     data-modal-id="{{ $id }}"
     data-busy="false">

    <div class="modal-overlay fixed inset-0 bg-[rgba(15,23,42,.56)] backdrop-blur-[3px]"></div>

    <div class="modal-panel relative flex max-h-[90vh] w-full {{ $width }} flex-col overflow-hidden rounded-2xl bg-white shadow-modal"
         role="document">

        {{-- Header --}}
        <div class="flex items-start gap-3 border-b border-line px-5 py-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#dbeafe] text-brand">
                <x-icon :name="$icon" :size="18" />
            </span>
            <div class="min-w-0 flex-1">
                <h3 id="{{ $id }}-title" class="text-sm font-bold text-[#142d70]">{{ $title }}</h3>
                <p class="mt-0.5 text-[11px] text-slate-500">{{ $subtitle }}</p>
            </div>
            <button type="button" class="modal-close flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                    aria-label="Tutup modal">
                <x-icon name="x" :size="17" />
            </button>
        </div>

        {{-- Body --}}
        <div class="modal-body flex-1 overflow-y-auto px-5 py-5">
            {{ $slot }}
        </div>

        @if (isset($footer))
            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 border-t border-line bg-head-bg px-5 py-4">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

<script>
    (function () {
        const root = document.getElementById('{{ $id }}');
        if (!root) return;

        const open = () => {
            root.classList.remove('hidden');
            root.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            const focusable = root.querySelector('input, select, textarea, button');
            if (focusable) focusable.focus();
        };
        const close = () => {
            if (root.dataset.busy === 'true') return;
            root.classList.add('hidden');
            root.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        };

        root.openModal = open;
        root.closeModal = close;

        root.querySelectorAll('.modal-close').forEach(function (button) {
            button.addEventListener('click', close);
        });
        root.querySelector('.modal-overlay').addEventListener('click', close);

        // Escape ditangani global di index.blade.php untuk semua modal.
        @if(request()->query('modal') === $id)
            document.addEventListener('DOMContentLoaded', open);
        @endif
    })();
</script>
