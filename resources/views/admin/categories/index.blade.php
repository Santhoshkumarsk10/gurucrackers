@extends('layouts.app')

@section('title', 'Manage Categories - Admin')

@section('content')
<div class="space-y-5">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-tags"></i>
                </span>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">
                    Manage Product Categories
                </h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Organize cracker categories, festive icons, sort orders, and product visibility.</p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                onclick="openAddCategoryModal()"
                class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all font-heading cursor-pointer active:scale-95"
            >
                <i class="fa-solid fa-plus"></i>
                <span>Add Category</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs: Active vs Trash -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
        <a
            href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status !== 'trash' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
        >
            <i class="fa-solid fa-check-circle"></i>
            <span>Active Categories</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status !== 'trash' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $activeCount }}</span>
        </a>

        <a
            href="{{ route('admin.categories.index', ['status' => 'trash']) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'trash' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
        >
            <i class="fa-solid fa-trash-can"></i>
            <span>Trash / Archived</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'trash' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700' }}">{{ $trashedCount }}</span>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="flex items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.categories.index') }}" onsubmit="if (!this.search.value.trim()) { this.search.disabled = true; }" class="flex-1 max-w-sm relative">
            @if ($status === 'trash')
                <input type="hidden" name="status" value="trash">
            @endif
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search category name..."
                minlength="2"
                maxlength="50"
                oninput="this.value = this.value.replace(/[^a-zA-Z0-9\s.\-\/\u0B80-\u0BFF]/g, '').replace(/[\-\.\/]{2,}/g, '-').replace(/^[\-\.\/\s]+/, '')"
                class="w-full pl-8 pr-8 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium"
            >
            @if ($search)
                <a href="{{ route('admin.categories.index', array_filter(['status' => $status === 'trash' ? 'trash' : null])) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>

        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200">
            Total: {{ $categories->total() }} {{ $status === 'trash' ? 'Trashed' : 'Active' }} Categories
        </span>
    </div>

    <!-- Error Alerts -->
    @if (isset($errors) && $errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-800 p-4 rounded-xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Categories Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-center w-16">Order</th>
                        <th class="px-4 py-3 text-left">Category & Icon</th>
                        <th class="px-4 py-3 text-left hidden sm:table-cell">Slug</th>
                        <th class="px-4 py-3 text-center">Products</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3 text-center font-bold text-slate-500">
                                #{{ $category->sort_order }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-xl p-1.5 bg-slate-100 rounded-lg shrink-0">{{ $category->icon ?: '💥' }}</span>
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-sm font-heading">{{ $category->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-400 hidden sm:table-cell">
                                {{ $category->slug }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold bg-slate-100 text-slate-700 text-[11px]">
                                    {{ $category->products_count }} {{ $category->products_count === 1 ? 'item' : 'items' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($category->trashed())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold text-[10px]">
                                        <i class="fa-solid fa-trash-can text-[7px] text-rose-600"></i> Deleted
                                    </span>
                                @elseif ($category->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                        <i class="fa-solid fa-circle text-[7px] text-emerald-600"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
                                        <i class="fa-solid fa-circle text-[7px] text-slate-400"></i> Hidden
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if ($category->trashed())
                                        <form method="POST" action="{{ route('admin.categories.restore', $category->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white border border-emerald-200 rounded-xl transition-all font-bold text-xs cursor-pointer shadow-xs active:scale-95" title="Restore Category">
                                                <i class="fa-solid fa-rotate-left"></i>
                                                <span>Restore</span>
                                            </button>
                                        </form>

                                        @if ($category->products_count === 0)
                                            <form method="POST" action="{{ route('admin.categories.force_delete', $category->id) }}" data-confirm="Permanently delete category '{{ $category->name }}'? This action cannot be undone." data-confirm-title="Permanently Delete Category?" data-confirm-btn="Yes, Delete Permanently" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Permanently Delete">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <button
                                            type="button"
                                            onclick='openEditCategoryModal(@json($category))'
                                            class="p-1.5 text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors font-semibold cursor-pointer"
                                            title="Edit Category"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </button>

                                        @if ($category->products_count === 0)
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-confirm="Are you sure you want to delete category '{{ $category->name }}'? It can be restored from Trash later." data-confirm-title="Move Category to Trash?" data-confirm-btn="Yes, Move to Trash" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Delete Category">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                {{ $status === 'trash' ? 'Trash is empty. No deleted categories.' : 'No categories found matching your query.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div>
        {{ $categories->links() }}
    </div>

</div>

<!-- ======================================================= -->
<!-- CATEGORY ADD / EDIT FESTIVE MODAL                       -->
<!-- ======================================================= -->
<div
    id="categoryModal"
    class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm p-4 overflow-y-auto flex items-center justify-center transition-all"
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
>
    <div class="bg-white rounded-3xl border border-rose-100 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all my-8 animate-fadeIn">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-900 via-rose-950 to-slate-900 text-white p-5 flex items-center justify-between border-b border-white/10">
            <div class="flex items-center gap-3">
                <span id="catModalBadgeIcon" class="w-10 h-10 rounded-2xl bg-rose-500/20 border border-rose-400/30 text-amber-400 flex items-center justify-center text-xl shadow-inner">
                    💥
                </span>
                <div>
                    <h3 id="categoryModalTitle" class="text-base sm:text-lg font-black font-heading text-white tracking-wide">
                        Add New Category
                    </h3>
                    <p class="text-[11px] text-slate-300">Set category name, festive emoji & display order.</p>
                </div>
            </div>
            <button
                type="button"
                onclick="closeCategoryModal()"
                class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-colors text-sm cursor-pointer"
                title="Close Modal"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="categoryForm" method="POST" action="{{ route('admin.categories.store') }}" class="p-5 sm:p-6 space-y-4 text-xs">
            @csrf
            <input type="hidden" name="_method" id="categoryMethod" value="POST">
            <input type="hidden" name="_modal" value="category">
            <input type="hidden" id="categoryEditId" value="">

            <!-- Category Name -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Category Name <span class="text-rose-600">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    id="catNameInput"
                    value="{{ old('name') }}"
                    required
                    minlength="2"
                    maxlength="60"
                    placeholder="e.g. Special Crackers, Giant Rockets, Sound Bombs..."
                    class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-800 placeholder:text-slate-400 transition-colors"
                >
            </div>

            <!-- Icon / Emoji Picker -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Category Icon / Emoji
                </label>
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            name="icon"
                            id="catIconInput"
                            value="{{ old('icon', '💥') }}"
                            minlength="1"
                            maxlength="10"
                            class="w-20 text-center text-xl py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-bold"
                            oninput="updateCategoryBadgeEmoji(this.value)"
                        >
                        <span class="text-[11px] text-slate-400">Click a festive emoji below or type any custom emoji:</span>
                    </div>

                    <!-- Quick Emoji Presets -->
                    <div class="flex flex-wrap items-center gap-1.5 p-2 bg-slate-50 border border-slate-200 rounded-2xl">
                        @foreach (['💥', '🌸', '🌀', '✨', '💣', '🚀', '🏮', '⛲', '🎇', '🪔', '🕯️', '🌙', '👑', '🎯', '⚡', '🎁', '🏷️', '🧨'] as $emoji)
                            <button
                                type="button"
                                onclick="selectCategoryEmoji('{{ $emoji }}')"
                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-white hover:bg-rose-50 hover:border-rose-300 border border-slate-200 shadow-xs transition-all text-base active:scale-95 cursor-pointer"
                            >
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sort Order -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Display Sort Order
                </label>
                <input
                    type="number"
                    name="sort_order"
                    id="catSortOrderInput"
                    value="{{ old('sort_order', $nextOrder) }}"
                    min="0"
                    max="9999"
                    class="w-32 px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-bold text-slate-700"
                >
                <p class="text-[11px] text-slate-400 mt-1">Lower numbers appear first on the order price list.</p>
            </div>

            <!-- Active Status -->
            <div class="pt-2">
                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="catIsActiveInput"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500 cursor-pointer"
                    >
                    <span class="font-bold text-slate-700">Active (Visible on public order page)</span>
                </label>
            </div>

            <!-- Footer Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    onclick="closeCategoryModal()"
                    class="px-4 py-2.5 font-bold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold px-6 py-2.5 rounded-xl shadow-md transition-all font-heading active:scale-95 cursor-pointer"
                >
                    <i class="fa-solid fa-check"></i>
                    <span id="catSubmitBtnText">Save Category</span>
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    const categoryModal = document.getElementById('categoryModal');
    const categoryForm = document.getElementById('categoryForm');
    const categoryMethod = document.getElementById('categoryMethod');
    const categoryModalTitle = document.getElementById('categoryModalTitle');
    const catModalBadgeIcon = document.getElementById('catModalBadgeIcon');
    const catSubmitBtnText = document.getElementById('catSubmitBtnText');

    const catNameInput = document.getElementById('catNameInput');
    const catIconInput = document.getElementById('catIconInput');
    const catSortOrderInput = document.getElementById('catSortOrderInput');
    const catIsActiveInput = document.getElementById('catIsActiveInput');

    const defaultNextOrder = {{ $nextOrder }};
    const storeRoute = "{{ route('admin.categories.store') }}";

    function updateCategoryBadgeEmoji(emoji) {
        catModalBadgeIcon.innerText = emoji || '💥';
    }

    function selectCategoryEmoji(emoji) {
        catIconInput.value = emoji;
        updateCategoryBadgeEmoji(emoji);
    }

    window.openAddCategoryModal = function() {
        categoryModalTitle.innerText = 'Add New Category';
        catSubmitBtnText.innerText = 'Save Category';
        categoryForm.action = storeRoute;
        categoryMethod.value = 'POST';

        catNameInput.value = '';
        catIconInput.value = '💥';
        updateCategoryBadgeEmoji('💥');
        catSortOrderInput.value = defaultNextOrder;
        catIsActiveInput.checked = true;

        categoryModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => catNameInput.focus(), 80);
    };

    window.openEditCategoryModal = function(cat) {
        categoryModalTitle.innerText = `Edit Category: ${cat.name}`;
        catSubmitBtnText.innerText = 'Update Category';
        categoryForm.action = `/admin/categories/${cat.id}`;
        categoryMethod.value = 'PUT';

        catNameInput.value = cat.name || '';
        catIconInput.value = cat.icon || '💥';
        updateCategoryBadgeEmoji(cat.icon || '💥');
        catSortOrderInput.value = cat.sort_order ?? 0;
        catIsActiveInput.checked = Boolean(cat.is_active);

        categoryModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => catNameInput.focus(), 80);
    };

    window.closeCategoryModal = function() {
        categoryModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    // Close on backdrop click
    categoryModal.addEventListener('click', function(e) {
        if (e.target === categoryModal) {
            closeCategoryModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !categoryModal.classList.contains('hidden')) {
            closeCategoryModal();
        }
    });

    // Auto reopen if validation errors occurred on category form
    @if (isset($errors) && $errors->any() && old('_modal') === 'category')
        document.addEventListener('DOMContentLoaded', () => {
            categoryModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        });
    @endif
</script>
@endpush
@endsection
