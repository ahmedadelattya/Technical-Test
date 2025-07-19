<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
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

it('renders the users index page', function () {
    $response = $this->get(route('dashboard.users.index'));

    $response->assertOk();
    $response->assertViewIs('dashboard.users.index');
});

it('renders the create user page', function () {
    $response = $this->get(route('dashboard.users.create'));

    $response->assertOk();
    $response->assertViewIs('dashboard.users.create');
});

it('can create a new user with role', function () {
    $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);
    $response = $this->post(route('dashboard.users.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
        'role_id' => $role->id,
    ]);

    $response->assertRedirect(route('dashboard.users.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]);
});

it('can create a new user without role', function () {
    $response = $this->post(route('dashboard.users.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ]);

    $response->assertRedirect(route('dashboard.users.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com'
    ]);
});

it('renders the edit user page', function () {
    $user = User::factory()->create();

    $response = $this->get(route('dashboard.users.edit', $user->id));

    $response->assertOk();
    $response->assertViewIs('dashboard.users.edit');
});

it('updates a user with new data and role', function () {
    $user = User::factory()->create(['name' => 'Old Name']);
    $role = Role::create(['name' => 'Editor', 'guard_name' => 'web']);

    $response = $this->put(route('dashboard.users.update', $user->id), [
        'name' => 'Updated Name',
        'email' => $user->email,
        'role_id' => $role->id,
    ]);

    $response->assertRedirect(route('dashboard.users.index'));
    expect($user->fresh()->name)->toBe('Updated Name');
});

it('updates a user without password when password field is empty', function () {
    $user = User::factory()->create();
    $originalPassword = $user->password;

    $response = $this->put(route('dashboard.users.update', $user->id), [
        'name' => 'Updated Name',
        'email' => $user->email,
        'password' => '',
    ]);

    $response->assertRedirect(route('dashboard.users.index'));
    expect($user->fresh()->password)->toBe($originalPassword);
});

it('prevents updating the super admin user', function () {
    $superAdmin = User::find(1);

    $response = $this->put(route('dashboard.users.update', $superAdmin->id), [
        'name' => 'Hacked Name',
        'email' => $superAdmin->email,
    ]);

    $response->assertRedirect(route('dashboard.users.edit', $superAdmin->id));
    $response->assertSessionHas('error', 'Cannot update the super admin.');
});

it('can delete a user', function () {
    $user = User::factory()->create();

    $response = $this->delete(route('dashboard.users.destroy', $user->id));

    $response->assertRedirect(route('dashboard.users.index'));
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('prevents deleting the super admin user', function () {
    $superAdmin = User::find(1);

    $response = $this->delete(route('dashboard.users.destroy', $superAdmin->id));

    $response->assertRedirect(route('dashboard.users.index'));
    $response->assertSessionHas('error', 'Cannot delete the super admin user.');
});

it('filters users by search term', function () {
    User::factory()->create(['name' => 'John Smith', 'email' => 'john@example.com']);
    User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

    $response = $this->get(route('dashboard.users.index', ['search' => 'John']));

    $response->assertOk();
    $response->assertSee('John Smith');
    $response->assertDontSee('Jane Doe');
});

it('excludes the current authenticated user from the user table partial', function () {
    $response = $this->get(route('dashboard.users.index'), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertOk();
    $response->assertDontSee($this->user->name);
});
