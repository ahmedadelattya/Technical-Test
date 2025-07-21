@extends('dashboard.layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold">#{{ $order->code ?? 'ORD-' . $order->id }}</h1>
                <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('m/d/Y') }}</p>
            </div>
            @php
                $status = strtolower($order->status->name);
                $badgeClasses = [
                    'pending' => 'bg-gray-200 text-gray-800',
                    'shipped' => 'bg-blue-100 text-blue-800',
                    'delivered' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                ];
            @endphp
            <span
                class="px-3 py-1 text-sm rounded-full font-medium {{ $badgeClasses[$status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ ucfirst($status) }}
            </span>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            {{-- Left: Order Items --}}
            <div class="md:col-span-2 space-y-4">
                <div class="bg-white border rounded-lg shadow-sm">
                    <div class="px-4 py-3 border-b font-bold text-sm text-gray-700">
                        Order Items
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-2">Product</th>
                                    <th class="px-4 py-2">Quantity</th>
                                    <th class="px-4 py-2">Price</th>
                                    <th class="px-4 py-2">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $item->product->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">{{ $item->quantity }}</td>
                                        <td class="px-4 py-2">${{ number_format($item->price_at_purchase, 2) }}</td>
                                        <td class="px-4 py-2 font-semibold">
                                            ${{ number_format($item->price_at_purchase * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 text-right font-bold text-sm border-t">
                        Total Amount: ${{ number_format($order->total_amount, 2) }}
                    </div>
                </div>
            </div>

            {{-- Right: Customer + Employee + Actions --}}
            <div class="space-y-4">
                {{-- Customer Info --}}
                <div class="bg-white border rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-bold mb-2">Customer Information</h3>
                    <div class="text-sm text-gray-700 font-semibold">{{ $order->user->name }}</div>
                    <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                    <div class="text-sm text-gray-700 mt-2 font-semibold">
                        {{ $order->shipping_address ?? 'No address available' }}
                    </div>
                </div>

                {{-- Assigned Employee --}}
                <div class="bg-white border rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-bold mb-2">Assigned Employee</h3>
                    @if ($order->assignedEmployee)
                        <div class="text-sm font-semibold text-gray-700">{{ $order->assignedEmployee->name }}</div>
                        <div class="text-sm text-gray-500">{{ $order->assignedEmployee->email }}</div>
                    @else
                        <p class="text-sm text-gray-500">No employee assigned</p>
                    @endif
                </div>

                {{-- Update Status --}}
                @if (strtolower($order->status->name) !== 'cancelled')
                    <form x-data="{ originalStatus: '{{ $order->status_id }}', selectedStatus: '{{ $order->status_id }}' }" @submit.prevent="if (originalStatus != selectedStatus) $el.submit()"
                        method="POST" action="{{ route('dashboard.orders.updateStatus', $order) }}"
                        class="bg-white border rounded-lg shadow-sm p-4">
                        @csrf
                        @method('PUT')
                        <h3 class="text-lg font-bold mb-2">Update Status</h3>
                        <select name="status" x-model="selectedStatus" class="w-full border-gray-300 rounded text-sm">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}">{{ ucfirst($status->name) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" :disabled="originalStatus == selectedStatus"
                            x-text="originalStatus == selectedStatus ? 'No Changes' : 'Update Status'"
                            class="mt-3 w-full py-2 text-sm rounded-lg font-semibold focus:outline-none focus:ring-2 focus:ring-black"
                            :class="originalStatus == selectedStatus ?
                                'bg-gray-300 text-gray-600 cursor-not-allowed' :
                                'bg-black text-white hover:bg-gray-800'"></button>
                    </form>
                    {{-- Update Employee --}}
                    <form x-data="{ originalEmployee: '{{ $order->assignedEmployee->id ?? '' }}', selectedEmployee: '{{ $order->assignedEmployee->id ?? '' }}' }" @submit.prevent="if (originalEmployee != selectedEmployee) $el.submit()"
                        method="POST" action="{{ route('dashboard.orders.updateEmployee', $order) }}"
                        class="bg-white border rounded-lg shadow-sm p-4">
                        @csrf
                        @method('PUT')
                        <h3 class="text-lg font-bold mb-2">Employee Assignment</h3>
                        <select name="employee_id" x-model="selectedEmployee"
                            class="w-full border-gray-300 rounded text-sm">
                            <option value="">Unassigned</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" :disabled="originalEmployee == selectedEmployee"
                            x-text="originalEmployee == selectedEmployee ? 'No Changes' : 'Update Employee'"
                            class="mt-3 w-full py-2 text-sm rounded-lg font-semibold focus:outline-none focus:ring-2 focus:ring-black"
                            :class="originalEmployee == selectedEmployee ?
                                'bg-gray-300 text-gray-600 cursor-not-allowed' :
                                'bg-black text-white hover:bg-gray-800'"></button>
                    </form>
                @endif


            </div>
        </div>
    </div>
@endsection
