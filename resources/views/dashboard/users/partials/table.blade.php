<div class="bg-white shadow rounded overflow-x-auto">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-100 font-semibold">
            <tr>
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $loop->iteration + ($users->firstItem() - 1) }}</td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2 text-right">
                        @can(\App\Enums\PermissionEnum::USER_UPDATE['name'])
                            <a href="{{ route('dashboard.users.edit', $user) }}" class="inline-block">
                                <i <i class="fas fa-edit text-black hover:text-gray-700 transition duration-200"></i>
                            </a>
                        @endcan

                        @can(\App\Enums\PermissionEnum::USER_DELETE['name'])
                            <form action="{{ route('dashboard.users.destroy', $user) }}" method="POST"
                                class="inline-block ml-2" onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">
                                    <i class="fas fa-trash text-red-600 hover:text-red-800 transition duration-200"></i>
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-8 text-gray-500">
                        @if (request('search'))
                            No users found matching "{{ request('search') }}"
                        @else
                            No users found.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if ($users->hasPages())
        <div class="my-4" id="pagination-container">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script>
    // Handle pagination clicks for AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const paginationContainer = document.getElementById('pagination-container');
        if (paginationContainer) {
            paginationContainer.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' && e.target.getAttribute('href')) {
                    e.preventDefault();
                    const url = e.target.getAttribute('href');

                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html',
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('users-container').innerHTML = html;
                            history.replaceState(null, '', url);
                        })
                        .catch(error => {
                            console.error('Pagination error:', error);
                        });
                }
            });
        }
    });
</script>
