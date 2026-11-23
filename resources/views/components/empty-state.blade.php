<div class="flex flex-col items-center justify-center px-4 py-10 text-center">
    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-[#f1f5f9] text-slate-400">
        <x-icon :name="$icon ?? 'box'" :size="22" />
    </span>
    <p class="mt-3 text-sm font-bold text-[#374151]">{{ $title ?? 'Belum ada data' }}</p>
    <p class="mt-1 max-w-sm text-xs leading-relaxed text-slate-500">{{ $description ?? 'Data akan tampil di sini setelah tersedia.' }}</p>
</div>
