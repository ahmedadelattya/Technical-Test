<?php

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Spatie\Permission\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->user = User::factory()->create();
    $adminRole = Role::where('name', 'admin')->first();
    $this->user->assignRole($adminRole);
    $this->category = Category::factory()->create();
    $this->product = Product::factory()->create();
    $this->actingAs($this->user);
});

it('renders the products index page', function () {
    $response = $this->get(route('dashboard.products.index'));
    $response->assertOk();
    $response->assertViewIs('dashboard.products.index');
});

it('renders the create product page', function () {
    $response = $this->get(route('dashboard.products.create'));
    $response->assertOk();
    $response->assertViewIs('dashboard.products.create');
});

it('can create a new product', function () {
    Storage::fake('public');
    $image = UploadedFile::fake()->image('test-image.png');
    $response = $this->post(route('dashboard.products.store'), [
        'name' => 'Test Product',
        'slug' => 'test-product',
        'description' => 'Test Description',
        'price' => 100,
        'stock_quantity' => 50,
        'categories' => [$this->category->id],
        'image' => $image,

    ]);
    $response->assertRedirect(route('dashboard.products.index'));
    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'description' => 'Test Description',
        'price' => 100,
    ]);
});

it('renders the edit product page', function () {
    $response = $this->get(route('dashboard.products.edit', $this->product->id));
    $response->assertOk();
    $response->assertViewIs('dashboard.products.edit');
});

it('updates a product with new data', function () {
    $response = $this->put(route('dashboard.products.update', $this->product->id), [
        'name' => 'Updated Product',
        'description' => 'Updated Description',
        'price' => 200,
        'categories' => [$this->category->id],
    ]);
    $response->assertRedirect(route('dashboard.products.index'));
    expect($this->product->fresh()->name)->toBe('Updated Product');
});

it('can delete a product', function () {
    $product = Product::factory()->create();
    $response = $this->delete(route('dashboard.products.destroy', $product->id));
    $response->assertRedirect(route('dashboard.products.index'));
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

it('filters products by search term', function () {
    Product::factory()->create(['name' => 'Apple', 'description' => 'Fresh Apple']);
    Product::factory()->create(['name' => 'Banana', 'description' => 'Yellow Banana']);
    $response = $this->get(route('dashboard.products.index', ['search' => 'Apple']));
    $response->assertOk();
    $response->assertSee('Apple');
    $response->assertDontSee('Banana');
});

it('filters products by category', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create(['name' => 'Category Product']);
    $product->categories()->attach($category->id);
    $response = $this->get(route('dashboard.products.index', ['category' => $category->id]));
    $response->assertOk();
    $response->assertSee('Category Product');
});

it('filters products by price range', function () {
    Product::factory()->create(['name' => 'Cheap', 'price' => 10]);
    Product::factory()->create(['name' => 'Expensive', 'price' => 1000]);
    $response = $this->get(route('dashboard.products.index', ['min_price' => 500, 'max_price' => 1500]));
    $response->assertOk();
    $response->assertSee('Expensive');
    $response->assertDontSee('Cheap');
});

it('shows products table partial for ajax requests', function () {
    $response = $this->get(route('dashboard.products.index'), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);
    $response->assertOk();
    $response->assertSee($this->product->name);
});
