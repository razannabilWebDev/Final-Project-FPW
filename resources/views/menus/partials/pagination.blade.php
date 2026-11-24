@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        $pages = range(max(1, $current - 1), min($last, $current + 1));

        if ($current > 3) {
            array_unshift($pages, 1, '...');
        } elseif ($current === 3) {
            array_unshift($pages, 1);
        }

        if ($current < $last - 2) {
            array_push($pages, '...', $last);
        } elseif ($current === $last - 2) {
            $pages[] = $last;
        }
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#f0f1f2] px-5 py-4">
        <p class="text-[11px] text-slate-500">
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </p>

        <nav class="flex items-center gap-1.5" aria-label="Navigasi halaman">
            @if ($paginator->onFirstPage())
                <span class="flex h-[27px] min-w-[27px] cursor-not-allowed items-center justify-center rounded-md border border-line text-[11px] font-bold text-slate-300" aria-disabled="true">
                    <span aria-hidden="true">&lsaquo;</span><span class="sr-only">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="flex h-[27px] min-w-[27px] items-center justify-center rounded-md border border-line-input bg-white text-[11px] font-bold text-slate-600 transition hover:border-brand hover:text-brand"
                   aria-label="Halaman sebelumnya">
                    <span aria-hidden="true">&lsaquo;</span>
                </a>
            @endif

            @foreach ($pages as $page)
                @if ($page === '...')
                    <span class="flex h-[27px] items-center px-1 text-[11px] text-slate-400" aria-hidden="true">&hellip;</span>
                @elseif ($page === $current)
                    <span class="flex h-[27px] min-w-[27px] items-center justify-center rounded-md border border-brand bg-brand px-2 text-[11px] font-bold text-white"
                          aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}"
                       class="flex h-[27px] min-w-[27px] items-center justify-center rounded-md border border-line-input bg-white px-2 text-[11px] font-bold text-slate-600 transition hover:border-brand hover:text-brand"
                       aria-label="Halaman {{ $page }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="flex h-[27px] min-w-[27px] items-center justify-center rounded-md border border-line-input bg-white text-[11px] font-bold text-slate-600 transition hover:border-brand hover:text-brand"
                   aria-label="Halaman berikutnya">
                    <span aria-hidden="true">&rsaquo;</span>
                </a>
            @else
                <span class="flex h-[27px] min-w-[27px] cursor-not-allowed items-center justify-center rounded-md border border-line text-[11px] font-bold text-slate-300" aria-disabled="true">
                    <span aria-hidden="true">&rsaquo;</span><span class="sr-only">Berikutnya</span>
                </span>
            @endif
        </nav>
    </div>
@endif
