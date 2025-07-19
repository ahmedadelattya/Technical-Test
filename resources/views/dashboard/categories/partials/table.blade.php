<div class="bg-white shadow rounded overflow-x-auto">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-100 font-semibold">
            <tr>
                <th class="px-4 py-2">#</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Parent Category</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $loop->iteration + ($categories->firstItem() - 1) }}</td>
                    <td class="px-4 py-2">{{ $category->name ?? '' }}</td>
                    <td class="px-4 py-2">
                        @if ($category->parent)
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                {{ $category->parent->name }}
                            </span>
                        @else
                            <span class="text-gray-500 text-xs">Main Category</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('dashboard.categories.edit', $category) }}" class="inline-block">
                            <i class="fas fa-edit text-black hover:text-gray-700 transition duration-200"></i>
                        </a>
                        @can(\App\Enums\PermissionEnum::CATEGORY_DELETE['name'])
                            <form action="{{ route('dashboard.categories.destroy', $category) }}" method="POST"
                                class="inline-block ml-2" onsubmit="return confirm('Delete this category?')">
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
                    <td colspan="5" class="text-center py-8 text-gray-500">
                        @if (request('search'))
                            No categories found matching "{{ request('search') }}"
                        @else
                            No categories found.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($categories->hasPages())
    <div class="mt-4" id="pagination-container">
        {{ $categories->appends(request()->query())->links() }}
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
                            document.getElementById('categories-container').innerHTML = html;
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
