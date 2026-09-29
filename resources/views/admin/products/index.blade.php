@extends('layouts.app')

@section('title', 'Manage Products - Admin')

@section('content')
<div class="space-y-5">

    <!-- Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-fire-flame-curved"></i>
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">
                    Manage Products
                </h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Add and manage cracker products with English & Tamil names, images, categories, and discounted rates.</p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                onclick="openBulkUploadModal()"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-md transition-all font-heading cursor-pointer active:scale-95"
                title="Bulk Upload products from Excel or CSV"
            >
                <i class="fa-solid fa-file-excel text-sm"></i>
                <span>Bulk Upload (Excel)</span>
            </button>

            <button
                type="button"
                onclick="openAddProductModal()"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all font-heading cursor-pointer active:scale-95"
            >
                <i class="fa-solid fa-plus"></i>
                <span>Add Product</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs: Active vs Trash -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a
            href="{{ route('admin.products.index', array_filter(['category_id' => $categoryId])) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status !== 'trash' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
        >
            <i class="fa-solid fa-check-circle"></i>
            <span>Active Products</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status !== 'trash' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $activeCount }}</span>
        </a>

        <a
            href="{{ route('admin.products.index', array_filter(['status' => 'trash', 'category_id' => $categoryId])) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'trash' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
        >
            <i class="fa-solid fa-trash-can"></i>
            <span>Trash / Archived</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'trash' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $trashedCount }}</span>
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <form method="GET" action="{{ route('admin.products.index') }}" onsubmit="if (!this.search.value.trim()) { this.search.disabled = true; } if (this.category_id && !this.category_id.value) { this.category_id.disabled = true; }" class="bg-white p-3 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        @if ($status === 'trash')
            <input type="hidden" name="status" value="trash">
        @endif
        <!-- Search Input -->
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search by English name or தமிழ் பெயர்..."
                minlength="2"
                maxlength="60"
                oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s.\-\/\u0B80-\u0BFF]/g, '').replace(/[\-\.\/]{2,}/g, '-').replace(/^[\-\.\/\s]+/, '')"
                class="w-full pl-8 pr-8 py-2 text-xs bg-slate-50 hover:bg-slate-100/70 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium"
            >
            @if ($search)
                <a href="{{ route('admin.products.index', array_filter(['status' => $status === 'trash' ? 'trash' : null, 'category_id' => $categoryId])) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs p-1" title="Clear search">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </div>

        <!-- Category Dropdown Filter -->
        <div class="w-full sm:w-56">
            <select
                name="category_id"
                onchange="this.form.onsubmit(); this.form.submit()"
                class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium"
            >
                <option value="">All Categories ({{ $categories->count() }})</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                        {{ $cat->icon }} {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Submit & Reset -->
        <div class="flex items-center gap-2">
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-3 py-2 rounded-xl transition-colors cursor-pointer">
                Filter
            </button>
            @if ($search || $categoryId)
                <a href="{{ route('admin.products.index', array_filter(['status' => $status === 'trash' ? 'trash' : null])) }}" class="text-slate-500 hover:text-rose-600 text-xs font-semibold px-2 py-2">
                    Reset
                </a>
            @endif
        </div>

        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-2 rounded-xl border border-slate-200 ml-auto hidden md:block">
            Total: {{ $products->total() }} {{ $status === 'trash' ? 'Trashed' : 'Active' }} Products
        </span>
    </form>

    <!-- Error Alerts -->
    @if (isset($errors) && $errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-800 p-4 rounded-xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Products Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left w-16">Image</th>
                        <th class="px-4 py-3 text-left">Product Name & தமிழ் பெயர்</th>
                        <th class="px-4 py-3 text-left">Category</th>
                        <th class="px-4 py-3 text-center">Unit</th>
                        <th class="px-4 py-3 text-right">MRP</th>
                        <th class="px-4 py-3 text-right">Net Price</th>
                        <th class="px-4 py-3 text-center">Discount</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Image Thumbnail -->
                            <td class="px-4 py-3">
                                <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0">
                                    @if ($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl">{{ $product->category?->icon ?: '💥' }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Product Name & Tamil Name -->
                            <td class="px-4 py-3">
                                <div class="font-extrabold text-slate-900 text-sm font-heading leading-tight">
                                    {{ $product->name }}
                                </div>
                                @if ($product->tamil_name)
                                    <div class="text-[11px] text-slate-500 font-medium mt-0.5">
                                        {{ $product->tamil_name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Category -->
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 border border-rose-100 text-rose-700 font-bold text-[11px]">
                                    <span>{{ $product->category?->icon ?: '💥' }}</span>
                                    <span>{{ $product->category?->name ?: $product->category }}</span>
                                </span>
                            </td>

                            <!-- Unit -->
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium text-[11px]">
                                    {{ $product->unit }}
                                </span>
                            </td>

                            <!-- MRP -->
                            <td class="px-4 py-3 text-right line-through text-slate-400 font-medium">
                                ₹{{ number_format($product->actual_rate, 2) }}
                            </td>

                            <!-- Net Price -->
                            <td class="px-4 py-3 text-right font-black text-rose-700 text-sm">
                                ₹{{ number_format($product->net_rate, 2) }}
                            </td>

                            <!-- Discount % -->
                            <td class="px-4 py-3 text-center">
                                @if ($product->discount_percent > 0)
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">
                                        {{ $product->discount_percent }}% OFF
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3 text-center">
                                @if ($product->trashed())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">
                                        <i class="fa-solid fa-trash-can text-[7px] text-rose-600"></i> Deleted
                                    </span>
                                @elseif ($product->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                        <i class="fa-solid fa-circle text-[7px] text-emerald-600"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
                                        <i class="fa-solid fa-circle text-[7px] text-slate-400"></i> Hidden
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($product->trashed())
                                        <form method="POST" action="{{ route('admin.products.restore', $product->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 rounded-xl transition-all font-bold text-xs cursor-pointer shadow-xs active:scale-95" title="Restore Product">
                                                <i class="fa-solid fa-rotate-left"></i>
                                                <span>Restore</span>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.products.force_delete', $product->id) }}" data-confirm="Permanently delete product '{{ $product->name }}'? This will permanently delete the image and database record. This action cannot be undone." data-confirm-title="Permanently Delete Product?" data-confirm-btn="Yes, Delete Permanently" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Permanently Delete">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button
                                            type="button"
                                            onclick='openEditProductModal(@json($product))'
                                            class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors font-semibold cursor-pointer"
                                            title="Edit Product"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>

                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-confirm="Are you sure you want to delete product '{{ $product->name }}'? It can be restored from Trash later." data-confirm-title="Move Product to Trash?" data-confirm-btn="Yes, Move to Trash" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Delete Product">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                {{ $status === 'trash' ? 'Trash is empty. No deleted products.' : 'No products found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div>
        {{ $products->links() }}
    </div>

</div>

<!-- ======================================================= -->
<!-- PRODUCT ADD / EDIT FESTIVE MODAL                        -->
<!-- ======================================================= -->
<div
    id="productModal"
    class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto flex items-center justify-center transition-all"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
>
    <div class="bg-white rounded-3xl border border-rose-100 shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col overflow-hidden my-6 animate-fadeIn">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-900 via-rose-950 to-slate-900 text-white p-5 flex items-center justify-between border-b border-white/10 shrink-0">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-rose-500/20 border border-rose-400/30 text-amber-400 flex items-center justify-center text-xl shadow-inner shrink-0">
                    <i class="fa-solid fa-fire-flame-curved"></i>
                </span>
                <div>
                    <h3 id="productModalTitle" class="text-base sm:text-lg font-black font-heading text-white tracking-wide">
                        Add New Product
                    </h3>
                    <p class="text-[11px] text-slate-300">Set cracker details, Tamil name, pricing, unit & photo.</p>
                </div>
            </div>
            <button
                type="button"
                onclick="closeProductModal()"
                class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-colors text-sm cursor-pointer"
                title="Close Modal"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Form (Scrollable) -->
        <form
            id="productForm"
            method="POST"
            action="{{ route('admin.products.store') }}"
            enctype="multipart/form-data"
            class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1"
        >
            @csrf
            <input type="hidden" name="_method" id="productMethod" value="POST">
            <input type="hidden" name="_modal" value="product">

            <!-- Category -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Category <span class="text-rose-600">*</span>
                </label>
                <select
                    name="category_id"
                    id="prodCategoryId"
                    required
                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800"
                >
                    <option value="">Select Category...</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Product Names (English & Tamil) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Product Name (English) <span class="text-rose-600">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="prodNameInput"
                        value="{{ old('name') }}"
                        required
                        minlength="2"
                        maxlength="100"
                        placeholder="e.g. Gold Lakshmi"
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800"
                    >
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        தமிழ் பெயர் (Tamil Name)
                    </label>
                    <input
                        type="text"
                        name="tamil_name"
                        id="prodTamilNameInput"
                        value="{{ old('tamil_name') }}"
                        minlength="2"
                        maxlength="100"
                        placeholder="e.g. தங்க லக்ஷ்மி"
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800"
                    >
                </div>
            </div>

            <!-- Unit with Quick Chips -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Packaging Unit <span class="text-rose-600">*</span>
                </label>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <input
                        type="text"
                        name="unit"
                        id="prodUnitInput"
                        value="{{ old('unit', '1 Box') }}"
                        required
                        minlength="1"
                        maxlength="30"
                        placeholder="e.g. 1 Box, 1 Pkt, 1 Pcs"
                        class="w-full sm:w-44 px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800"
                    >
                    <div class="flex flex-wrap items-center gap-1.5">
                        @foreach (['1 Box', '1 Pkt', '1 Pcs', '1 Roll', '1 Bundle'] as $preset)
                            <button
                                type="button"
                                onclick="document.getElementById('prodUnitInput').value = '{{ $preset }}'"
                                class="px-2.5 py-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 border border-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition-all active:scale-95 cursor-pointer"
                            >
                                {{ $preset }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Pricing: MRP, Net Price (Auto), Discount % (Company Offer) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Actual Rate (MRP ₹) <span class="text-rose-600">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        min="1"
                        max="999999"
                        name="actual_rate"
                        id="prodActualRateInput"
                        value="{{ old('actual_rate') }}"
                        required
                        placeholder="e.g. 800.00"
                        class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-bold text-slate-700 shadow-sm"
                    >
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1 flex items-center justify-between">
                        <span>Selling Rate (Net ₹) <span class="text-rose-600">*</span></span>
                        <span class="text-[10px] text-slate-400 font-normal flex items-center gap-1"><i class="fa-solid fa-lock text-[9px]"></i> Auto</span>
                    </label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="999999"
                        name="net_rate"
                        id="prodNetRateInput"
                        value="{{ old('net_rate') }}"
                        required
                        readonly
                        tabindex="-1"
                        placeholder="Auto-calculated"
                        class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-xl font-extrabold text-rose-700 cursor-not-allowed select-none focus:outline-none"
                    >
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1 flex items-center justify-between">
                        <span>Discount %</span>
                        <span class="text-[10px] text-emerald-600 font-bold flex items-center gap-1"><i class="fa-solid fa-lock text-[9px]"></i> Company ({{ (int) ($shop->offer_percentage ?? 90) }}%)</span>
                    </label>
                    <input
                        type="number"
                        min="0"
                        max="100"
                        name="discount_percent"
                        id="prodDiscountInput"
                        value="{{ old('discount_percent', $shop->offer_percentage ?? 90) }}"
                        readonly
                        tabindex="-1"
                        class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-xl font-extrabold text-emerald-700 cursor-not-allowed select-none focus:outline-none"
                    >
                </div>
            </div>

            <!-- Image Upload with Live Preview & Remove Option -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Product Image (Optional)
                </label>
                <div class="flex items-center gap-4">
                    <div id="prodImagePreviewContainer" class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                        <i class="fa-solid fa-image text-2xl text-slate-300"></i>
                    </div>
                    <div class="flex-1 space-y-1.5">
                        <input
                            type="file"
                            name="image"
                            id="prodImageFileInput"
                            accept="image/*"
                            onchange="previewProductModalImage(this)"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 file:cursor-pointer cursor-pointer"
                        >
                        <p class="text-[11px] text-slate-400">Supports JPG, PNG, WEBP up to 2MB.</p>

                        <div id="prodRemoveImageWrap" class="hidden pt-1">
                            <label class="inline-flex items-center gap-2 cursor-pointer select-none text-rose-600 font-semibold text-[11px]">
                                <input type="checkbox" name="remove_image" id="prodRemoveImageCheck" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                <span>Remove current image</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Status -->
            <div class="pt-1">
                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="prodIsActiveInput"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer"
                    >
                    <span class="font-bold text-slate-700">Active (Visible for ordering on public price list)</span>
                </label>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5 sticky bottom-0 bg-white pb-1">
                <button
                    type="button"
                    onclick="closeProductModal()"
                    class="px-4 py-2.5 font-bold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold px-6 py-2.5 rounded-xl shadow-md transition-all font-heading active:scale-95 cursor-pointer"
                >
                    <i class="fa-solid fa-check"></i>
                    <span id="prodSubmitBtnText">Save Product</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ============================================================== -->
<!-- BULK UPLOAD MODAL (Excel / CSV)                                -->
<!-- ============================================================== -->
<div
    id="bulkUploadModal"
    class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden"
>
    <div class="bg-white rounded-3xl border border-emerald-100 shadow-2xl w-full max-w-lg max-h-[92vh] flex flex-col overflow-hidden my-6 animate-fadeIn">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 text-white p-5 flex items-center justify-between border-b border-white/10 shrink-0">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-400 flex items-center justify-center text-xl shadow-inner shrink-0">
                    <i class="fa-solid fa-file-excel"></i>
                </span>
                <div>
                    <h3 class="text-base sm:text-lg font-black font-heading text-white tracking-wide">
                        Bulk Upload Products
                    </h3>
                    <p class="text-[11px] text-slate-300">Import products from Sivakasi Excel price list (.xlsx) or CSV.</p>
                </div>
            </div>
            <button
                type="button"
                onclick="closeBulkUploadModal()"
                class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-colors text-sm cursor-pointer"
                title="Close Modal"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Form -->
        <form
            id="bulkUploadForm"
            method="POST"
            action="{{ route('admin.products.bulk_upload') }}"
            enctype="multipart/form-data"
            class="p-5 sm:p-6 space-y-4 text-xs overflow-y-auto flex-1"
        >
            @csrf

            <!-- Template Download Alert Box -->
            <div class="bg-emerald-50/80 border border-emerald-200 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
                <div class="space-y-0.5">
                    <div class="font-bold text-emerald-950 text-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-emerald-600"></i> Sample Excel Template
                    </div>
                    <p class="text-[11px] text-emerald-800">Standard 4-column Sivakasi format (S.No, Name, Per, Actual Rate).</p>
                </div>
                <a
                    href="{{ route('admin.products.sample_template') }}"
                    class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-3 py-2 rounded-xl transition-all shadow-sm shrink-0"
                >
                    <i class="fa-solid fa-download"></i>
                    <span>Download Sample</span>
                </a>
            </div>

            <!-- File Upload Drop Box -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">
                    Select Excel / CSV File <span class="text-rose-600">*</span>
                </label>
                <div class="border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-6 text-center transition-colors bg-slate-50 hover:bg-emerald-50/30 cursor-pointer relative group">
                    <input
                        type="file"
                        name="file"
                        id="bulkFileInput"
                        accept=".xlsx,.xls,.csv"
                        required
                        onchange="handleBulkFileSelect(this)"
                        class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10"
                    >
                    <div id="bulkFilePlaceholder" class="space-y-2 pointer-events-none">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-xl shadow-inner group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-700 text-xs">
                                Click or drag your Excel file here
                            </p>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                Supports .xlsx, .xls, .csv (Max 10MB)
                            </p>
                        </div>
                    </div>
                    <div id="bulkFileInfo" class="hidden space-y-1 pointer-events-none">
                        <i class="fa-solid fa-file-circle-check text-2xl text-emerald-600"></i>
                        <p id="bulkFileName" class="font-bold text-slate-800 text-xs break-all"></p>
                        <p id="bulkFileSize" class="text-[10px] text-slate-500"></p>
                    </div>
                </div>
            </div>

            <!-- Auto Calculation Notice -->
            <div class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-3 text-[11px] text-amber-900 flex items-start gap-2">
                <i class="fa-solid fa-wand-magic-sparkles text-amber-600 mt-0.5 shrink-0"></i>
                <div>
                    <strong>Auto Selling Rate Calculation:</strong> Selling rate (Net ₹) will be automatically calculated using your company offer rate (<strong>{{ (int) ($shop->offer_percentage ?? 90) }}%</strong>) for all imported products.
                </div>
            </div>

            <!-- Update Existing Checkbox -->
            <label class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition-colors">
                <input
                    type="checkbox"
                    name="update_existing"
                    value="1"
                    checked
                    class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer"
                >
                <div class="text-[11px]">
                    <span class="font-bold text-slate-800">Update existing products</span>
                    <p class="text-slate-500">If product name already exists in category, update its MRP and unit instead of duplicating.</p>
                </div>
            </label>

            <!-- Actions -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    onclick="closeBulkUploadModal()"
                    class="px-4 py-2.5 font-bold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold px-6 py-2.5 rounded-xl shadow-md transition-all font-heading active:scale-95 cursor-pointer"
                >
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Start Bulk Import</span>
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    const productModal = document.getElementById('productModal');
    const productForm = document.getElementById('productForm');
    const productMethod = document.getElementById('productMethod');
    const productModalTitle = document.getElementById('productModalTitle');
    const prodSubmitBtnText = document.getElementById('prodSubmitBtnText');

    const prodCategoryId = document.getElementById('prodCategoryId');
    const prodNameInput = document.getElementById('prodNameInput');
    const prodTamilNameInput = document.getElementById('prodTamilNameInput');
    const prodUnitInput = document.getElementById('prodUnitInput');
    const prodActualRateInput = document.getElementById('prodActualRateInput');
    const prodNetRateInput = document.getElementById('prodNetRateInput');
    const prodDiscountInput = document.getElementById('prodDiscountInput');
    const prodImageFileInput = document.getElementById('prodImageFileInput');
    const prodImagePreviewContainer = document.getElementById('prodImagePreviewContainer');
    const prodRemoveImageWrap = document.getElementById('prodRemoveImageWrap');
    const prodRemoveImageCheck = document.getElementById('prodRemoveImageCheck');
    const prodIsActiveInput = document.getElementById('prodIsActiveInput');

    const storeProductRoute = "{{ route('admin.products.store') }}";
    const companyOfferPercent = {{ (int) ($shop->offer_percentage ?? 90) }};

    function calculateProductDiscount() {
        const actual = parseFloat(prodActualRateInput.value) || 0;
        prodDiscountInput.value = companyOfferPercent;

        if (actual > 0) {
            const net = actual * (1 - (companyOfferPercent / 100));
            prodNetRateInput.value = net.toFixed(2);
        } else {
            prodNetRateInput.value = '';
        }
    }

    prodActualRateInput.addEventListener('input', calculateProductDiscount);

    function previewProductModalImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                prodImagePreviewContainer.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
            if (prodRemoveImageCheck) prodRemoveImageCheck.checked = false;
        }
    }

    window.openAddProductModal = function() {
        productModalTitle.innerText = 'Add New Product';
        prodSubmitBtnText.innerText = 'Save Product';
        productForm.action = storeProductRoute;
        productMethod.value = 'POST';

        prodCategoryId.value = '';
        prodNameInput.value = '';
        prodTamilNameInput.value = '';
        prodUnitInput.value = '1 Box';
        prodActualRateInput.value = '';
        prodNetRateInput.value = '';
        prodDiscountInput.value = companyOfferPercent;
        prodImageFileInput.value = '';
        prodImagePreviewContainer.innerHTML = '<i class="fa-solid fa-image text-2xl text-slate-300"></i>';
        prodRemoveImageWrap.classList.add('hidden');
        if (prodRemoveImageCheck) prodRemoveImageCheck.checked = false;
        prodIsActiveInput.checked = true;

        productModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => prodNameInput.focus(), 80);
    };

    window.openEditProductModal = function(prod) {
        productModalTitle.innerText = `Edit Product: ${prod.name}`;
        prodSubmitBtnText.innerText = 'Update Product';
        productForm.action = `/admin/products/${prod.id}`;
        productMethod.value = 'PUT';

        prodCategoryId.value = prod.category_id || '';
        prodNameInput.value = prod.name || '';
        prodTamilNameInput.value = prod.tamil_name || '';
        prodUnitInput.value = prod.unit || '1 Box';
        prodActualRateInput.value = prod.actual_rate ? parseFloat(prod.actual_rate).toFixed(2) : '';
        
        prodDiscountInput.value = companyOfferPercent;
        if (prod.actual_rate) {
            const actual = parseFloat(prod.actual_rate);
            const net = actual * (1 - (companyOfferPercent / 100));
            prodNetRateInput.value = net.toFixed(2);
        } else {
            prodNetRateInput.value = prod.net_rate ? parseFloat(prod.net_rate).toFixed(2) : '';
        }

        prodImageFileInput.value = '';
        if (prod.image_url) {
            prodImagePreviewContainer.innerHTML = `<img src="${prod.image_url}" class="w-full h-full object-cover">`;
            prodRemoveImageWrap.classList.remove('hidden');
        } else {
            prodImagePreviewContainer.innerHTML = '<i class="fa-solid fa-image text-2xl text-slate-300"></i>';
            prodRemoveImageWrap.classList.add('hidden');
        }
        if (prodRemoveImageCheck) prodRemoveImageCheck.checked = false;

        prodIsActiveInput.checked = Boolean(prod.is_active);

        productModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => prodNameInput.focus(), 80);
    };

    window.closeProductModal = function() {
        productModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    // Close on backdrop click
    productModal.addEventListener('click', function(e) {
        if (e.target === productModal) {
            closeProductModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!productModal.classList.contains('hidden')) closeProductModal();
            if (!bulkUploadModal.classList.contains('hidden')) closeBulkUploadModal();
        }
    });

    // Bulk Upload Modal Controls
    const bulkUploadModal = document.getElementById('bulkUploadModal');
    const bulkFileInput = document.getElementById('bulkFileInput');
    const bulkFilePlaceholder = document.getElementById('bulkFilePlaceholder');
    const bulkFileInfo = document.getElementById('bulkFileInfo');
    const bulkFileName = document.getElementById('bulkFileName');
    const bulkFileSize = document.getElementById('bulkFileSize');

    window.openBulkUploadModal = function() {
        if (bulkFileInput) bulkFileInput.value = '';
        if (bulkFilePlaceholder) bulkFilePlaceholder.classList.remove('hidden');
        if (bulkFileInfo) bulkFileInfo.classList.add('hidden');
        bulkUploadModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    window.closeBulkUploadModal = function() {
        bulkUploadModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    window.handleBulkFileSelect = function(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            bulkFileName.innerText = file.name;
            bulkFileSize.innerText = (file.size / 1024).toFixed(1) + ' KB';
            bulkFilePlaceholder.classList.add('hidden');
            bulkFileInfo.classList.remove('hidden');
        } else {
            bulkFilePlaceholder.classList.remove('hidden');
            bulkFileInfo.classList.add('hidden');
        }
    };

    bulkUploadModal.addEventListener('click', function(e) {
        if (e.target === bulkUploadModal) {
            closeBulkUploadModal();
        }
    });

    // Auto reopen if validation errors occurred on product form
    @if (isset($errors) && $errors->any() && old('_modal') === 'product')
        document.addEventListener('DOMContentLoaded', () => {
            productModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
    @endif
</script>
@endpush
@endsection
