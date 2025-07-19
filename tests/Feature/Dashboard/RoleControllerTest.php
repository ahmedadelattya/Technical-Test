<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);

    $this->user = User::factory()->create();

    $adminRole = Role::where('name', 'admin')->first();
    $this->user->assignRole($adminRole);

    $this->actingAs($this->user);
});

it('renders the roles index page', function () {
    $response = $this->get(route('dashboard.roles.index'));

    $response->assertOk();
    $response->assertViewIs('dashboard.roles.index');
});

it('renders the create role page', function () {
    $response = $this->get(route('dashboard.roles.create'));

    $response->assertOk();
    $response->assertViewIs('dashboard.roles.create');
});

it('can create a new role with permissions', function () {
    $permission = Permission::first();

    $response = $this->post(route('dashboard.roles.store'), [
        'name' => 'Editor',
        'permissions' => [$permission->name],
    ]);

    $response->assertRedirect(route('dashboard.roles.index'));
    $this->assertDatabaseHas('roles', ['name' => 'Editor']);
});

it('renders the edit role page', function () {
    $role = Role::create(['name' => 'Manager', 'guard_name' => 'web']);

    $response = $this->get(route('dashboard.roles.edit', $role->id));

    $response->assertOk();
    $response->assertViewIs('dashboard.roles.edit');
});

it('updates a role with new name and permissions', function () {
    $role = Role::create(['name' => 'Old Name', 'guard_name' => 'web']);
    $permission = Permission::first();

    $response = $this->put(route('dashboard.roles.update', $role->id), [
        'name' => 'Updated Name',
        'permissions' => [$permission->name],
    ]);

    $response->assertRedirect(route('dashboard.roles.index'));
    expect($role->fresh()->name)->toBe('Updated Name');
});

it('prevents updating the super admin role', function () {
    $role = Role::where('name', 'admin')->first();

    $permission = Permission::first();

    $response = $this->put(route('dashboard.roles.update', $role->id), [
        'name' => 'Hacked Name',
        'permissions' => [$permission->name],
    ]);

    $response->assertRedirect(route('dashboard.roles.edit', $role->id));
    $response->assertSessionHas('error', 'Cannot update the super admin role.');
});

it('can delete a role', function () {
    $role = Role::create(['name' => 'Temporary', 'guard_name' => 'web']);

    $response = $this->delete(route('dashboard.roles.destroy', $role->id));

    $response->assertRedirect(route('dashboard.roles.index'));
    $this->assertDatabaseMissing('roles', ['id' => $role->id]);
});

it('prevents deleting the super admin role', function () {
    $role = Role::where('name', 'admin')->first(); 

    $response = $this->delete(route('dashboard.roles.destroy', $role->id));

    $response->assertRedirect(route('dashboard.roles.index'));
    $response->assertSessionHas('error', 'Cannot delete the super admin role.');
});
