@extends('dashboard.layouts.app')

@section('content')
    <div class="p-6" x-data="productSearch()">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Top Controls -->
        <div class="flex flex-col md:flex-row justify-between items-stretch gap-4 mb-4">
            <!-- Search and Filters -->
            <div class="flex w-full md:max-w-md gap-2">
                <!-- Search -->
                <div class="relative flex-1">
                    <input type="text" x-model="searchTerm" @input="debounceSearch()" placeholder="Search products..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute right-3 top-2.5">
                        <!-- Spinner or Icon -->
                        <svg x-show="!loading" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <svg x-show="loading" class="animate-spin w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </div>

                <!-- Toggle Filters -->
                <button @click="showFilters = !showFilters"
                    class="bg-black hover:bg-gray-700 text-white px-3 py-2 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707l-5.414 5.414A1 1 0 0115 12v6a1 1 0 01-1.447.894l-4-2A1 1 0 019 16v-4a1 1 0 01.293-.707L14.707 6.707A1 1 0 0115 6V5a1 1 0 00-1-1H4a1 1 0 00-1 1z" />
                    </svg>
                </button>
            </div>

            <!-- Create Product -->
            <div>
                <a href="{{ route('dashboard.products.create') }}"
                    class="bg-black hover:bg-gray-700 font-medium text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <span>Create Product</span>
                    <span class="text-lg font-bold">+</span>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div x-show="showFilters" x-transition class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Category -->
            <div>
                <select x-model="categoryFilter" @change="performSearch()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Min Price -->
            <div class="relative">
                <input type="number" min="0" x-model="minPrice" @input="debouncePriceSearch()"
                    placeholder="Min Price"
                    class="w-full pr-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="absolute right-3 top-2.5" x-show="priceLoading">
                    <svg class="animate-spin w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Max Price -->
            <div class="relative">
                <input type="number" min="0" x-model="maxPrice" @input="debouncePriceSearch()"
                    placeholder="Max Price"
                    class="w-full pr-10 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <div class="absolute right-3 top-2.5" x-show="priceLoading">
                    <svg class="animate-spin w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Clear Filters -->
            <div>
                <button @click="clearFilters()" class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg">
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Results -->
        <div id="products-container">
            @include('dashboard.products.partials.table', ['products' => $products])
        </div>
    </div>

    <script>
        function productSearch() {
            return {
                searchTerm: '{{ $search ?? '' }}',
                categoryFilter: '{{ $category ?? '' }}',
                minPrice: '{{ $minPrice ?? '' }}',
                maxPrice: '{{ $maxPrice ?? '' }}',
                loading: false,
                priceLoading: false,
                showFilters: false,
                searchTimeout: null,
                priceTimeout: null,

                debounceSearch() {
                    clearTimeout(this.searchTimeout);
                    this.loading = true;
                    this.searchTimeout = setTimeout(() => {
                        this.performSearch(false);
                    }, 500);
                },

                debouncePriceSearch() {
                    clearTimeout(this.priceTimeout);
                    this.priceLoading = true;
                    this.priceTimeout = setTimeout(() => {
                        this.performSearch(true);
                    }, 500);
                },

                performSearch(fromPrice = false) {
                    const url = new URL(window.location.href);

                    if (this.searchTerm.trim()) {
                        url.searchParams.set('search', this.searchTerm);
                    } else {
                        url.searchParams.delete('search');
                    }

                    if (this.categoryFilter) {
                        url.searchParams.set('category', this.categoryFilter);
                    } else {
                        url.searchParams.delete('category');
                    }

                    if (this.minPrice) {
                        url.searchParams.set('min_price', this.minPrice);
                    } else {
                        url.searchParams.delete('min_price');
                    }

                    if (this.maxPrice) {
                        url.searchParams.set('max_price', this.maxPrice);
                    } else {
                        url.searchParams.delete('max_price');
                    }

                    url.searchParams.set('page', 1);

                    if (fromPrice) {
                        this.priceLoading = true;
                    } else {
                        this.loading = true;
                    }

                    fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html',
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('products-container').innerHTML = html;
                            this.loading = false;
                            this.priceLoading = false;
                            history.replaceState(null, '', url.toString());
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            this.loading = false;
                            this.priceLoading = false;
                        });
                },

                clearFilters() {
                    this.searchTerm = '';
                    this.categoryFilter = '';
                    this.minPrice = '';
                    this.maxPrice = '';
                    this.performSearch();
                }
            }
        }
    </script>
@endsection
