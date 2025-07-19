<div class="bg-white shadow rounded overflow-x-auto">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-100 font-semibold">
            <tr>
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Permissions</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($roles as $role)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $loop->iteration + ($roles->firstItem() - 1) }}</td>
                    <td class="px-4 py-2">{{ $role->name }}</td>
                    <td class="px-4 py-2">{{ $role->permissions_count }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('dashboard.roles.edit', $role) }}" class="inline-block">
                            <i class="fas fa-edit text-black hover:text-gray-700 transition duration-200"></i>
                        </a>
                        @can(\App\Enums\PermissionEnum::ROLE_DELETE['name'])
                            <form action="{{ route('dashboard.roles.destroy', $role) }}" method="POST"
                                class="inline-block ml-2" onsubmit="return confirm('Delete this role?')">
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
                            No roles found matching "{{ request('search') }}"
                        @else
                            No roles found.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($roles->hasPages())
    <div class="mt-4" id="pagination-container">
        {{ $roles->appends(request()->query())->links() }}
    </div>
@endif

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
                            document.getElementById('roles-container').innerHTML = html;
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
