<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categories = $this->PaginateAllCategories(['parent'], [
            'search' => $search,
            'per_page' => 10
        ]);

        if ($request->ajax()) {
            return view('dashboard.categories.partials.table', compact('categories'))->render();
        }

        return view('dashboard.categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        $category = new Category();
        $parentCategories = $this->getAllParentCategories(null);
        return view('dashboard.categories.create', compact('category', 'parentCategories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->StoreCategory($request->validated());
        return redirect()->route('dashboard.categories.index')->with('success', 'Category created.');
    }

    public function edit($id)
    {
        $category = $this->view($id, []);
        if (!$category) {
            return redirect()->route('dashboard.categories.index')->with('error', 'Category not found.');
        }
        $parentCategories = $this->getAllParentCategories($category->id);
        return view('dashboard.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->UpdateCategory($category->id, $request->validated());
        return redirect()->route('dashboard.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $deleted = $this->deleteCategory($category->id);
        if (!$deleted) {
            return redirect()->route('dashboard.categories.index')->with('error', 'Category cannot be deleted .');
        }
        return redirect()->route('dashboard.categories.index')->with('success', 'Category deleted.');
    }

    // helper functions
    public function StoreCategory(array $data)
    {
        $category = Category::create($data);
        return $category;
    }

    public function UpdateCategory($id, $data,)
    {
        $category = $this->view($id, []);
        if (!$category) {
            return null;
        }
        $category->fill($data);
        $category->save();
        return $category;
    }

    public function view($id, $relations = [])
    {
        $query = Category::with($relations);
        $category = $query->find($id);

        return $category;
    }

    private function PaginateAllCategories($relations = [], $data = [])
    {
        $perPage = $data['per_page'] ?? 12;
        $query = Category::with($relations);
        if (isset($data['search']) && !empty($data['search'])) {
            $statement = $data['search'];
            $query->whereLike('name', '%' . $statement . '%');
        }
        return $query->latest()->paginate($perPage);
    }

    private function getAllParentCategories($excludeId = null)
    {
        $query = Category::whereNull('parent_id');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }
    public function deleteCategory($id)
    {
        $category = $this->view($id, ['children']);
        if ($category && (count($category->children) == 0)) {
            $category->delete();
            return true;
        }
        return false;
    }
}
