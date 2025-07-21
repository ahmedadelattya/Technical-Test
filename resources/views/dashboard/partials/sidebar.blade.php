@php
    use App\Enums\PermissionEnum;
@endphp

<aside class="fixed top-0 left-0 w-64 h-full bg-black text-white shadow-2xl overflow-y-auto">
    <!-- Logo Section -->
    <div class="p-6 ">
        <div class="flex flex-col items-center">
            <a href="{{ url('/') }}">
                <x-application-logo class="w-20 h-20 fill-current text-white" />
            </a>
            <div class="h-[2px] w-48 bg-white/30 mt-8 rounded-full"></div>
        </div>
    </div>


    <!-- Navigation -->
    <nav class="p-4" x-data="{
        openManagement: {{ request()->routeIs('dashboard.users.*') || request()->routeIs('dashboard.roles.*') ? 'true' : 'false' }},
    }">

        @canany([PermissionEnum::USER_READ['name'], PermissionEnum::ROLE_READ['name']])
            <!-- Management Accordion -->
            <div class="mt-4">
                <button @click="openManagement = !openManagement"
                    class="w-full flex items-center justify-between py-3 px-4 rounded-lg hover:bg-gray-700 text-white font-bold">
                    <span>Management</span>
                    <svg :class="{ 'rotate-180': openManagement }" class="w-4 h-4 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </button>
                <div x-show="openManagement" class="ml-6 mt-2 space-y-1">
                    @can(PermissionEnum::USER_READ['name'])
                        <a href="{{ route('dashboard.users.index') }}"
                            class="flex items-center py-2 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('dashboard.users.*') ? 'bg-gray-700 font-bold' : 'text-white font-bold' }}">
                            Users
                        </a>
                    @endcan
                    @can(PermissionEnum::ROLE_READ['name'])
                        <a href="{{ route('dashboard.roles.index') }}"
                            class="flex items-center py-2 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('dashboard.roles.*') ? 'bg-gray-700 font-bold' : 'text-white font-bold' }}">
                            Roles
                        </a>
                    @endcan
                </div>
            </div>
        @endcanany
        <!-- Categories -->
        <div class="mt-4">
            <a href="{{ route('dashboard.categories.index') }}"
                class="flex items-center py-3 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('dashboard.categories.*') ? 'bg-gray-700 font-bold' : 'text-white font-bold' }}">
                Categories
            </a>
        </div>
        <!-- Products -->
        <div class="mt-4">
            <a href="{{ route('dashboard.products.index') }}"
                class="flex items-center py-3 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('dashboard.products.*') ? 'bg-gray-700 font-bold' : 'text-white font-bold' }}">
                Products
            </a>
        </div>
        <!-- Orders -->
        <div class="mt-4">
            <a href="{{ route('dashboard.orders.index') }}"
                class="flex items-center py-3 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('dashboard.orders.*') ? 'bg-gray-700 font-bold' : 'text-white font-bold' }}">
                Orders
            </a>
        </div>

    </nav>
</aside>
