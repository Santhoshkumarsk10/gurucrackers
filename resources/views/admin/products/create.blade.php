@extends('layouts.app')

@section('title', 'Add Product - Admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-fire-flame-curved"></i>
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">
                Add New Product
            </h1>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Products
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-800 p-4 rounded-xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- Category -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Category <span class="text-rose-600">*</span>
                </label>
                <select
                    name="category_id"
                    required
                    class="w-full px-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium"
                >
                    <option value="">Select Category...</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->icon }} {{ $cat->name }} @if($cat->tamil_name) ({{ $cat->tamil_name }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Product Names (English & Tamil) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        Product Name (English) <span class="text-rose-600">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        minlength="2"
                        maxlength="100"
                        placeholder="e.g. Gold Lakshmi"
                        class="w-full px-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium"
                    >
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">
                        தமிழ் பெயர் (Tamil Name)
                    </label>
                    <input
                        type="text"
                        name="tamil_name"
                        value="{{ old('tamil_name') }}"
                        minlength="2"
                        maxlength="100"
                        placeholder="e.g. தங்க லக்ஷ்மி"
                        class="w-full px-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium"
                    >
                </div>
            </div>

            <!-- Unit -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Packaging Unit <span class="text-rose-600">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        name="unit"
                        id="unitInput"
                        value="{{ old('unit', '1 Box') }}"
                        required
                        minlength="1"
                        maxlength="30"
                        placeholder="e.g. 1 Box, 1 Pkt, 1 Pcs"
                        class="w-48 px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium"
                    >
                    <div class="flex items-center gap-1">
                        @foreach (['1 Box', '1 Pkt', '1 Pcs'] as $preset)
                            <button
                                type="button"
                                onclick="document.getElementById('unitInput').value = '{{ $preset }}'"
                                class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold transition-colors"
                            >
                                {{ $preset }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Pricing: Actual Rate, Net Rate (Auto), Discount % (Company Offer) -->
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
                        id="actualRateInput"
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
                        id="netRateInput"
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
                        id="discountInput"
                        value="{{ old('discount_percent', $shop->offer_percentage ?? 90) }}"
                        readonly
                        tabindex="-1"
                        class="w-full px-3 py-2 text-xs bg-slate-100 border border-slate-200 rounded-xl font-extrabold text-emerald-700 cursor-not-allowed select-none focus:outline-none"
                    >
                </div>
            </div>

            <!-- Image Upload with Live Preview -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Product Image (Optional)
                </label>
                <div class="flex items-center gap-4">
                    <div id="imagePreviewContainer" class="w-16 h-16 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0">
                        <i class="fa-solid fa-image text-2xl text-slate-300"></i>
                    </div>
                    <div class="flex-1">
                        <input
                            type="file"
                            name="image"
                            id="imageFileInput"
                            accept="image/*"
                            onchange="previewImage(this)"
                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 file:cursor-pointer cursor-pointer"
                        >
                        <p class="text-[11px] text-slate-400 mt-1">Supports JPG, PNG, WEBP up to 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- Active Status -->
            <div class="pt-2">
                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500"
                    >
                    <span class="font-bold text-slate-700">Active (Visible for ordering on public price list)</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 font-semibold text-slate-500 hover:text-slate-700">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold px-5 py-2.5 rounded-xl shadow-md transition-all font-heading"
                >
                    Save Product
                </button>
            </div>

        </form>
    </div>

</div>

@push('scripts')
<script>
    // Live discount calculator using company offer percentage
    const actualInput = document.getElementById('actualRateInput');
    const netInput = document.getElementById('netRateInput');
    const discountInput = document.getElementById('discountInput');
    const companyOfferPercent = {{ (int) ($shop->offer_percentage ?? 90) }};

    function calculateDiscount() {
        const actual = parseFloat(actualInput.value) || 0;
        discountInput.value = companyOfferPercent;

        if (actual > 0) {
            const net = actual * (1 - (companyOfferPercent / 100));
            netInput.value = net.toFixed(2);
        } else {
            netInput.value = '';
        }
    }

    actualInput.addEventListener('input', calculateDiscount);
    // Initial trigger if old value present
    if (actualInput.value) {
        calculateDiscount();
    }

    // Live image preview
    function previewImage(input) {
        const container = document.getElementById('imagePreviewContainer');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                container.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
