<?php

use App\Models\Order;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->user = User::factory()->create();
    $adminRole = Role::where('name', 'admin')->first();
    $this->user->assignRole($adminRole);
    $this->actingAs($this->user);
    $this->employee = User::factory()->create();
    $employeeRole = Role::where('name', 'employee')->first();
    $this->employee->assignRole($employeeRole);
    $this->order = Order::factory()->create([
        'assigned_to' => $this->employee->id,
        'user_id' => $this->user->id,
    ]);
});

it('renders the orders index page', function () {
    $response = $this->get(route('dashboard.orders.index'));
    $response->assertOk();
    $response->assertViewIs('dashboard.orders.index');
});

it('renders the edit order page', function () {
    $response = $this->get(route('dashboard.orders.edit', $this->order->id));
    $response->assertOk();
    $response->assertViewIs('dashboard.orders.edit');
});

it('updates the assigned employee for an order', function () {
    $newEmployee = User::factory()->create();
    $employeeRole = Role::where('name', 'employee')->first();
    $newEmployee->assignRole($employeeRole);
    $response = $this->put(route('dashboard.orders.updateEmployee', $this->order->id), [
        'employee_id' => $newEmployee->id,
    ]);
    $response->assertRedirect();
    expect($this->order->fresh()->assigned_to)->toBe($newEmployee->id);
});

it('updates the status for an order', function () {
    $status = Status::create(['name' => 'processing']);
    $response = $this->put(route('dashboard.orders.updateStatus', $this->order->id), [
        'status' => $status->id,
    ]);
    $response->assertRedirect();
    expect($this->order->fresh()->status_id)->toBe($status->id);
});

it('filters orders by search term', function () {
    $order = Order::factory()->create(['user_id' => $this->user->id]);
    $response = $this->get(route('dashboard.orders.index', ['search' => $this->user->name]));
    $response->assertOk();
    $response->assertSee($this->user->name);
});

it('filters orders by status', function () {
    $status = Status::create(['name' => 'completed']);
    $order = Order::factory()->create(['status_id' => $status->id]);
    $response = $this->get(route('dashboard.orders.index', ['status' => $status->id]));
    $response->assertOk();
    $response->assertSee(ucfirst($status->name));
});

it('filters orders by employee', function () {
    $order = Order::factory()->create(['assigned_to' => $this->employee->id]);
    $response = $this->get(route('dashboard.orders.index', ['employee' => $this->employee->id]));
    $response->assertOk();
    $response->assertSee($this->employee->name);
});

it('shows orders table partial for ajax requests', function () {
    $response = $this->get(route('dashboard.orders.index'), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);
    $response->assertOk();
    $response->assertSee((string) $this->order->id);
});

it('does not show update forms when order is cancelled', function () {
    $cancelledStatus = Status::firstOrCreate(['name' => 'cancelled']);
    $this->order->update(['status_id' => $cancelledStatus->id]);
    $response = $this->get(route('dashboard.orders.edit', $this->order->id));
    $response->assertOk();
    $response->assertDontSee('Update Status');
    $response->assertDontSee('Employee Assignment');
});
