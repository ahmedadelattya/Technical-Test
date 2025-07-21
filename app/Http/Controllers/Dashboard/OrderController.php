<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderEmployeeRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $employee = $request->get('employee');
        $orders = $this->paginateAllOrders(['user', 'assignedEmployee', 'status'], [
            'search' => $search,
            'status' => $status,
            'employee' => $employee,
            'per_page' => 10
        ]);

        $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'employee');
        })->get();
        $statuses = Status::all();

        if ($request->ajax()) {
            return view('dashboard.orders.partials.table', compact('orders'))->render();
        }

        return view('dashboard.orders.index', compact('orders', 'search',  'status', 'employee', 'employees', 'statuses'));
    }
    public function edit(Order $order)
    {
        $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'employee');
        })->get();
        $statuses = Status::all();
        return view('dashboard.orders.edit', compact('order', 'employees', 'statuses'));
    }

    public function updateEmployee(UpdateOrderEmployeeRequest $request, Order $order)
    {
        $newEmployeeId = $request->validated()['employee_id'];

        if ($order->assigned_to != $newEmployeeId) {
            $order->assigned_to = $newEmployeeId;
            $order->save();
        }
        return back()->with([
            'success_title' => 'Assignment updated',
            'success' => 'Assigned employee updated.',
        ]);
    }
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $newStatusId = $request->validated()['status'];
        $order->load(['items']);
        if ($order->status_id != $newStatusId) {
            $cancelStatusId = Status::where('name', 'cancelled')->value('id');

            if ($newStatusId == $cancelStatusId) {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);
                    }
                }
            }

            $order->status_id = $newStatusId;
            $order->save();
        }
        return back()->with([
            'success_title' => 'Status updated',
            'success' => 'Order status updated.',
        ]);
    }
    private function paginateAllOrders($relations = [], $data = [])
    {
        $perPage = $data['per_page'] ?? 12;
        $query = Order::with($relations);

        if (isset($data['search']) && !empty($data['search'])) {
            $statement = $data['search'];
            $query->where(function ($q) use ($statement) {
                $q->whereHas('user', function ($q) use ($statement) {
                    $q->whereLike('name', '%' . $statement . '%')
                        ->orWhereLike('email', '%' . $statement . '%');
                })
                    ->orWhereHas('assignedEmployee', function ($q) use ($statement) {
                        $q->whereLike('name', '%' . $statement . '%')
                            ->orWhereLike('email', '%' . $statement . '%');
                    });
            });
        }
        if (isset($data['status']) && !empty($data['status'])) {
            $query->where('status_id', $data['status']);
        }
        if (isset($data['employee']) && !empty($data['employee'])) {
            $query->where('assigned_to', $data['employee']);
        }
        return $query->latest()->paginate($perPage);
    }
}
