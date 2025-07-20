{{-- resources/views/components/breadcrumbs.blade.php --}}
@php
    // Get full path segments (preserve all segments including 'dashboard' for building URLs)
    $fullPathSegments = collect(explode('/', trim(request()->path(), '/')))
        ->filter(fn($segment) => !empty($segment) && !is_numeric($segment))
        ->values();

    // Build visible breadcrumbs by hiding 'dashboard' (but keeping it in URLs)
    $visibleBreadcrumbs = collect(['home'])->concat(
        $fullPathSegments->reject(fn($segment) => $segment === 'dashboard')->values(),
    );
@endphp

<nav aria-label="Breadcrumb" class="flex items-center text-sm text-gray-500">
    @foreach ($visibleBreadcrumbs as $index => $segment)
        @if ($index === 0)
            {{-- Home link with icon --}}
            <a href="{{ url('/dashboard') }}"
                class="flex items-center gap-1 text-gray-500 hover:text-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                Home
            </a>
        @elseif ($index < $visibleBreadcrumbs->count() - 1)
            @php
                // Get real index in full path segments
                $segmentIndexInFull = $fullPathSegments->search($segment);
                $url = url($fullPathSegments->slice(0, $segmentIndexInFull + 1)->implode('/'));
                $isDisabled = in_array($segment, []);
            @endphp

            @if ($isDisabled)
                <span class="capitalize text-gray-400 cursor-not-allowed">{{ $segment }}</span>
            @else
                <a href="{{ $url }}" class="capitalize text-gray-500 hover:text-gray-700 transition-colors">
                    {{ $segment }}
                </a>
            @endif
        @else
            {{-- Current page (last item) --}}
            <span class="capitalize text-gray-700 font-medium">
                {{ $segment }}
            </span>
        @endif

        {{-- Separator --}}
        @if ($index < $visibleBreadcrumbs->count() - 1)
            <svg class="w-4 h-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"></path>
            </svg>
        @endif
    @endforeach
</nav>
