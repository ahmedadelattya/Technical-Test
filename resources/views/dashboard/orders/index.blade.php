@extends('dashboard.layouts.app')

@section('content')
    <div class="p-6" x-data="orderSearch()">
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
                    <input type="text" x-model="searchTerm" @input="debounceSearch()" placeholder="Search orders..."
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
        </div>

        <!-- Filters -->
        <div x-show="showFilters" x-transition class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Status -->
            <div>
                <select x-model="statusFilter" @change="performSearch()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ ucfirst($status->name) }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Employees -->
            <div>
                <select x-model="employeeFilter" @change="performSearch()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Employees</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Clear Filters -->
            <div>
                <button @click="clearFilters()" class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg">
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Results -->
        <div id="orders-container">
            @include('dashboard.orders.partials.table', ['orders' => $orders])
        </div>
    </div>

    <script>
        function orderSearch() {
            return {
                searchTerm: '{{ $search ?? '' }}',
                statusFilter: '',
                employeeFilter: '',
                loading: false,
                priceLoading: false,
                showFilters: false,
                searchTimeout: null,

                debounceSearch() {
                    clearTimeout(this.searchTimeout);
                    this.loading = true;
                    this.searchTimeout = setTimeout(() => {
                        this.performSearch(false);
                    }, 500);
                },

                performSearch() {
                    const url = new URL(window.location.href);

                    if (this.searchTerm.trim()) {
                        url.searchParams.set('search', this.searchTerm);
                    } else {
                        url.searchParams.delete('search');
                    }

                    if (this.statusFilter) {
                        url.searchParams.set('status', this.statusFilter);
                    } else {
                        url.searchParams.delete('status');
                    }

                    if (this.employeeFilter) {
                        url.searchParams.set('employee', this.employeeFilter);
                        console.log('employee : ' + this.employeeFilter);
                    } else {
                        url.searchParams.delete('employee');
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
                            document.getElementById('orders-container').innerHTML = html;
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
                    this.statusFilter = '';
                    this.employeeFilter = '';
                    this.performSearch();
                }
            }
        }
    </script>
@endsection
