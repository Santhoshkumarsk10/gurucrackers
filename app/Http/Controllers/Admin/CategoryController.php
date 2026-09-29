<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('page')) {
            $page = (string) $request->input('page');
            if (!ctype_digit($page) || (int) $page < 1) {
                $query = $request->query();
                unset($query['page']);
                return redirect()->route('admin.categories.index', $query);
            }
        }

        $status = $request->input('status', 'active');
        $rawSearch = (string) $request->input('search', '');
        $search = trim(preg_replace('/[^a-zA-Z0-9\x{0B80}-\x{0BFF}\s\.\-\/]/u', '', $rawSearch));
        $search = preg_replace('/\-+/', '-', $search);
        $search = trim($search, "-./ \t\n\r\0\x0B");

        if (!preg_match('/[a-zA-Z0-9\x{0B80}-\x{0BFF}]/u', $search)) {
            $search = '';
        }

        $activeCount = Category::count();
        $trashedCount = Category::onlyTrashed()->count();

        $query = ($status === 'trash')
            ? Category::onlyTrashed()->withCount(['products' => fn ($q) => $q->withTrashed()])
            : Category::withCount('products');

        $categories = $query
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $nextOrder = (Category::max('sort_order') ?? 0) + 1;

        return view('admin.categories.index', compact('categories', 'search', 'nextOrder', 'status', 'activeCount', 'trashedCount'));
    }

    public function create()
    {
        $nextOrder = (Category::max('sort_order') ?? 0) + 1;
        return view('admin.categories.create', compact('nextOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:60|unique:categories,name',
            'icon' => 'nullable|string|max:10',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'icon' => $validated['icon'] ?: '💥',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "Category '{$category->name}' created successfully!");
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:60|unique:categories,name,' . $category->id,
            'icon' => 'nullable|string|max:10',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'icon' => $validated['icon'] ?: '💥',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "Category '{$category->name}' updated successfully!");
    }

    public function destroy(Category $category)
    {
        $productsCount = $category->products()->count();

        if ($productsCount > 0) {
            return back()->withErrors([
                'error' => "Cannot delete '{$category->name}' because it contains {$productsCount} active products. Please reassign or delete the products first."
            ]);
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', "Category '{$name}' moved to trash! You can restore it anytime.");
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()
            ->route('admin.categories.index', ['status' => 'trash'])
            ->with('status', "Category '{$category->name}' restored successfully!");
    }

    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);

        if ($category->products()->withTrashed()->count() > 0) {
            return back()->withErrors([
                'error' => "Cannot permanently delete '{$category->name}' because products are still linked to it."
            ]);
        }

        $name = $category->name;
        $category->forceDelete();

        return redirect()
            ->route('admin.categories.index', ['status' => 'trash'])
            ->with('status', "Category '{$name}' permanently deleted.");
    }
}
