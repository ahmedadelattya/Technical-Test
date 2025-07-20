<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Enums\MediaTypeEnum;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Traits\FileHandler;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use FileHandler;
    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        $products = $this->paginateAllProducts(['image', 'categories'], [
            'search' => $search,
            'categories' => $category ? [$category] : null,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'per_page' => 10
        ]);

        $categories = Category::all();

        if ($request->ajax()) {
            return view('dashboard.products.partials.table', compact('products'))->render();
        }

        return view('dashboard.products.index', compact('products', 'search',  'category', 'minPrice', 'maxPrice',  'categories'));
    }

    public function create()
    {
        $product = new Product();
        $categories = Category::all();
        return view('dashboard.products.create', compact('product', 'categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $this->storeProduct($request->validated());
        return redirect()->route('dashboard.products.index')->with('success', 'Product created successfully.');
    }
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('dashboard.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $updatedProduct = $this->updateProduct($product->id, $request->validated());

        if ($updatedProduct) {
            return redirect()->route('dashboard.products.index')->with('success', 'Product updated successfully.');
        }
        return redirect()->route('dashboard.products.index')->with('error', 'Failed to update product.');
    }

    public function destroy(Product $product)
    {
        $deleted = $this->delete($product->id);
        if ($deleted) {
            return redirect()->route('dashboard.products.index')->with('success', 'Product deleted successfully.');
        }
        return redirect()->route('dashboard.products.index')->with('error', 'Failed to delete product.');
    }

    // Helper functions
    private function paginateAllProducts($relations = [], $data = [])
    {
        $perPage = $data['per_page'] ?? 12;
        $query = Product::with($relations);

        if (isset($data['search']) && !empty($data['search'])) {
            $statement = $data['search'];
            $query->where(function ($q) use ($statement) {
                $q->whereLike('name', '%' . $statement . '%')
                    ->orWhereLike('description', '%' . $statement . '%');
            });
        }
        if (isset($data['categories']) && !empty($data['categories'])) {
            $selectedIds = $data['categories'];
            $categories = Category::whereIn('id', $selectedIds)->get(['id', 'parent_id']);
            $parentIds = $categories->whereNull('parent_id')->pluck('id')->toArray();
            $childIds = $categories->whereNotNull('parent_id')->pluck('id')->toArray();
            $finalIds = $childIds;
            if (!empty($parentIds)) {
                $childrenOfParents = Category::whereIn('parent_id', $parentIds)->pluck('id')->toArray();
                $finalIds = array_merge($finalIds, $parentIds, $childrenOfParents);
            }
            $finalIds = array_unique($finalIds);
            $query->whereHas('categories', function ($q) use ($finalIds) {
                $q->whereIn('category_id', $finalIds);
            });
        }
        if (isset($data['min_price'])) {
            $query->where('price', '>=', $data['min_price']);
        }

        if (isset($data['max_price'])) {
            $query->where('price', '<=', $data['max_price']);
        }
        return $query->latest()->paginate($perPage);
    }

    public function storeProduct(array $data)
    {
        $image = null;
        $categories = $data["categories"] ?? null;
        if (isset($data['image'])) {
            $image = $data['image'];
        }
        $product = Product::create($data);
        if ($image) {
            $this->StoreMediaToModel($product, $image, MediaTypeEnum::FEATURED);
        }
        if ($categories) {
            $product->categories()->attach($categories);
        }
        return $product;
    }

    public function updateProduct($id, $data, $relations = [])
    {
        $product = $this->view($id);
        $categories = $data["categories"] ?? null;
        $image = null;

        if (!$product) {
            return null;
        }
        if (isset($data['image'])) {
            $image = $data['image'];
        }
        if ($image) {
            $this->ReplaceFeaturedMedia($product, $image, MediaTypeEnum::FEATURED);
        }
        $product->fill($data);
        if (isset($data['categories'])) {
            $product->categories()->sync($categories);
        }
        $product->save();
        return $product->load($relations);
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return false;
        }
        $this->deleteModelMultipleMedia($product);
        $product->delete();
        return true;
    }

    public function view($id, $relations = [])
    {
        return Product::with($relations)->find($id);
    }
}
