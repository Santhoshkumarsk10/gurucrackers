<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('page')) {
            $page = (string) $request->input('page');
            if (!ctype_digit($page) || (int) $page < 1) {
                $query = $request->query();
                unset($query['page']);
                return redirect()->route('admin.products.index', $query);
            }
        }

        $status = $request->input('status', 'active');
        $categoryId = $request->input('category_id');
        $rawSearch = (string) $request->input('search', '');
        $search = trim(preg_replace('/[^a-zA-Z0-9\x{0B80}-\x{0BFF}\s\.\-\/]/u', '', $rawSearch));
        $search = preg_replace('/\-+/', '-', $search);
        $search = trim($search, "-./ \t\n\r\0\x0B");

        if (!preg_match('/[a-zA-Z0-9\x{0B80}-\x{0BFF}]/u', $search)) {
            $search = '';
        }

        $activeCount = Product::count();
        $trashedCount = Product::onlyTrashed()->count();

        $categories = Category::withTrashed()->orderBy('sort_order')->orderBy('name')->get();

        $query = ($status === 'trash')
            ? Product::onlyTrashed()->with(['category' => fn ($q) => $q->withTrashed()])
            : Product::with('category');

        $products = $query
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('tamil_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('category_id')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'categories', 'categoryId', 'search', 'status', 'activeCount', 'trashedCount'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|min:2|max:100',
            'tamil_name' => 'nullable|string|min:2|max:100',
            'unit' => 'required|string|min:1|max:30',
            'actual_rate' => 'required|numeric|min:1|max:999999',
            'net_rate' => 'nullable|numeric|min:0|max:999999',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $shop = Shop::current();
        $companyDiscount = ($shop && $shop->offer_percentage !== null) ? (int) $shop->offer_percentage : 90;

        $actualRate = (float) $validated['actual_rate'];
        $netRate = round($actualRate * (1 - ($companyDiscount / 100)), 2);
        $discount = $companyDiscount;

        $category = Category::findOrFail($validated['category_id']);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'category_id' => $category->id,
            'name' => $validated['name'],
            'tamil_name' => $validated['tamil_name'] ?? null,
            'unit' => $validated['unit'],
            'image' => $imagePath,
            'actual_rate' => $actualRate,
            'net_rate' => $netRate,
            'discount_percent' => $discount,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('status', "Product '{$product->name}' added successfully!");
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('sort_order')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|min:2|max:100',
            'tamil_name' => 'nullable|string|min:2|max:100',
            'unit' => 'required|string|min:1|max:30',
            'actual_rate' => 'required|numeric|min:1|max:999999',
            'net_rate' => 'nullable|numeric|min:0|max:999999',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $shop = Shop::current();
        $companyDiscount = ($shop && $shop->offer_percentage !== null) ? (int) $shop->offer_percentage : 90;

        $actualRate = (float) $validated['actual_rate'];
        $netRate = round($actualRate * (1 - ($companyDiscount / 100)), 2);
        $discount = $companyDiscount;

        $category = Category::findOrFail($validated['category_id']);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = null;
        }

        $product->update([
            'category_id' => $category->id,
            'name' => $validated['name'],
            'tamil_name' => $validated['tamil_name'] ?? null,
            'unit' => $validated['unit'],
            'image' => $imagePath,
            'actual_rate' => $actualRate,
            'net_rate' => $netRate,
            'discount_percent' => $discount ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('status', "Product '{$product->name}' updated successfully!");
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', "Product '{$name}' moved to trash! You can restore it anytime.");
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()
            ->route('admin.products.index', ['status' => 'trash'])
            ->with('status', "Product '{$product->name}' restored successfully!");
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $name = $product->name;
        $product->forceDelete();

        return redirect()
            ->route('admin.products.index', ['status' => 'trash'])
            ->with('status', "Product '{$name}' permanently deleted.");
    }

    /**
     * Handle bulk upload of products from XLSX or CSV file.
     */
    public function bulkUpload(Request $request, \App\Services\ProductImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
            'update_existing' => 'nullable|boolean',
        ], [
            'file.required' => 'Please select an Excel (.xlsx) or CSV file to upload.',
            'file.mimes' => 'The upload file must be an Excel (.xlsx, .xls) or CSV (.csv) file.',
            'file.max' => 'The file size must not exceed 10MB.',
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $updateExisting = $request->has('update_existing');

            $result = $importService->import(
                $file->getRealPath(),
                $extension,
                dryRun: false,
                updateExisting: $updateExisting
            );

            $msg = "Bulk upload successful! {$result['products_created']} products added, {$result['products_updated']} updated across {$result['categories_detected']} categories.";
            if ($result['products_skipped'] > 0) {
                $msg .= " ({$result['products_skipped']} duplicate products skipped).";
            }

            \App\Services\AuditLogger::log(
                event: 'bulk_upload',
                module: 'products',
                summary: "Bulk uploaded products from {$file->getClientOriginalName()}: {$result['products_created']} created, {$result['products_updated']} updated",
                newValues: $result
            );

            return redirect()
                ->route('admin.products.index')
                ->with('status', $msg);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Bulk upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Download the sample product Excel template.
     */
    public function downloadSampleTemplate()
    {
        $templatePath = storage_path('app/templates/sample_products.xlsx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Sample template file not found. Please contact administrator.');
        }

        return response()->download($templatePath, 'sample_products.xlsx');
    }
}
