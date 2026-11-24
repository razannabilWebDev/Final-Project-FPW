@props([
    'tone' => 'neutral',
])

@php
    $tones = [
        'success' => 'bg-[#dcfce7] text-[#15803d]',
        'warning' => 'bg-[#fef3c7] text-[#b45309]',
        'danger' => 'bg-[#fee2e2] text-[#b91c1c]',
        'info' => 'bg-[#dbeafe] text-[#1d4ed8]',
        'purple' => 'bg-[#ede9fe] text-[#7c3aed]',
        'neutral' => 'bg-[#f1f5f9] text-[#64748b]',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold ' . ($tones[$tone] ?? $tones['neutral'])]) }}>
    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
    {{ $slot }}
</span>
