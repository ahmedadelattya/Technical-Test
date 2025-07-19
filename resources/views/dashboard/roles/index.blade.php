@extends('dashboard.layouts.app')

@section('content')
    <div class="p-6 max-w-3xl mx-auto" x-data="roleSearch()">
        <!-- Session Flash Messages -->
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

        <!-- Top Controls: Search, Create Button -->
        <div class="flex flex-col md:flex-row justify-between items-stretch gap-4 mb-4">
            <!-- Left: Search + Filter Toggle -->
            <div class="flex w-full md:max-w-md gap-2">
                <!-- Search Bar -->
                <div class="relative flex-1">
                    <input type="text" x-model="searchTerm" @input="debounceSearch()" placeholder="Search roles by name..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute right-3 top-2.5">
                        <!-- Spinner or Search Icon -->
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
            </div>

            <!-- Right: Create Role Button -->
            <div>
                <a href="{{ route('dashboard.roles.create') }}"
                    class="bg-black hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <span class="font-medium">Create Role</span>
                    <span class="text-lg font-bold">+</span>
                </a>
            </div>
        </div>

        <!-- Results Container -->
        <div id="roles-container">
            @include('dashboard.roles.partials.table', ['roles' => $roles])
        </div>
    </div>

    <script>
        function roleSearch() {
            return {
                searchTerm: '{{ $search ?? '' }}',
                loading: false,
                showFilters: false,
                searchTimeout: null,

                debounceSearch() {
                    clearTimeout(this.searchTimeout);
                    this.loading = true;

                    this.searchTimeout = setTimeout(() => {
                        this.performSearch();
                    }, 500);
                },

                performSearch() {
                    const url = new URL(window.location.href);

                    // Set search parameters
                    if (this.searchTerm.trim()) {
                        url.searchParams.set('search', this.searchTerm);
                    } else {
                        url.searchParams.delete('search');
                    }
                    url.searchParams.set('page', 1);
                    fetch(url.toString(), {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html',
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('roles-container').innerHTML = html;
                            this.loading = false;
                            history.replaceState(null, '', url.toString());
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            this.loading = false;
                        });
                },
            }
        }
    </script>
@endsection
