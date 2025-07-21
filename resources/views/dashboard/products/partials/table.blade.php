<div class="bg-white shadow rounded overflow-x-auto">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-100 font-semibold">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Categories</th>
                <th class="px-4 py-2">Price</th>
                <th class="px-4 py-2">Quantity</th>
                <th class="px-4 py-2 whitespace-nowrap">Created At</th>
                <th class="px-4 py-2 whitespace-nowrap">Updated At</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $loop->iteration + ($products->firstItem() - 1) }}</td>
                    <td class="px-4 py-2 font-medium">{{ $product->name ?? '' }}</td>

                    {{-- Categories --}}
                    <td class="px-4 py-2">
                        @if ($product->categories->count())
                            @php
                                $categories = $product->categories;
                                $count = $categories->count();
                            @endphp

                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <div class="flex gap-1" @mouseenter="open = true" @mouseleave="open = false">
                                    <span class="bg-black text-white px-2 py-0.5 rounded-full text-xs"
                                        style="white-space: nowrap;">
                                        {{ $categories[0]->name }}
                                    </span>

                                    @if ($count > 1)
                                        <span class="bg-black text-white px-2 py-0.5 rounded-full text-xs"
                                            style="white-space: nowrap;">
                                            +{{ $count - 1 }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Tooltip -->
                                <div x-show="open" x-transition
                                    class="absolute left-full top-0 ml-2 z-10 p-2 bg-white border rounded shadow-lg w-max text-xs space-y-1"
                                    style="white-space: nowrap">
                                    @foreach ($categories as $category)
                                        <span
                                            class="inline-block bg-black text-white px-2 py-0.5 rounded-full mr-1 mb-1"
                                            style="white-space: nowrap;">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>

                            </div>
                        @else
                            <span class="text-gray-400 text-xs">No categories</span>
                        @endif
                    </td>

                    {{-- Price --}}
                    <td class="px-4 py-2">
                        <span class="text-gray-800 font-semibold">
                            ${{ number_format($product->price, 2) }}
                        </span>
                    </td>

                    {{-- Quantity --}}
                    <td class="px-4 py-2">
                        <span class="text-gray-800 font-semibold">
                            {{ $product->stock_quantity }}
                        </span>
                    </td>

                    {{-- Created At --}}
                    <td class="px-4 py-2 text-xs text-gray-600">
                        {{ \Illuminate\Support\Carbon::parse($product->created_at)->format('d M Y, H:i') }}
                    </td>

                    {{-- Updated At --}}
                    <td class="px-4 py-2 text-xs text-gray-600">
                        {{ \Illuminate\Support\Carbon::parse($product->updated_at)->format('d M Y, H:i') }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-2 text-right">
                        <div class="flex justify-end gap-2">
                            @can(\App\Enums\PermissionEnum::PRODUCT_UPDATE['name'])
                                <a href="{{ route('dashboard.products.edit', $product) }}"
                                    class="text-black hover:text-gray-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan

                            @can(\App\Enums\PermissionEnum::PRODUCT_DELETE['name'])
                                <form action="{{ route('dashboard.products.destroy', $product) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Delete this Product? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-8 text-gray-500">
                        @if (request()->anyFilled(['search', 'category', 'minPrice', 'maxPrice']))
                            No Products found matching your filters.
                        @else
                            No Products found.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if ($products->hasPages())
        <div class="my-4" id="pagination-container">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @endif
</div>
