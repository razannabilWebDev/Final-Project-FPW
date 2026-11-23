@php
    $tone = $tone ?? 'blue';
    $tones = [
        'blue' => 'bg-[#dbeafe] text-[#2563eb]',
        'green' => 'bg-[#dcfce7] text-[#16a34a]',
        'orange' => 'bg-[#fef3c7] text-[#d97706]',
        'red' => 'bg-[#fee2e2] text-[#dc2626]',
    ];
@endphp
<div class="rounded-2xl border border-[#ecece8] bg-white p-5 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[11px] font-semibold text-slate-500">{{ $label }}</p>
            <p class="mt-2 truncate text-xl font-extrabold text-[#142d70]">{{ $value }}</p>
            @if (isset($hint))
                <p class="mt-1.5 text-[11px] text-slate-400">{{ $hint }}</p>
            @endif
        </div>
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tones[$tone] ?? $tones['blue'] }}">
            <x-icon :name="$icon ?? 'chart'" :size="19" />
        </span>
    </div>
</div>
