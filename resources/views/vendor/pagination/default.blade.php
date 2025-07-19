@if ($paginator->hasPages())
    <div class="mt-4 flex items-center justify-center space-x-2 text-sm font-medium" id="pagination-container">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="flex items-center space-x-1 text-gray-400 cursor-not-allowed">
                <span>Previous</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="flex items-center space-x-1 text-black hover:bg-gray-200 px-3 py-1 rounded">
                <span class="text-lg">&lsaquo;</span>
                <span>Previous</span>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-1 text-gray-500">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 rounded bg-gray-300 text-black font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                            class="px-3 py-1 rounded hover:bg-gray-200 text-black">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="flex items-center space-x-1 text-black hover:bg-gray-200 px-3 py-1 rounded">
                <span>Next</span>
                <span class="text-lg">&rsaquo;</span>
            </a>
        @else
            <span class="flex items-center space-x-1 text-gray-400 cursor-not-allowed">
                <span>Next</span>
            </span>
        @endif
    </div>
@endif
