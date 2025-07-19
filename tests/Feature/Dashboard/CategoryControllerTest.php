<?php

use App\Models\Category;
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
    $this->category = Category::factory()->create(['name' => 'Parent Category']);
    $this->actingAs($this->user);
});

it('renders the categories index page', function () {
    $response = $this->get(route('dashboard.categories.index'));
    $response->assertOk();
    $response->assertViewIs('dashboard.categories.index');
});

it('renders the create category page', function () {
    $response = $this->get(route('dashboard.categories.create'));
    $response->assertOk();
    $response->assertViewIs('dashboard.categories.create');
});

it('can create a new category', function () {
    $response = $this->post(route('dashboard.categories.store'), [
        'name' => 'New Category',
        'parent_id' => null,
    ]);
    $response->assertRedirect(route('dashboard.categories.index'));
    $this->assertDatabaseHas('categories', [
        'name' => 'New Category',
    ]);
});

it('renders the edit category page', function () {
    $response = $this->get(route('dashboard.categories.edit', $this->category->id));
    $response->assertOk();
    $response->assertViewIs('dashboard.categories.edit');
});

it('updates a category', function () {
    $response = $this->put(route('dashboard.categories.update', $this->category->id), [
        'name' => 'Updated Category',
        'parent_id' => null,
    ]);
    $response->assertRedirect(route('dashboard.categories.index'));
    expect($this->category->fresh()->name)->toBe('Updated Category');
});

it('deletes a category with no children or posts', function () {
    $category = Category::factory()->create();
    $response = $this->delete(route('dashboard.categories.destroy', $category->id));
    $response->assertRedirect(route('dashboard.categories.index'));
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

it('prevents deleting a category with children', function () {
    $parent = Category::factory()->create();
    $child = Category::factory()->create(['parent_id' => $parent->id]);
    $response = $this->delete(route('dashboard.categories.destroy', $parent->id));
    $response->assertRedirect(route('dashboard.categories.index'));
    $response->assertSessionHas('error', 'Category cannot be deleted .');
    $this->assertDatabaseHas('categories', ['id' => $parent->id]);
});

it('filters categories by search term', function () {
    Category::factory()->create(['name' => 'Alpha Category']);
    Category::factory()->create(['name' => 'Beta Category']);
    $response = $this->get(route('dashboard.categories.index', ['search' => 'Alpha']));
    $response->assertOk();
    $response->assertSee('Alpha Category');
    $response->assertDontSee('Beta Category');
});
