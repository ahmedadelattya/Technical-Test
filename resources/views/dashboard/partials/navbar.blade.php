{{-- Complete header template matching your design --}}
<header class="bg-white border-b border-gray-200 px-6 py-4">
    <div class="flex justify-between items-center">
        {{-- Left side: Breadcrumbs --}}
        <div class="flex items-center">
            @include('components.breadcrumbs')
        </div>

        {{-- Right side: User menu --}}
        <div class="relative">
            <div class="flex items-center gap-3 cursor-pointer group" onclick="toggleUserMenu()">
                {{-- User Avatar --}}
                <div
                    class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-sm font-medium text-gray-700">
                    {{ strtoupper(substr(auth()?->user()?->name ?? 'A', 0, 1)) }}
                </div>

                {{-- Dropdown Arrow --}}
                <svg class="w-4 h-4 text-gray-500 transition-transform group-hover:rotate-180" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            {{-- Dropdown Menu --}}
            <div id="userMenu"
                class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50">
                <div class="py-2">
                    {{-- User Info --}}
                    <div class="px-4 py-2 border-b border-gray-100">
                        <div class="font-medium text-gray-900">{{ auth()?->user()?->name }}</div>
                        <div class="text-sm text-gray-500">{{ auth()?->user()?->email }}</div>
                        @if (auth()?->user()?->role)
                            <div class="text-xs text-gray-400 mt-1">{{ auth()?->user()?->role }}</div>
                        @endif
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button
                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('hidden');
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('userMenu');
        const trigger = event.target.closest('[onclick="toggleUserMenu()"]');

        if (!trigger && !menu.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });
</script>
