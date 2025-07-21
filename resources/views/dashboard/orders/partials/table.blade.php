<div class="bg-white shadow rounded overflow-x-auto">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-100 font-semibold">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Customer</th>
                <th class="px-4 py-2">Order Date</th>
                <th class="px-4 py-2">Total Amount</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2 whitespace-nowrap">Assigned Employee</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $loop->iteration + ($orders->firstItem() - 1) }}</td>


                    {{-- Customer --}}
                    <td class="px-4 py-2">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900">
                                {{ $order->user->name ?? 'N/A' }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $order->user->email ?? '' }}
                            </span>
                        </div>
                    </td>


                    {{-- Order Date --}}
                    <td class="px-4 py-2 text-xs text-gray-600">
                        {{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d M Y') }}
                    </td>


                    {{-- Total Price --}}
                    <td class="px-4 py-2">
                        <span class="text-gray-800 font-semibold">
                            ${{ number_format($order->total_amount, 2) }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-2">
                        @php
                            $status = strtolower($order->status->name);
                            $badgeClasses = [
                                'pending' => 'bg-gray-100 text-gray-800',
                                'shipped' => 'bg-blue-100 text-blue-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span
                            class="px-2 py-1 text-xs font-medium rounded-full {{ $badgeClasses[$status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>


                    {{-- Assigned Employee --}}
                    <td class="px-4 py-2">
                        @if ($order->assignedEmployee)
                            <div class="flex items-center gap-2">
                                <div
                                    class="flex items-center justify-center w-6 h-6 bg-gray-100 text-gray-500 rounded-full text-xs font-semibold">
                                    {{ strtoupper(substr($order->assignedEmployee->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">
                                        {{ $order->assignedEmployee->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Order Processing
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-gray-600">Unassigned</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-2 text-right">
                        <div class="flex justify-end gap-2">
                            @can(\App\Enums\PermissionEnum::ORDER_UPDATE['name'])
                                <a href="{{ route('dashboard.orders.edit', $order) }}"
                                    class="text-black hover:text-gray-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-8 text-gray-500">
                        @if (request()->anyFilled(['search', 'status', 'employee']))
                            No Orders found matching your filters.
                        @else
                            No Orders found.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if ($orders->hasPages())
        <div class="my-4" id="pagination-container">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
</div>
