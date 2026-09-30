@extends('layouts.app')

@section('title', ($shop->name ?? 'Guru Crackers') . ' - Sivakasi Direct Price List & Online Booking 2026')

@section('content')
    <div class="space-y-6 pb-24 sm:pb-20">

        {{-- ===================== HERO FESTIVE BANNER & CAROUSEL ===================== --}}
        @php
            $activeBanners = isset($banners) && $banners->isNotEmpty() ? $banners : collect();
            $totalHeroSlides = $activeBanners->count();
        @endphp

        @if ($totalHeroSlides > 0)
            <section
                class="relative overflow-hidden rounded-2xl sm:rounded-3xl shadow-xl shadow-rose-900/15 group select-none min-h-[160px] sm:min-h-[320px] md:min-h-[270px] lg:min-h-[250px] bg-slate-950"
                id="heroCarouselSection">
                <div class="relative w-full h-full min-h-[160px] sm:min-h-[320px] md:min-h-[270px] lg:min-h-[250px]"
                    id="bannerCarouselTrack">

                    {{-- SLIDE 0: Festive Store & Cart Overview Card (Hidden on both Desktop & Mobile as requested)
                <div class="carousel-slide absolute inset-0 w-full h-full transition-all duration-700 ease-out opacity-100 scale-100 z-10 bg-gradient-to-br from-rose-800 via-red-700 to-amber-700 text-white p-5 md:p-8 flex flex-col justify-center overflow-hidden"
                    data-index="0">
                    <div
                        class="absolute -top-12 -right-12 w-48 h-48 bg-amber-400/20 rounded-full blur-2xl pointer-events-none">
                    </div>
                    <div
                        class="absolute -bottom-10 -left-10 w-44 h-44 bg-rose-500/30 rounded-full blur-2xl pointer-events-none">
                    </div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-2.5 max-w-2xl">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-sm border border-white/20 text-amber-200 text-xs font-semibold tracking-wide shadow-sm">
                                <span class="animate-bounce">💥</span>
                                {{ $shop->offer ?? 'DIWALI 2026 FESTIVAL PRICE LIST | தீபாவளி பட்டாசு பட்டியல்' }}
                            </div>
                            <h1
                                class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight font-heading leading-tight">
                                {{ $shop->name ?? 'Sivakasi Direct Genuine Crackers' }}
                            </h1>
                            <p class="text-rose-100 text-sm md:text-base leading-relaxed">
                                Direct factory wholesale rates with up to <strong
                                    class="text-amber-300 font-bold">{{ $shop->offer_percentage ?? 90 }}% Discount</strong>.
                                Choose your favorite crackers by category with Tamil names & images, verify your live
                                savings, and book in 1-click!
                            </p>

                            <!-- Quick Highlights & Social Media Links -->
                            <div class="pt-2 flex flex-wrap items-center gap-2.5 sm:gap-3 text-xs font-medium text-rose-50">
                                <span
                                    class="flex items-center gap-1.5 bg-black/20 px-2.5 py-1 rounded-md border border-white/10">
                                    <i class="fa-solid fa-certificate text-amber-300"></i> 100% Green Crackers
                                </span>
                                <span
                                    class="flex items-center gap-1.5 bg-black/20 px-2.5 py-1 rounded-md border border-white/10">
                                    <i class="fa-solid fa-truck-fast text-amber-300"></i> Direct Transport Dispatch
                                </span>
                                <span
                                    class="flex items-center gap-1.5 bg-black/20 px-2.5 py-1 rounded-md border border-white/10">
                                    <i class="fa-solid fa-tags text-amber-300"></i> {{ $categories->count() }} Categories
                                </span>
                                @if (!empty($shop->whatsapp_phone))
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $shop->whatsapp_phone) }}"
                                        target="_blank" rel="noopener noreferrer"
                                        class="flex items-center gap-1.5 bg-emerald-600/90 hover:bg-emerald-600 px-2.5 py-1 rounded-md border border-white/15 text-white font-bold transition-all shadow-sm"
                                        title="Chat on WhatsApp">
                                        <i class="fa-brands fa-whatsapp text-emerald-200 text-sm"></i> WhatsApp Us
                                    </a>
                                @endif
                                @if (!empty($shop->facebook_url))
                                    <a href="{{ $shop->facebook_url }}" target="_blank" rel="noopener noreferrer"
                                        class="flex items-center gap-1 bg-[#1877F2]/80 hover:bg-[#1877F2] px-2 py-1 rounded-md border border-white/15 text-white font-bold transition-all shadow-sm"
                                        title="Visit Facebook Page">
                                        <i class="fa-brands fa-facebook-f text-xs"></i>
                                        <span class="hidden sm:inline">Facebook</span>
                                    </a>
                                @endif
                                @if (!empty($shop->instagram_url))
                                    <a href="{{ $shop->instagram_url }}" target="_blank" rel="noopener noreferrer"
                                        class="flex items-center gap-1 bg-gradient-to-r from-[#f09433] via-[#dc2743] to-[#bc1888] hover:opacity-95 px-2 py-1 rounded-md border border-white/15 text-white font-bold transition-all shadow-sm"
                                        title="Follow on Instagram">
                                        <i class="fa-brands fa-instagram text-xs"></i>
                                        <span class="hidden sm:inline">Instagram</span>
                                    </a>
                                @endif
                                @if (!empty($shop->youtube_url))
                                    <a href="{{ $shop->youtube_url }}" target="_blank" rel="noopener noreferrer"
                                        class="flex items-center gap-1 bg-[#FF0000]/85 hover:bg-[#FF0000] px-2 py-1 rounded-md border border-white/15 text-white font-bold transition-all shadow-sm"
                                        title="Watch on YouTube">
                                        <i class="fa-brands fa-youtube text-xs"></i>
                                        <span class="hidden sm:inline">YouTube</span>
                                    </a>
                                @endif
                                @if (!empty($shop->maps_url))
                                    <a href="{{ $shop->maps_url }}" target="_blank" rel="noopener noreferrer"
                                        class="flex items-center gap-1 bg-amber-600/80 hover:bg-amber-600 px-2 py-1 rounded-md border border-white/15 text-white font-bold transition-all shadow-sm"
                                        title="View Factory Location on Google Maps">
                                        <i class="fa-solid fa-map-location-dot text-xs text-amber-200"></i>
                                        <span class="hidden sm:inline">Maps</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Action Box -->
                        <div
                            class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-4 flex flex-col items-center justify-center text-center shrink-0 min-w-[220px]">
                            <div class="text-xs text-rose-200 uppercase tracking-wider font-semibold">Live Cart Status</div>
                            <div class="text-2xl font-extrabold text-amber-300 my-1 font-heading" id="heroTotalDisplay">
                                ₹0.00</div>
                            <div class="text-[11px] text-white/80" id="heroItemsCount">0 items selected</div>
                            <button type="button" onclick="scrollToCheckout()"
                                class="mt-3 w-full bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-500 hover:to-amber-600 text-slate-900 text-xs font-bold py-2 px-3 rounded-lg shadow-md transition-all flex items-center justify-center gap-1.5 font-heading cursor-pointer">
                                <i class="fa-solid fa-basket-shopping"></i> Checkout Now
                            </button>
                        </div>
                    </div>
                </div>
                --}}

                    <!-- SLIDES: Promotional Banners -->
                    @foreach ($activeBanners as $idx => $banner)
                        <div class="carousel-slide absolute inset-0 w-full h-full transition-all duration-700 ease-out {{ $idx === 0 ? 'opacity-100 scale-100 z-10' : 'opacity-0 scale-105 pointer-events-none z-0' }} bg-slate-950 overflow-hidden"
                            data-index="{{ $idx }}">
                            <a href="{{ $banner->safe_link }}"
                                class="block w-full h-full cursor-pointer" title="{{ $banner->title ?: 'Diwali Offer' }}">
                                <picture class="block w-full h-full">
                                    @if (!empty($banner->mobile_image))
                                        <source media="(max-width: 640px)" srcset="{{ $banner->mobile_image_url }}">
                                    @endif
                                    <img src="{{ $banner->image_url }}"
                                        alt="{{ $banner->title ?: 'Festival Cracker Banner' }}"
                                        class="w-full h-full object-cover" loading="lazy">
                                </picture>
                            </a>

                            @if (!empty($banner->title))
                                <div
                                    class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent p-4 sm:p-6 text-white pointer-events-none">
                                    <h2
                                        class="text-xs sm:text-base md:text-lg font-black font-heading drop-shadow-md text-amber-300">
                                        {{ $banner->title }}
                                    </h2>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Carousel Controls (Show only if total slides > 1) -->
                @if ($totalHeroSlides > 1)
                    <!-- Dots Indicators -->
                    <div
                        class="absolute bottom-3 left-1/2 -translate-x-1/2 z-30 flex items-center gap-1.5 bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-full">
                        @for ($i = 0; $i < $totalHeroSlides; $i++)
                            <button type="button" onclick="goToBannerSlide({{ $i }})"
                                class="banner-dot rounded-full transition-all duration-300 cursor-pointer {{ $i === 0 ? 'bg-amber-400 w-5 h-2' : 'bg-white/60 hover:bg-white w-2 h-2' }}"
                                aria-label="Go to slide {{ $i + 1 }}"></button>
                        @endfor
                    </div>
                @endif
            </section>
        @endif

        {{-- ===================== VALIDATION ERRORS ===================== --}}
        @if (isset($errors) && $errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-800 p-4 rounded-xl shadow-sm space-y-2">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                    <span>Please fix the following issues to place your order:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===================== STICKY SEARCH & CATEGORY BAR ===================== --}}
        <div
            class="sticky top-[61px] z-30 bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200/80 shadow-md p-2.5 sm:p-3 space-y-2 transition-all">
            <!-- Search & Filter Row -->
            <div class="flex items-center gap-2">
                <!-- Search Bar (Supports English & Tamil) -->
                <div class="relative flex-1 min-w-0">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs sm:text-sm"></i>
                    <input type="text" id="searchInput" placeholder="Search crackers (e.g. Bomb, லக்ஷ்மி, Flower Pot)..."
                        minlength="2" maxlength="60"
                        class="w-full pl-9 pr-8 py-2 text-xs sm:text-sm bg-slate-50 hover:bg-slate-100/80 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all placeholder:text-slate-400 font-medium">
                    <button type="button" id="clearSearchBtn" onclick="clearSearch()"
                        class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1"
                        title="Clear search">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>

                <!-- Filter Button (Opens Bottom Sheet Drawer) -->
                <button type="button" onclick="openCategoryFilterDrawer()" id="categoryFilterDrawerBtn"
                    class="px-3 py-2 text-xs font-bold bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 border border-slate-200 rounded-xl transition-all flex items-center gap-1.5 shrink-0 cursor-pointer shadow-xs active:scale-95"
                    title="Filter by Category">
                    <i class="fa-solid fa-sliders text-xs text-rose-600"></i>
                    <span class="font-heading">Filter</span>
                    <span id="activeFilterDot" class="hidden w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                </button>

                <!-- Desktop Controls: View Mode & Reset -->
                <div class="hidden md:flex items-center gap-2 shrink-0">
                    <div
                        class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200 text-[11px] font-bold shrink-0">
                        <button type="button" id="viewModeAllBtn" onclick="setViewMode('all')"
                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer flex items-center gap-1 bg-white text-slate-900 shadow-sm"
                            title="Show all categories stacked">
                            <span>View All</span>
                        </button>
                        <button type="button" id="viewModeSingleBtn" onclick="setViewMode('single')"
                            class="px-2.5 py-1 rounded-lg transition-all cursor-pointer flex items-center gap-1 text-slate-500 hover:text-slate-900"
                            title="Show one selected category at a time">
                            <span>Single</span>
                        </button>
                    </div>

                    <button type="button" onclick="clearAllQuantities()"
                        class="px-2.5 py-2 text-xs font-semibold text-slate-600 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 border border-slate-200 rounded-xl transition-colors flex items-center gap-1 shrink-0 cursor-pointer"
                        title="Reset all quantities to 0">
                        <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </div>

            <!-- Active Selected Category Display (Shows outside ONLY when a filter is applied) -->
            <div id="activeCategoryPillContainer"
                class="hidden items-center justify-between bg-rose-50 border border-rose-200/90 rounded-xl px-2.5 py-1.5 text-xs text-rose-900 transition-all">
                <div class="flex items-center gap-1.5 min-w-0">
                    <span class="text-[10px] uppercase font-bold text-rose-500 tracking-wider shrink-0">Filter:</span>
                    <span id="activeCatIcon" class="text-sm shrink-0">💥</span>
                    <span id="activeCatName" class="font-extrabold text-xs text-rose-900 truncate"></span>
                    <span id="activeCatTamil" class="text-[11px] text-rose-700 font-semibold truncate"></span>
                    <span id="activeCatCount"
                        class="text-[10px] text-rose-600 font-bold bg-white/80 border border-rose-200 px-1.5 py-0.2 rounded-md shrink-0"></span>
                </div>
                <button type="button" id="clearActiveCatBtn" onclick="selectCategory('all')"
                    class="text-rose-500 hover:text-rose-800 hover:bg-rose-100 p-1 rounded-md transition-colors cursor-pointer shrink-0 ml-1.5"
                    title="Clear filter (Show All Categories)">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Product Search Match Status -->
        <div id="searchMatchStatus"
            class="hidden text-xs font-semibold text-slate-500 px-1 flex items-center justify-between">
            <span>Showing results for "<span id="searchKeyword" class="text-rose-600 font-bold"></span>":</span>
            <span id="matchCountText" class="text-slate-700">0 found</span>
        </div>

        {{-- ===================== MAIN ORDER FORM ===================== --}}
        <form method="POST" action="{{ route('order.store') }}" id="orderForm" novalidate>
            @csrf

            {{-- ===================== PRODUCT CATALOG LISTING ===================== --}}
            <div class="space-y-6" id="productCatalog">
                @foreach ($categories as $category)
                    <div class="category-section bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden scroll-mt-36"
                        id="cat-{{ $category->slug }}" data-category-slug="cat-{{ $category->slug }}">

                        <!-- Category Header (Accordion Trigger Button) -->
                        <button type="button" onclick="toggleCategoryAccordion('cat-{{ $category->slug }}')"
                            class="w-full text-left bg-gradient-to-r from-rose-700 via-rose-800 to-red-900 hover:from-rose-800 hover:to-red-950 text-white px-4 py-3 flex items-center justify-between gap-3 select-none transition-all cursor-pointer group active:brightness-95"
                            aria-expanded="true" id="heading-cat-{{ $category->slug }}">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span
                                    class="text-xl p-1.5 bg-white/15 rounded-xl group-hover:scale-110 transition-transform shrink-0">
                                    {{ $category->icon ?: '💥' }}
                                </span>
                                <div class="min-w-0">
                                    <h2
                                        class="font-extrabold text-base sm:text-lg font-heading tracking-wide leading-snug">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="truncate">{{ $category->name }}</span>
                                            @if ($category->tamil_name)
                                                <span
                                                    class="hidden sm:inline text-xs sm:text-sm text-rose-200 font-medium">({{ $category->tamil_name }})</span>
                                            @endif
                                        </div>
                                        @if ($category->tamil_name)
                                            <div class="sm:hidden text-xs text-rose-200 font-medium leading-tight mt-0.5">
                                                ({{ $category->tamil_name }})
                                            </div>
                                        @endif
                                    </h2>
                                    <p class="text-[11px] text-rose-200 font-medium mt-0.5 sm:mt-0">
                                        {{ $category->products->count() }}
                                        {{ $category->products->count() === 1 ? 'variety' : 'varieties' }} available
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span
                                    class="category-items-count-badge hidden text-[11px] font-bold bg-amber-400 text-slate-900 px-2.5 py-0.5 rounded-full shadow-sm">
                                    0 in cart
                                </span>
                                <span
                                    class="w-7 h-7 rounded-lg bg-white/15 flex items-center justify-center text-xs text-white/90 group-hover:bg-white/25 transition-all">
                                    <i class="fa-solid fa-chevron-down category-chevron transform transition-transform duration-300"
                                        id="chevron-cat-{{ $category->slug }}"></i>
                                </span>
                            </div>
                        </button>

                        <!-- Products Table / List (Collapsible Accordion Body) -->
                        <div id="body-cat-{{ $category->slug }}"
                            class="category-accordion-body divide-y divide-slate-100 transition-all duration-300">
                            @foreach ($category->products as $product)
                                <div class="product-row p-3 sm:p-4 hover:bg-rose-50/30 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                    data-product-name="{{ strtolower($product->name) }}"
                                    data-tamil-name="{{ strtolower($product->tamil_name ?? '') }}"
                                    data-category-name="{{ strtolower($category->name) }}"
                                    data-category-tamil="{{ strtolower($category->tamil_name ?? '') }}"
                                    id="product-row-{{ $product->id }}">

                                    <!-- Left: Thumbnail + Product Details -->
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <!-- Thumbnail with Zoom Option -->
                                        <div
                                            class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center shrink-0 shadow-sm relative group">
                                            @if ($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                    class="w-full h-full object-cover cursor-pointer group-hover:scale-105 transition-transform"
                                                    onclick="openImageModal('{{ $product->image_url }}', '{{ addslashes($product->name) }}', '{{ addslashes($product->tamil_name ?? '') }}')"
                                                    loading="lazy">
                                                <div
                                                    class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs pointer-events-none">
                                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                </div>
                                            @else
                                                <span
                                                    class="text-xl sm:text-2xl select-none">{{ $category->icon ?: '💥' }}</span>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                                <h3
                                                    class="font-bold text-slate-800 text-xs sm:text-sm md:text-base leading-snug">
                                                    {{ $product->name }}
                                                </h3>
                                                @if ($product->tamil_name)
                                                    <span
                                                        class="hidden sm:inline text-xs sm:text-sm font-semibold text-rose-700">
                                                        ({{ $product->tamil_name }})
                                                    </span>
                                                @endif
                                                <span
                                                    class="inline-flex items-center text-[10px] sm:text-[11px] font-semibold text-slate-500 bg-slate-100 border border-slate-200 px-1.5 sm:px-2 py-0.5 rounded-md shrink-0">
                                                    {{ $product->unit }}
                                                </span>
                                            </div>

                                            @if ($product->tamil_name)
                                                <div
                                                    class="sm:hidden text-[11px] font-semibold text-rose-700 leading-tight mt-0.5">
                                                    ({{ $product->tamil_name }})
                                                </div>
                                            @endif

                                            <!-- Pricing display -->
                                            <div class="mt-1 flex items-baseline gap-1.5 sm:gap-2 flex-wrap text-xs">
                                                @if ($product->discount_percent > 0)
                                                    <span
                                                        class="line-through text-slate-400 font-medium text-[11px] sm:text-xs">₹{{ number_format($product->actual_rate, 2) }}</span>
                                                    <span
                                                        class="inline-flex items-center px-1 sm:px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold text-[9px] sm:text-[10px] tracking-tight shrink-0">
                                                        {{ $product->discount_percent }}% OFF
                                                    </span>
                                                    <span
                                                        class="text-rose-700 font-extrabold text-sm sm:text-base">₹{{ number_format($product->net_rate, 2) }}</span>
                                                @else
                                                    <span
                                                        class="text-rose-700 font-extrabold text-sm sm:text-base">₹{{ number_format($product->net_rate, 2) }}</span>
                                                @endif
                                                <span class="text-[10px] sm:text-[11px] text-slate-400">/
                                                    {{ $product->unit }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Quantity Stepper & Subtotal -->
                                    <div
                                        class="flex items-center justify-between sm:justify-end gap-2 sm:gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 w-full sm:w-auto">
                                        <!-- Line Subtotal Display -->
                                        <div class="text-left sm:text-right min-w-[70px]">
                                            <div
                                                class="text-[9px] sm:text-[10px] uppercase tracking-wider text-slate-400 font-semibold">
                                                Total</div>
                                            <div class="line-total-display text-xs sm:text-sm font-bold text-slate-700"
                                                id="line-total-{{ $product->id }}">
                                                ₹0.00
                                            </div>
                                        </div>

                                        <!-- Touch-Friendly Quantity Stepper -->
                                        <div
                                            class="flex items-center border-2 border-slate-200 rounded-xl bg-slate-50 p-0.5 focus-within:border-rose-500 transition-colors shadow-inner shrink-0">
                                            <button type="button" onclick="stepQuantity({{ $product->id }}, -1)"
                                                class="w-8 h-8 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg text-slate-600 hover:text-rose-600 hover:bg-white active:scale-95 transition-all text-xs sm:text-sm font-bold focus:outline-none"
                                                title="Decrease quantity">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>

                                            <input type="number" min="0" max="20"
                                                value="{{ old('products.' . $product->id . '.qty', 0) }}"
                                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                data-tamil="{{ $product->tamil_name ?? '' }}"
                                                data-category="{{ $category->name }}"
                                                data-category-id="cat-{{ $category->slug }}"
                                                data-unit="{{ $product->unit }}"
                                                data-actual-price="{{ $product->actual_rate }}"
                                                data-price="{{ $product->net_rate }}"
                                                data-discount="{{ $product->discount_percent }}"
                                                name="products[{{ $product->id }}][qty]"
                                                id="qty-input-{{ $product->id }}"
                                                class="qty-input w-10 sm:w-12 bg-transparent text-center font-extrabold text-xs sm:text-sm text-slate-800 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                placeholder="0">

                                            <button type="button" onclick="stepQuantity({{ $product->id }}, 1)"
                                                class="w-8 h-8 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg text-slate-600 hover:text-rose-600 hover:bg-white active:scale-95 transition-all text-xs sm:text-sm font-bold focus:outline-none"
                                                title="Increase quantity">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- No Results Fallback -->
            <div id="noResultsBox"
                class="hidden bg-white rounded-2xl border border-slate-200 p-8 text-center space-y-3 shadow-sm">
                <div class="text-4xl text-slate-300">🔍</div>
                <h3 class="text-base font-bold text-slate-700 font-heading">No crackers matched your search</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Try searching for other popular items in English or
                    Tamil like "Lakshmi", "லக்ஷ்மி", "Flower Pot", "பூந்தொட்டி", "Bomb", "பாம்", or "Sparklers".</p>
                <button type="button" onclick="clearSearch()"
                    class="text-xs font-semibold text-rose-600 hover:text-rose-700 underline">
                    Show all products
                </button>
            </div>

            {{-- ===================== CHECKOUT & DELIVERY SECTION ===================== --}}
            <div class="mt-10 pt-4" id="checkoutSection">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Column: Order Summary Review -->
                    <div class="lg:col-span-6 space-y-4">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-5">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">
                                        <i class="fa-solid fa-receipt"></i>
                                    </span>
                                    <h2 class="font-extrabold text-base sm:text-lg text-slate-900 font-heading">
                                        Selected Items Review
                                    </h2>
                                </div>
                                <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                                    <span class="cart-items-badge">0</span> Varieties
                                </span>
                            </div>

                            <!-- Live Selected Items Container -->
                            <div id="selectedItemsContainer"
                                class="max-h-[360px] overflow-y-auto space-y-2 pr-1 divide-y divide-slate-100">
                                <div id="emptyCartNotice" class="py-8 text-center text-slate-400 space-y-2">
                                    <i class="fa-solid fa-cart-arrow-down text-3xl text-slate-300"></i>
                                    <p class="text-xs">No items added yet. Select products above to place your order.</p>
                                </div>
                            </div>

                            <!-- Price Breakdown Totals -->
                            <div class="border-t border-slate-100 pt-3 space-y-2 text-xs">
                                <div class="flex justify-between text-slate-500">
                                    <span>Actual Value (MRP):</span>
                                    <span class="font-semibold text-slate-700" id="summaryActualTotal">₹0.00</span>
                                </div>
                                <div
                                    class="flex justify-between text-emerald-700 font-semibold bg-emerald-50/80 px-2.5 py-1.5 rounded-lg">
                                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-tags"></i> Festival
                                        Discount Savings:</span>
                                    <span id="summarySavingsTotal">- ₹0.00</span>
                                </div>
                                <div
                                    class="flex justify-between items-baseline pt-2 border-t border-dashed border-slate-200">
                                    <span class="text-sm sm:text-base font-extrabold text-slate-900 font-heading">Net
                                        Payable Amount:</span>
                                    <span class="text-xl sm:text-2xl font-black text-rose-700 font-heading"
                                        id="summaryPayableTotal">₹0.00</span>
                                </div>
                            </div>

                            <!-- Sivakasi Direct Guarantee Info -->
                            <div
                                class="bg-amber-50/70 border border-amber-200/80 rounded-xl p-3 text-[11px] text-amber-900 space-y-1">
                                <div class="font-bold flex items-center gap-1.5">
                                    <i class="fa-solid fa-shield-halved text-amber-600"></i> Direct From Sivakasi
                                </div>
                                <p class="leading-relaxed text-amber-800">
                                    We dispatch top brand fireworks directly from Sivakasi factory. Our representative will
                                    contact you for transport hub delivery confirmation before dispatch.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Delivery Details Form -->
                    <div class="lg:col-span-6 space-y-4">
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-5">
                            <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
                                <span
                                    class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                                    <i class="fa-solid fa-truck"></i>
                                </span>
                                <div>
                                    <h2
                                        class="font-extrabold text-base sm:text-lg text-slate-900 font-heading leading-tight">
                                        Delivery & Contact Details
                                    </h2>
                                    <p class="text-[11px] text-slate-500">Please provide accurate contact details for
                                        dispatch confirmation</p>
                                </div>
                            </div>

                            <div class="space-y-3.5 text-xs">
                                <!-- Full Name -->
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1" for="nameInput">
                                        Customer Full Name <span class="text-rose-600">*</span>
                                    </label>
                                    <div class="relative">
                                        <i
                                            class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                        <input type="text" name="name" id="nameInput" value="{{ old('name') }}"
                                            required minlength="3" maxlength="60" placeholder="e.g. Ramesh Kumar"
                                            class="w-full pl-8 pr-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all font-medium placeholder:text-slate-400">
                                    </div>
                                    <p id="err-name"
                                        class="field-error-msg hidden text-red-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                        <span></span>
                                    </p>
                                </div>

                                <!-- Phone 1 & Phone 2 -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label
                                            class="block font-bold text-slate-700 mb-1 flex items-center justify-between"
                                            for="phone1Input">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                                                <span>WhatsApp Mobile No <span class="text-rose-600">*</span></span>
                                            </span>
                                            <span
                                                class="text-[10px] text-emerald-700 font-bold bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded-md">
                                                Active WhatsApp
                                            </span>
                                        </label>
                                        <div class="relative flex rounded-xl border border-slate-200 bg-slate-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-rose-500 focus-within:border-rose-500 transition-all overflow-hidden"
                                            id="phone1Wrapper">
                                            <span
                                                class="inline-flex items-center px-2.5 text-slate-700 font-extrabold text-xs bg-slate-100 border-r border-slate-200 select-none shrink-0">
                                                🇮🇳 +91
                                            </span>
                                            <input type="tel" name="phone1" id="phone1Input"
                                                value="{{ old('phone1') }}" required placeholder="10-digit mobile number"
                                                minlength="10" maxlength="10" inputmode="numeric"
                                                pattern="[6-9][0-9]{9}"
                                                title="10-digit mobile number starting with 6, 7, 8, or 9"
                                                class="w-full px-2.5 py-2.5 text-xs bg-transparent border-none focus:outline-none font-medium placeholder:text-slate-400">
                                        </div>
                                        <p class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-info text-emerald-600 text-[10px]"></i>
                                            <span>Invoice & parcel updates will be sent to this WhatsApp.</span>
                                        </p>
                                        <p id="err-phone1"
                                            class="field-error-msg hidden text-red-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                            <span></span>
                                        </p>
                                    </div>
                                    <div>
                                        <label
                                            class="block font-bold text-slate-700 mb-1 flex items-center justify-between"
                                            for="phone2Input">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fa-solid fa-phone text-slate-500 text-xs"></i>
                                                <span>Alternate Mobile No <span
                                                        class="text-slate-400 font-normal">(Optional)</span></span>
                                            </span>
                                            <span class="text-[10px] text-slate-500 font-normal">For Voice Call</span>
                                        </label>
                                        <div class="relative flex rounded-xl border border-slate-200 bg-slate-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-rose-500 focus-within:border-rose-500 transition-all overflow-hidden"
                                            id="phone2Wrapper">
                                            <span
                                                class="inline-flex items-center px-2.5 text-slate-500 font-bold text-xs bg-slate-100 border-r border-slate-200 select-none shrink-0">
                                                +91
                                            </span>
                                            <input type="tel" name="phone2" id="phone2Input"
                                                value="{{ old('phone2') }}" placeholder="Secondary 10 digits"
                                                minlength="10" maxlength="10" inputmode="numeric"
                                                pattern="[6-9][0-9]{9}"
                                                title="10-digit mobile number starting with 6, 7, 8, or 9"
                                                class="w-full px-2.5 py-2.5 text-xs bg-transparent border-none focus:outline-none font-medium placeholder:text-slate-400">
                                        </div>
                                        <p class="text-[10px] text-slate-500 mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-phone-volume text-slate-400 text-[10px]"></i>
                                            <span>To call if your WhatsApp number is unreachable.</span>
                                        </p>
                                        <p id="err-phone2"
                                            class="field-error-msg hidden text-red-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                            <span></span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Delivery Address -->
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1" for="deliveryAddressInput">
                                        Delivery Address / Nearest Transport Hub <span class="text-rose-600">*</span>
                                    </label>
                                    <div class="relative">
                                        <i
                                            class="fa-solid fa-location-dot absolute left-3 top-3 text-slate-400 text-xs pointer-events-none"></i>
                                        <textarea name="delivery_address" id="deliveryAddressInput" required rows="2" minlength="5" maxlength="250"
                                            placeholder="Door No, Street Name, Area / Landmark..."
                                            class="w-full pl-8 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all font-medium placeholder:text-slate-400">{{ old('delivery_address') }}</textarea>
                                    </div>
                                    <p id="err-delivery_address"
                                        class="field-error-msg hidden text-red-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                        <span></span>
                                    </p>
                                </div>

                                <!-- City, State & Pincode -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1" for="cityInput">
                                            City / Town <span class="text-rose-600">*</span>
                                        </label>
                                        <div class="relative">
                                            <i
                                                class="fa-solid fa-city absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                            <input type="text" name="city" id="cityInput"
                                                value="{{ old('city') }}" required minlength="2" maxlength="50"
                                                placeholder="e.g. Madurai / Chennai"
                                                class="w-full pl-8 pr-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all font-medium placeholder:text-slate-400">
                                        </div>
                                        <p id="err-city"
                                            class="field-error-msg hidden text-red-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                            <span></span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1" for="stateInput">
                                            State <span class="text-rose-600">*</span>
                                        </label>
                                        <div class="relative">
                                            <i
                                                class="fa-solid fa-map-location-dot absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                            <select name="state" id="stateInput" required
                                                class="w-full pl-8 pr-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all font-medium">
                                                <option value="Tamil Nadu" {{ old('state', 'Tamil Nadu') === 'Tamil Nadu' ? 'selected' : '' }}>Tamil Nadu</option>
                                                <option value="Pondicherry" {{ old('state') === 'Pondicherry' ? 'selected' : '' }}>Pondicherry</option>
                                                <option value="Kerala" {{ old('state') === 'Kerala' ? 'selected' : '' }}>Kerala</option>
                                                <option value="Karnataka" {{ old('state') === 'Karnataka' ? 'selected' : '' }}>Karnataka</option>
                                                <option value="Andhra Pradesh" {{ old('state') === 'Andhra Pradesh' ? 'selected' : '' }}>Andhra Pradesh</option>
                                                <option value="Telangana" {{ old('state') === 'Telangana' ? 'selected' : '' }}>Telangana</option>
                                                <option value="Maharashtra" {{ old('state') === 'Maharashtra' ? 'selected' : '' }}>Maharashtra</option>
                                                <option value="Other" {{ old('state') === 'Other' ? 'selected' : '' }}>Other State</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1" for="pincodeInput">
                                            Pincode <span class="text-rose-600">*</span>
                                        </label>
                                        <div class="relative">
                                            <i
                                                class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                            <input type="text" name="pincode" id="pincodeInput"
                                                value="{{ old('pincode') }}" required placeholder="6-digit pincode"
                                                pattern="[0-9]{6}" minlength="6" maxlength="6" inputmode="numeric"
                                                class="w-full pl-8 pr-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all font-medium placeholder:text-slate-400">
                                        </div>
                                        <p id="err-pincode"
                                            class="field-error-msg hidden text-red-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                            <span></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Order Button -->
                            <div class="pt-2">
                                <button type="submit" id="submitOrderBtn"
                                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 via-rose-700 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold text-sm font-heading shadow-lg shadow-rose-600/20 active:scale-[0.99] transition-all flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span>🎆 Submit Order</span>
                                    <span class="bg-white/20 px-2 py-0.5 rounded text-xs tracking-wide"
                                        id="btnTotalText">₹0.00</span>
                                    <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                </button>
                                <p class="text-[11px] text-slate-400 text-center mt-2">
                                    🔒 Safe booking. No immediate online payment required. Pay upon transport confirmation.
                                </p>
                                <div class="mt-3 pt-3 border-t border-slate-100 flex flex-col items-center gap-1.5">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1">
                                        <i class="fa-solid fa-shield-halved text-emerald-600"></i> Payment Accepted via UPI
                                    </span>
                                    @include('partials.payment-accepted-badges')
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    {{-- ===================== IMAGE LIGHTBOX MODAL ===================== --}}
    <div id="imageModal"
        class="hidden fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
        onclick="closeImageModal()">
        <div class="bg-white rounded-2xl max-w-sm sm:max-w-md w-full overflow-hidden shadow-2xl relative"
            onclick="event.stopPropagation()">
            <button type="button" onclick="closeImageModal()"
                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/60 hover:bg-black text-white flex items-center justify-center transition-colors z-10"
                title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="w-full h-64 sm:h-72 bg-slate-900 flex items-center justify-center overflow-hidden">
                <img id="modalImg" src="" alt="" class="max-w-full max-h-full object-contain">
            </div>
            <div class="p-4 bg-white">
                <h3 id="modalTitle" class="font-extrabold text-slate-900 text-base font-heading"></h3>
                <p id="modalTamilTitle" class="text-xs text-rose-700 font-semibold mt-0.5"></p>
            </div>
        </div>
    </div>

    {{-- ===================== CATEGORY FILTER BOTTOM SHEET (MOBILE) & MODAL (DESKTOP) ===================== --}}
    <div id="categoryDrawerBackdrop"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 transition-opacity duration-300 opacity-0 pointer-events-none"
        onclick="closeCategoryFilterDrawer()"></div>

    <div id="categoryModalWrapper"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 pointer-events-none"
        onclick="closeCategoryFilterDrawer()">

        <div id="categoryFilterDrawer"
            class="w-full sm:max-w-2xl bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col max-h-[85vh] sm:max-h-[80vh] transform transition-all duration-300 ease-out translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0 pointer-events-none overflow-hidden"
            onclick="event.stopPropagation()">

            <!-- Drawer / Modal Header -->
            <div class="px-5 pt-3 pb-3 border-b border-slate-100 shrink-0">
                <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-3 cursor-pointer sm:hidden"
                    onclick="closeCategoryFilterDrawer()"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <h3
                            class="font-extrabold text-base sm:text-lg text-slate-900 font-heading flex items-center gap-2">
                            <i class="fa-solid fa-sliders text-rose-600 text-sm"></i>
                            <span>Filter by Category</span>
                            <span class="text-xs text-rose-600 font-normal">(வகைகள்)</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Select a category to view crackers</p>
                    </div>
                    <button type="button" onclick="closeCategoryFilterDrawer()"
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors cursor-pointer"
                        title="Close">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- View Mode Switcher inside Drawer / Modal -->
                <div
                    class="mt-3 flex items-center justify-between gap-2 bg-slate-50 p-1.5 rounded-xl border border-slate-200/80">
                    <span class="text-xs font-bold text-slate-600 pl-1">Display Mode:</span>
                    <div class="inline-flex rounded-lg bg-slate-200/70 p-0.5 text-xs font-bold">
                        <button type="button" onclick="setViewMode('all')" id="drawerModeAllBtn"
                            class="px-3 py-1 rounded-md transition-all cursor-pointer bg-white text-slate-900 shadow-xs">
                            View All
                        </button>
                        <button type="button" onclick="setViewMode('single')" id="drawerModeSingleBtn"
                            class="px-3 py-1 rounded-md transition-all cursor-pointer text-slate-500 hover:text-slate-900">
                            Single Only
                        </button>
                    </div>
                </div>
            </div>

            <!-- Drawer / Modal Body: Category List (1 column on mobile, 2 columns on desktop) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-2 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-2.5 divide-y sm:divide-y-0 divide-slate-100"
                id="categoryDrawerList">
                <!-- All Categories Option -->
                <button type="button" onclick="handleDrawerCategorySelect('all')"
                    class="drawer-cat-btn w-full text-left p-3 rounded-2xl transition-all flex items-center justify-between gap-3 cursor-pointer select-none bg-rose-50 border-2 border-rose-500 sm:col-span-2"
                    data-drawer-cat="all">
                    <div class="flex items-center gap-3 min-w-0">
                        <span
                            class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-xl shrink-0 shadow-xs border border-slate-100">
                            🌟
                        </span>
                        <div class="min-w-0">
                            <div class="font-bold text-sm text-slate-900">All Categories</div>
                            <div class="text-[11px] text-slate-500">அனைத்து ரகங்களும்</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span
                            class="text-xs font-bold text-slate-600 bg-white px-2 py-0.5 rounded-full border border-slate-200">
                            {{ $categories->sum(fn($c) => $c->products->count()) }} items
                        </span>
                        <i class="fa-solid fa-circle-check text-rose-600 text-lg drawer-check-icon"></i>
                    </div>
                </button>

                <!-- Each Category Option -->
                @foreach ($categories as $category)
                    <button type="button" onclick="handleDrawerCategorySelect('cat-{{ $category->slug }}')"
                        class="drawer-cat-btn w-full text-left p-3 pt-3.5 sm:pt-3 rounded-2xl transition-all flex items-center justify-between gap-3 cursor-pointer select-none hover:bg-slate-50 border-2 border-transparent"
                        data-drawer-cat="cat-{{ $category->slug }}" data-cat-name="{{ $category->name }}"
                        data-cat-tamil="{{ $category->tamil_name ?? '' }}" data-cat-icon="{{ $category->icon ?: '💥' }}"
                        data-cat-count="{{ $category->products->count() }}">
                        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                            <span
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-100 flex items-center justify-center text-lg sm:text-xl shrink-0 border border-slate-200/80">
                                {{ $category->icon ?: '💥' }}
                            </span>
                            <div class="min-w-0">
                                <div class="font-bold text-xs sm:text-sm text-slate-900 truncate drawer-item-name">
                                    {{ $category->name }}</div>
                                @if ($category->tamil_name)
                                    <div class="text-[10px] sm:text-[11px] text-rose-700 font-semibold truncate">
                                        ({{ $category->tamil_name }})
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span
                                class="text-[11px] sm:text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">
                                {{ $category->products->count() }} items
                            </span>
                            <i class="fa-regular fa-circle text-slate-300 text-base sm:text-lg drawer-check-icon"></i>
                        </div>
                    </button>
                @endforeach
            </div>

            <!-- Drawer / Modal Footer -->
            <div class="p-3 border-t border-slate-100 bg-slate-50/80 shrink-0 flex items-center justify-between gap-3">
                <button type="button" onclick="handleDrawerCategorySelect('all')"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
                    Reset to All
                </button>
                <button type="button" onclick="closeCategoryFilterDrawer()"
                    class="flex-1 py-2.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 text-white font-extrabold text-xs text-center shadow-md shadow-rose-600/20 transition-all cursor-pointer font-heading">
                    Done & View Products
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== FLOATING STICKY CART BAR (BOTTOM) ===================== --}}
    <aside id="floatingCartBar"
        class="fixed bottom-0 left-0 right-0 z-40 bg-slate-900/95 backdrop-blur-md text-white border-t border-slate-800 shadow-2xl px-3 py-2 sm:py-2.5 transition-all duration-300 transform translate-y-full pointer-events-none opacity-0">
        <div class="max-w-6xl mx-auto flex items-center justify-between gap-2 sm:gap-3">
            <div class="flex items-center gap-2 sm:gap-6 min-w-0">
                <div class="min-w-0">
                    <div
                        class="text-[9px] sm:text-[10px] text-slate-400 font-semibold uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-solid fa-cart-shopping text-rose-400 text-[10px]"></i>
                        <span class="truncate">Your Order:</span>
                    </div>
                    <div class="text-sm sm:text-base font-extrabold text-white flex items-center gap-1 font-heading">
                        <span id="floatingTotalAmount" class="truncate">₹0.00</span>
                        <span class="text-[10px] font-normal text-slate-400 shrink-0">(<span
                                class="cart-items-badge">0</span>)</span>
                    </div>
                </div>

                <div id="floatingSavingsPill"
                    class="hidden md:inline-flex items-center gap-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full shrink-0">
                    <i class="fa-solid fa-gift text-xs"></i>
                    <span>Save: <span id="floatingSavingsAmount">₹0.00</span></span>
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                <button type="button" onclick="scrollToCheckout()"
                    class="bg-gradient-to-r from-amber-400 via-amber-500 to-rose-500 hover:from-amber-500 hover:to-rose-600 text-slate-950 font-extrabold text-xs sm:text-sm py-2 px-3 sm:px-5 rounded-xl shadow-lg shadow-amber-500/20 active:scale-95 transition-all flex items-center gap-1.5 sm:gap-2 font-heading shrink-0 cursor-pointer"
                    title="View Cart & Checkout">
                    <i class="fa-solid fa-cart-shopping text-xs sm:text-sm"></i>
                    <span class="sm:hidden">View Cart</span>
                    <span class="hidden sm:inline">Review & Checkout</span>
                </button>
            </div>
        </div>
    </aside>

    {{-- ===================== JAVASCRIPT LOGIC ===================== --}}
    @push('scripts')
        <script>
            // Elements & State management
            const qtyInputs = document.querySelectorAll('.qty-input');
            const floatingTotalEl = document.getElementById('floatingTotalAmount');
            const heroTotalEl = document.getElementById('heroTotalDisplay');
            const heroCountEl = document.getElementById('heroItemsCount');
            const summaryActualTotalEl = document.getElementById('summaryActualTotal');
            const summarySavingsTotalEl = document.getElementById('summarySavingsTotal');
            const summaryPayableTotalEl = document.getElementById('summaryPayableTotal');
            const btnTotalTextEl = document.getElementById('btnTotalText');
            const floatingSavingsAmountEl = document.getElementById('floatingSavingsAmount');
            const floatingSavingsPill = document.getElementById('floatingSavingsPill');
            const floatingCartBar = document.getElementById('floatingCartBar');
            const selectedItemsContainer = document.getElementById('selectedItemsContainer');
            const emptyCartNotice = document.getElementById('emptyCartNotice');
            const cartBadgeEls = document.querySelectorAll('.cart-items-badge');
            const searchInput = document.getElementById('searchInput');
            const clearSearchBtn = document.getElementById('clearSearchBtn');
            const searchMatchStatus = document.getElementById('searchMatchStatus');
            const searchKeyword = document.getElementById('searchKeyword');
            const matchCountText = document.getElementById('matchCountText');
            const noResultsBox = document.getElementById('noResultsBox');
            const productRows = document.querySelectorAll('.product-row');
            const categorySections = document.querySelectorAll('.category-section');
            const categoryTabBtns = document.querySelectorAll('.category-tab-btn');

            // View mode: 'all' or 'single'
            let currentViewMode = 'all';
            let currentSelectedCategory = 'all';
            let currentTotalItemsCount = 0;

            function formatINR(val) {
                return '₹' + Number(val).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Recalculate totals, savings, category badges, and live summary
            function recalcCart() {
                let totalPayable = 0;
                let totalActual = 0;
                let totalItemsCount = 0;
                let categoryCountMap = {};
                let selectedList = [];

                qtyInputs.forEach(input => {
                    const qty = parseInt(input.value) || 0;
                    const netPrice = parseFloat(input.dataset.price) || 0;
                    const actualPrice = parseFloat(input.dataset.actualPrice) || netPrice;
                    const productId = input.dataset.id;
                    const categoryId = input.dataset.categoryId;
                    const lineTotalEl = document.getElementById('line-total-' + productId);
                    const rowEl = document.getElementById('product-row-' + productId);

                    const lineTotal = qty * netPrice;
                    if (lineTotalEl) {
                        lineTotalEl.textContent = formatINR(lineTotal);
                    }

                    if (rowEl) {
                        if (qty > 0) {
                            rowEl.classList.add('bg-rose-50/70', 'ring-1', 'ring-rose-300');
                            if (lineTotalEl) {
                                lineTotalEl.classList.remove('text-slate-700');
                                lineTotalEl.classList.add('text-rose-700', 'font-black');
                            }
                        } else {
                            rowEl.classList.remove('bg-rose-50/70', 'ring-1', 'ring-rose-300');
                            if (lineTotalEl) {
                                lineTotalEl.classList.add('text-slate-700');
                                lineTotalEl.classList.remove('text-rose-700', 'font-black');
                            }
                        }
                    }

                    if (qty > 0) {
                        totalPayable += lineTotal;
                        totalActual += qty * actualPrice;
                        totalItemsCount += qty;
                        categoryCountMap[categoryId] = (categoryCountMap[categoryId] || 0) + qty;

                        selectedList.push({
                            id: productId,
                            name: input.dataset.name,
                            tamil: input.dataset.tamil,
                            unit: input.dataset.unit,
                            price: netPrice,
                            qty: qty,
                            lineTotal: lineTotal
                        });
                    }
                });

                currentTotalItemsCount = totalItemsCount;
                const totalSavings = Math.max(0, totalActual - totalPayable);
                const payableFormatted = formatINR(totalPayable);

                if (floatingTotalEl) floatingTotalEl.textContent = payableFormatted;
                if (heroTotalEl) heroTotalEl.textContent = payableFormatted;
                document.querySelectorAll('.hero-total-sync').forEach(el => el.textContent = payableFormatted);
                if (summaryPayableTotalEl) summaryPayableTotalEl.textContent = payableFormatted;
                if (btnTotalTextEl) btnTotalTextEl.textContent = payableFormatted;

                if (summaryActualTotalEl) summaryActualTotalEl.textContent = formatINR(totalActual);
                if (summarySavingsTotalEl) summarySavingsTotalEl.textContent = '- ' + formatINR(totalSavings);
                if (floatingSavingsAmountEl) floatingSavingsAmountEl.textContent = formatINR(totalSavings);

                if (floatingSavingsPill) {
                    if (totalSavings > 0) {
                        floatingSavingsPill.classList.remove('hidden');
                        floatingSavingsPill.classList.add('inline-flex');
                    } else {
                        floatingSavingsPill.classList.add('hidden');
                        floatingSavingsPill.classList.remove('inline-flex');
                    }
                }

                // Show floating checkout bar only when cart has items
                if (floatingCartBar) {
                    if (totalItemsCount > 0) {
                        floatingCartBar.classList.remove('translate-y-full', 'pointer-events-none', 'opacity-0');
                        floatingCartBar.classList.add('translate-y-0', 'pointer-events-auto', 'opacity-100');
                    } else {
                        floatingCartBar.classList.add('translate-y-full', 'pointer-events-none', 'opacity-0');
                        floatingCartBar.classList.remove('translate-y-0', 'pointer-events-auto', 'opacity-100');
                    }
                }

                cartBadgeEls.forEach(badge => badge.textContent = selectedList.length);
                if (heroCountEl) {
                    heroCountEl.textContent = `${selectedList.length} varieties (${totalItemsCount} units) selected`;
                }
                document.querySelectorAll('.hero-count-sync').forEach(el => {
                    el.textContent = `${selectedList.length} varieties (${totalItemsCount} units)`;
                });

                categorySections.forEach(section => {
                    const catId = section.id;
                    const count = categoryCountMap[catId] || 0;

                    const headerBadge = section.querySelector('.category-items-count-badge');
                    if (headerBadge) {
                        if (count > 0) {
                            headerBadge.textContent = `${count} in cart`;
                            headerBadge.classList.remove('hidden');
                        } else {
                            headerBadge.classList.add('hidden');
                        }
                    }

                    const tabBtn = document.getElementById('tab-' + catId);
                    if (tabBtn) {
                        const tabBadge = tabBtn.querySelector('.category-selected-badge');
                        if (tabBadge) {
                            if (count > 0) {
                                tabBadge.textContent = count;
                                tabBadge.classList.remove('hidden');
                            } else {
                                tabBadge.classList.add('hidden');
                            }
                        }
                    }
                });

                // Re-sync tab highlight colors without breaking text contrast
                updateTabActiveState(currentSelectedCategory);
                renderSelectedItems(selectedList);
            }

            // Render selected items
            function renderSelectedItems(items) {
                if (!items.length) {
                    selectedItemsContainer.innerHTML = '';
                    selectedItemsContainer.appendChild(emptyCartNotice);
                    emptyCartNotice.classList.remove('hidden');
                    return;
                }

                let html = '';
                items.forEach(item => {
                    html += `
                <div class="py-2.5 flex items-center justify-between gap-3 text-xs">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-bold text-slate-800">${item.name}</span>
                            ${item.tamil ? `<span class="hidden sm:inline text-[11px] font-semibold text-rose-700">(${item.tamil})</span>` : ''}
                            ${item.unit ? `<span class="inline-flex items-center text-[10px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded-md shrink-0">${item.unit}</span>` : ''}
                        </div>
                        ${item.tamil ? `<div class="sm:hidden text-[10px] text-rose-700 font-semibold mt-0.5">${item.tamil}</div>` : ''}
                        <div class="text-[11px] text-slate-500 mt-0.5">₹${item.price.toFixed(2)} × ${item.qty}</div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="font-extrabold text-rose-700 text-sm">${formatINR(item.lineTotal)}</span>
                        <button type="button" onclick="removeItem(${item.id})" class="text-slate-400 hover:text-rose-600 p-1 transition-colors cursor-pointer" title="Remove item">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            `;
                });
                selectedItemsContainer.innerHTML = html;
            }

            // Steppers
            window.stepQuantity = function(productId, delta) {
                const input = document.getElementById('qty-input-' + productId);
                if (!input) return;
                let current = parseInt(input.value) || 0;
                if (delta > 0 && current >= 20) {
                    DiwaliAlert.toast({
                        type: 'warning',
                        message: 'Maximum 20 units allowed per item'
                    });
                    return;
                }
                let next = Math.max(0, Math.min(20, current + delta));
                input.value = next;
                recalcCart();
            };

            window.removeItem = function(productId) {
                const input = document.getElementById('qty-input-' + productId);
                if (input) {
                    input.value = 0;
                    recalcCart();
                }
            };

            window.clearAllQuantities = function() {
                if (currentTotalItemsCount === 0) {
                    DiwaliAlert.info('Cart Already Empty', 'There are no items in your cart to reset.');
                    return;
                }
                DiwaliAlert.confirm({
                    title: 'Reset All Quantities?',
                    text: 'Are you sure you want to clear all selected cracker quantities from your cart?',
                    confirmText: 'Yes, Reset Cart',
                    cancelText: 'Keep Items',
                    icon: 'warning',
                    onConfirm: () => {
                        qtyInputs.forEach(input => input.value = 0);
                        recalcCart();
                        DiwaliAlert.toast({
                            type: 'info',
                            message: 'Cart reset to 0'
                        });
                    }
                });
            };

            // Accordion Toggle
            window.toggleCategoryAccordion = function(catId) {
                const body = document.getElementById('body-' + catId);
                const chevron = document.getElementById('chevron-' + catId);
                if (!body) return;

                const isHidden = body.classList.contains('hidden');
                if (isHidden) {
                    body.classList.remove('hidden');
                    if (chevron) chevron.classList.remove('-rotate-90');
                } else {
                    body.classList.add('hidden');
                    if (chevron) chevron.classList.add('-rotate-90');
                }
            };

            // View Mode Switcher: 'all' vs 'single'
            window.setViewMode = function(mode) {
                currentViewMode = mode;
                const viewAllBtn = document.getElementById('viewModeAllBtn');
                const viewSingleBtn = document.getElementById('viewModeSingleBtn');
                const drawerModeAllBtn = document.getElementById('drawerModeAllBtn');
                const drawerModeSingleBtn = document.getElementById('drawerModeSingleBtn');

                if (mode === 'all') {
                    if (viewAllBtn) {
                        viewAllBtn.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
                        viewAllBtn.classList.remove('text-slate-500');
                    }
                    if (viewSingleBtn) {
                        viewSingleBtn.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
                        viewSingleBtn.classList.add('text-slate-500');
                    }
                    if (drawerModeAllBtn) {
                        drawerModeAllBtn.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                        drawerModeAllBtn.classList.remove('text-slate-500');
                    }
                    if (drawerModeSingleBtn) {
                        drawerModeSingleBtn.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                        drawerModeSingleBtn.classList.add('text-slate-500');
                    }
                    // Reset selected category to 'all' and update displays
                    currentSelectedCategory = 'all';
                    updateTabActiveState('all');
                    updateActiveCategoryDisplay('all');

                    // Show all category sections and ensure accordion bodies are visible
                    categorySections.forEach(sec => {
                        sec.classList.remove('hidden');
                        const body = document.getElementById('body-' + sec.id);
                        const chevron = document.getElementById('chevron-' + sec.id);
                        if (body) body.classList.remove('hidden');
                        if (chevron) chevron.classList.remove('-rotate-90');
                    });
                } else {
                    // Single Mode
                    if (viewSingleBtn) {
                        viewSingleBtn.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
                        viewSingleBtn.classList.remove('text-slate-500');
                    }
                    if (viewAllBtn) {
                        viewAllBtn.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
                        viewAllBtn.classList.add('text-slate-500');
                    }
                    if (drawerModeSingleBtn) {
                        drawerModeSingleBtn.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                        drawerModeSingleBtn.classList.remove('text-slate-500');
                    }
                    if (drawerModeAllBtn) {
                        drawerModeAllBtn.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                        drawerModeAllBtn.classList.add('text-slate-500');
                    }

                    // If current selection is 'all', pick first category
                    if ((currentSelectedCategory === 'all' || !currentSelectedCategory) && categorySections.length > 0) {
                        currentSelectedCategory = categorySections[0].id;
                    }
                    selectCategory(currentSelectedCategory);
                }
            };

            // Update Category Filter Bottom Sheet & Outside Selected Category Pill
            function updateActiveCategoryDisplay(categoryId) {
                const container = document.getElementById('activeCategoryPillContainer');
                const activeCatIcon = document.getElementById('activeCatIcon');
                const activeCatName = document.getElementById('activeCatName');
                const activeCatTamil = document.getElementById('activeCatTamil');
                const activeCatCount = document.getElementById('activeCatCount');
                const activeFilterDot = document.getElementById('activeFilterDot');

                // Update drawer radio styles as well
                const drawerButtons = document.querySelectorAll('.drawer-cat-btn');
                drawerButtons.forEach(btn => {
                    const isSelected = (btn.dataset.drawerCat === categoryId);
                    const iconEl = btn.querySelector('.drawer-check-icon');
                    if (isSelected) {
                        btn.classList.add('bg-rose-50', 'border-rose-500');
                        btn.classList.remove('border-transparent', 'hover:bg-slate-50');
                        if (iconEl) {
                            iconEl.classList.remove('fa-regular', 'fa-circle', 'text-slate-300');
                            iconEl.classList.add('fa-solid', 'fa-circle-check', 'text-rose-600');
                        }
                    } else {
                        btn.classList.remove('bg-rose-50', 'border-rose-500');
                        btn.classList.add('border-transparent', 'hover:bg-slate-50');
                        if (iconEl) {
                            iconEl.classList.remove('fa-solid', 'fa-circle-check', 'text-rose-600');
                            iconEl.classList.add('fa-regular', 'fa-circle', 'text-slate-300');
                        }
                    }
                });

                if (categoryId === 'all') {
                    // Hide outside category pill when no filter is active
                    if (container) {
                        container.classList.add('hidden');
                        container.classList.remove('flex');
                    }
                    if (activeFilterDot) activeFilterDot.classList.add('hidden');
                } else {
                    const drawerBtn = document.querySelector(`.drawer-cat-btn[data-drawer-cat="${categoryId}"]`);
                    if (drawerBtn) {
                        const name = drawerBtn.dataset.catName || '';
                        const tamil = drawerBtn.dataset.catTamil || '';
                        const icon = drawerBtn.dataset.catIcon || '💥';
                        const count = drawerBtn.dataset.catCount || '0';

                        if (activeCatIcon) activeCatIcon.textContent = icon;
                        if (activeCatName) activeCatName.textContent = name;
                        if (activeCatTamil) activeCatTamil.textContent = tamil ? `(${tamil})` : '';
                        if (activeCatCount) activeCatCount.textContent = `(${count})`;

                        // Show outside category pill when filter is active
                        if (container) {
                            container.classList.remove('hidden');
                            container.classList.add('flex');
                        }
                        if (activeFilterDot) activeFilterDot.classList.remove('hidden');
                    }
                }
            }

            // Category Filter Drawer (Mobile) & Modal (Desktop) controls
            window.openCategoryFilterDrawer = function() {
                const backdrop = document.getElementById('categoryDrawerBackdrop');
                const wrapper = document.getElementById('categoryModalWrapper');
                const drawer = document.getElementById('categoryFilterDrawer');
                if (backdrop && drawer) {
                    backdrop.classList.remove('opacity-0', 'pointer-events-none');
                    backdrop.classList.add('opacity-100', 'pointer-events-auto');

                    if (wrapper) {
                        wrapper.classList.remove('pointer-events-none');
                        wrapper.classList.add('pointer-events-auto');
                    }

                    drawer.classList.remove(
                        'translate-y-full',
                        'sm:scale-95',
                        'sm:opacity-0',
                        'pointer-events-none'
                    );
                    drawer.classList.add(
                        'translate-y-0',
                        'sm:scale-100',
                        'sm:opacity-100',
                        'pointer-events-auto'
                    );
                    document.body.classList.add('overflow-hidden');
                }
            };

            window.closeCategoryFilterDrawer = function() {
                const backdrop = document.getElementById('categoryDrawerBackdrop');
                const wrapper = document.getElementById('categoryModalWrapper');
                const drawer = document.getElementById('categoryFilterDrawer');
                if (backdrop && drawer) {
                    backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                    backdrop.classList.add('opacity-0', 'pointer-events-none');

                    if (wrapper) {
                        wrapper.classList.remove('pointer-events-auto');
                        wrapper.classList.add('pointer-events-none');
                    }

                    drawer.classList.remove(
                        'translate-y-0',
                        'sm:scale-100',
                        'sm:opacity-100',
                        'pointer-events-auto'
                    );
                    drawer.classList.add(
                        'translate-y-full',
                        'sm:scale-95',
                        'sm:opacity-0',
                        'pointer-events-none'
                    );
                    document.body.classList.remove('overflow-hidden');
                }
            };

            window.handleDrawerCategorySelect = function(catId) {
                selectCategory(catId);
                closeCategoryFilterDrawer();
            };

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeCategoryFilterDrawer();
                }
            });

            // Update Category Tab styles cleanly (avoid white-on-white text)
            function updateTabActiveState(targetId) {
                categoryTabBtns.forEach(btn => {
                    const isMatch = (btn.dataset.target === targetId);
                    const countBadge = btn.querySelector('.category-selected-badge');
                    const count = countBadge ? parseInt(countBadge.textContent || '0') : 0;
                    const tamilSpan = btn.querySelector('.tab-tamil-text');

                    if (isMatch) {
                        // ACTIVE TAB: Vibrant Rose background, clear White text
                        btn.classList.remove(
                            'bg-white', 'text-slate-700', 'border-slate-200',
                            'hover:border-rose-300', 'hover:text-rose-600',
                            'bg-rose-50', 'text-rose-700', 'border-rose-300',
                            'font-medium', 'font-semibold'
                        );
                        btn.classList.add('bg-rose-600', 'text-white', 'border-rose-600', 'shadow-sm', 'font-bold');

                        if (tamilSpan) {
                            tamilSpan.classList.remove('text-slate-400');
                            tamilSpan.classList.add('text-rose-100');
                        }
                        if (countBadge) {
                            countBadge.classList.remove('bg-rose-600', 'text-white');
                            countBadge.classList.add('bg-white', 'text-rose-700');
                        }
                    } else {
                        // INACTIVE TAB
                        btn.classList.remove('bg-rose-600', 'text-white', 'border-rose-600', 'shadow-sm', 'font-bold');

                        if (count > 0) {
                            btn.classList.add('bg-rose-50', 'text-rose-700', 'border-rose-300', 'font-semibold');
                            btn.classList.remove('bg-white', 'text-slate-700', 'border-slate-200');
                        } else {
                            btn.classList.add('bg-white', 'text-slate-700', 'border-slate-200', 'hover:border-rose-300',
                                'hover:text-rose-600', 'font-medium');
                            btn.classList.remove('bg-rose-50', 'text-rose-700', 'border-rose-300', 'font-semibold');
                        }

                        if (tamilSpan) {
                            tamilSpan.classList.remove('text-rose-100');
                            tamilSpan.classList.add('text-slate-400');
                        }
                        if (countBadge) {
                            countBadge.classList.remove('bg-white', 'text-rose-700');
                            countBadge.classList.add('bg-rose-600', 'text-white');
                        }
                    }
                });
            }

            // Category Selector
            window.selectCategory = function(categoryId) {
                currentSelectedCategory = categoryId;
                updateTabActiveState(categoryId);
                updateActiveCategoryDisplay(categoryId);

                // Auto-scroll the clicked tab into horizontal view
                const activeTab = document.getElementById('tab-' + categoryId);
                if (activeTab) {
                    activeTab.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                }

                if (categoryId === 'all') {
                    currentViewMode = 'all';
                    const viewAllBtn = document.getElementById('viewModeAllBtn');
                    const viewSingleBtn = document.getElementById('viewModeSingleBtn');
                    const drawerModeAllBtn = document.getElementById('drawerModeAllBtn');
                    const drawerModeSingleBtn = document.getElementById('drawerModeSingleBtn');

                    if (viewAllBtn) {
                        viewAllBtn.classList.add('bg-white', 'text-slate-900', 'shadow-sm');
                        viewAllBtn.classList.remove('text-slate-500');
                    }
                    if (viewSingleBtn) {
                        viewSingleBtn.classList.remove('bg-white', 'text-slate-900', 'shadow-sm');
                        viewSingleBtn.classList.add('text-slate-500');
                    }
                    if (drawerModeAllBtn) {
                        drawerModeAllBtn.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                        drawerModeAllBtn.classList.remove('text-slate-500');
                    }
                    if (drawerModeSingleBtn) {
                        drawerModeSingleBtn.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                        drawerModeSingleBtn.classList.add('text-slate-500');
                    }

                    categorySections.forEach(sec => {
                        sec.classList.remove('hidden');
                        const body = document.getElementById('body-' + sec.id);
                        const chevron = document.getElementById('chevron-' + sec.id);
                        if (body) body.classList.remove('hidden');
                        if (chevron) chevron.classList.remove('-rotate-90');
                    });
                    const catCatalog = document.getElementById('productCatalog');
                    if (catCatalog) {
                        catCatalog.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                    return;
                }

                if (currentViewMode === 'single') {
                    // SINGLE TAB VIEW: Show ONLY the selected category, hide all other categories
                    categorySections.forEach(sec => {
                        if (sec.id === categoryId) {
                            sec.classList.remove('hidden');
                            // Always expand the single selected category body
                            const body = document.getElementById('body-' + sec.id);
                            const chevron = document.getElementById('chevron-' + sec.id);
                            if (body) body.classList.remove('hidden');
                            if (chevron) chevron.classList.remove('-rotate-90');
                        } else {
                            sec.classList.add('hidden');
                        }
                    });

                    // Smoothly scroll to the target category
                    const target = document.getElementById(categoryId);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                } else {
                    // VIEW ALL MODE: All categories remain visible, open target and scroll to it
                    categorySections.forEach(sec => sec.classList.remove('hidden'));

                    const body = document.getElementById('body-' + categoryId);
                    const chevron = document.getElementById('chevron-' + categoryId);
                    if (body) body.classList.remove('hidden');
                    if (chevron) chevron.classList.remove('-rotate-90');

                    const target = document.getElementById(categoryId);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            };

            window.scrollToCheckout = function() {
                const target = document.getElementById('checkoutSection');
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            };

            // Image lightbox modal
            window.openImageModal = function(url, title, tamilTitle) {
                document.getElementById('modalImg').src = url;
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('modalTamilTitle').textContent = tamilTitle || '';
                document.getElementById('imageModal').classList.remove('hidden');
            };

            window.closeImageModal = function() {
                document.getElementById('imageModal').classList.add('hidden');
            };

            // Live bilingual search (English + Tamil) with Debounce & Strict Sanitization
            let searchDebounceTimer = null;

            function debouncedPerformSearch() {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(performSearch, 150);
            }

            function performSearch() {
                if (searchInput) {
                    let cleanVal = searchInput.value.replace(/[^a-zA-Z0-9\s.\-"'½¾¼()\u0B80-\u0BFF]/g, '');
                    cleanVal = cleanVal.replace(/[\-\.]{2,}/g, '-');
                    cleanVal = cleanVal.replace(/^[\-\.\s]+/, '');
                    if (searchInput.value !== cleanVal) {
                        searchInput.value = cleanVal;
                    }
                }

                const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
                const hasValidChar = /[a-zA-Z0-9\u0B80-\u0BFF]/.test(query);

                if (!query || !hasValidChar) {
                    clearSearchBtn.classList.add('hidden');
                    searchMatchStatus.classList.add('hidden');
                    noResultsBox.classList.add('hidden');

                    productRows.forEach(row => row.classList.remove('hidden'));

                    if (currentViewMode === 'single') {
                        // Restore single view mode
                        selectCategory(currentSelectedCategory);
                    } else {
                        categorySections.forEach(sec => sec.classList.remove('hidden'));
                    }
                    return;
                }

                clearSearchBtn.classList.remove('hidden');
                searchMatchStatus.classList.remove('hidden');
                searchKeyword.textContent = query;

                let totalMatches = 0;

                categorySections.forEach(sec => {
                    let catMatches = 0;
                    const rowsInSec = sec.querySelectorAll('.product-row');

                    rowsInSec.forEach(row => {
                        const name = row.dataset.productName || '';
                        const tamilName = row.dataset.tamilName || '';
                        const catName = row.dataset.categoryName || '';
                        const catTamil = row.dataset.categoryTamil || '';

                        if (name.includes(query) || tamilName.includes(query) || catName.includes(query) ||
                            catTamil.includes(query)) {
                            row.classList.remove('hidden');
                            catMatches++;
                            totalMatches++;
                        } else {
                            row.classList.add('hidden');
                        }
                    });

                    if (catMatches > 0) {
                        sec.classList.remove('hidden');
                        const body = document.getElementById('body-' + sec.id);
                        const chevron = document.getElementById('chevron-' + sec.id);
                        if (body) body.classList.remove('hidden');
                        if (chevron) chevron.classList.remove('-rotate-90');
                    } else {
                        sec.classList.add('hidden');
                    }
                });

                matchCountText.textContent = `${totalMatches} items found`;

                if (totalMatches === 0) {
                    noResultsBox.classList.remove('hidden');
                } else {
                    noResultsBox.classList.add('hidden');
                }
            }

            window.clearSearch = function() {
                searchInput.value = '';
                performSearch();
            };

            // ==========================================
            // Real-time Input Sanitization & Error Handling
            // ==========================================
            const nameInput = document.getElementById('nameInput');
            const phone1Input = document.getElementById('phone1Input');
            const phone2Input = document.getElementById('phone2Input');
            const deliveryAddressInput = document.getElementById('deliveryAddressInput');
            const cityInput = document.getElementById('cityInput');
            const pincodeInput = document.getElementById('pincodeInput');

            function showFieldError(fieldId, message) {
                const errEl = document.getElementById('err-' + fieldId);
                if (errEl) {
                    const span = errEl.querySelector('span');
                    if (span) span.textContent = message;
                    errEl.classList.remove('hidden');
                }

                if (fieldId === 'phone1') {
                    const wrap = document.getElementById('phone1Wrapper');
                    if (wrap) {
                        wrap.classList.remove('border-slate-200');
                        wrap.classList.add('border-red-500', 'bg-red-50/30', 'ring-2', 'ring-red-200');
                    }
                } else if (fieldId === 'phone2') {
                    const wrap = document.getElementById('phone2Wrapper');
                    if (wrap) {
                        wrap.classList.remove('border-slate-200');
                        wrap.classList.add('border-red-500', 'bg-red-50/30', 'ring-2', 'ring-red-200');
                    }
                } else {
                    const input = document.getElementById(fieldId === 'delivery_address' ? 'deliveryAddressInput' : fieldId +
                        'Input');
                    if (input) {
                        input.classList.remove('border-slate-200');
                        input.classList.add('border-red-500', 'bg-red-50/30', 'ring-2', 'ring-red-200');
                    }
                }
            }

            function clearFieldError(fieldId) {
                const errEl = document.getElementById('err-' + fieldId);
                if (errEl) {
                    errEl.classList.add('hidden');
                }

                if (fieldId === 'phone1') {
                    const wrap = document.getElementById('phone1Wrapper');
                    if (wrap) {
                        wrap.classList.add('border-slate-200');
                        wrap.classList.remove('border-red-500', 'bg-red-50/30', 'ring-2', 'ring-red-200');
                    }
                } else if (fieldId === 'phone2') {
                    const wrap = document.getElementById('phone2Wrapper');
                    if (wrap) {
                        wrap.classList.add('border-slate-200');
                        wrap.classList.remove('border-red-500', 'bg-red-50/30', 'ring-2', 'ring-red-200');
                    }
                } else {
                    const input = document.getElementById(fieldId === 'delivery_address' ? 'deliveryAddressInput' : fieldId +
                        'Input');
                    if (input) {
                        input.classList.add('border-slate-200');
                        input.classList.remove('border-red-500', 'bg-red-50/30', 'ring-2', 'ring-red-200');
                    }
                }
            }

            // Input sanitizers (allow only needed characters)
            if (nameInput) {
                nameInput.addEventListener('input', function() {
                    // Letters, spaces, and dots only
                    this.value = this.value.replace(/[^a-zA-Z\s.]/g, '');
                    if (this.value.trim().length >= 2) clearFieldError('name');
                });
            }

            // Strict 10-digit Indian Mobile Validation (Starts with 6, 7, 8, or 9 only)
            function setupStrictMobileInput(inputEl, fieldId, isRequired, fieldLabel) {
                if (!inputEl) return;

                // 1. Prevent entering non-digits, starting digits other than 6, 7, 8, 9, or exceeding 10 digits via keyboard
                inputEl.addEventListener('keydown', function(e) {
                    // Allow navigation / utility keys (Backspace, Tab, Arrows, Delete, Enter, Ctrl/Cmd shortcuts)
                    if (['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Enter',
                            'Home', 'End'
                        ].includes(e.key) || e.ctrlKey || e.metaKey) {
                        return;
                    }

                    // Block any non-numeric character
                    if (!/^[0-9]$/.test(e.key)) {
                        e.preventDefault();
                        return;
                    }

                    const selStart = this.selectionStart ?? 0;
                    const selEnd = this.selectionEnd ?? 0;

                    // If typing at the first digit position (position 0)
                    if (selStart === 0) {
                        if (!['6', '7', '8', '9'].includes(e.key)) {
                            e.preventDefault();
                            showFieldError(fieldId, fieldLabel + ' must start with 6, 7, 8, or 9');
                            return;
                        }
                    }

                    // Prevent entering more than 10 digits
                    if (this.value.length >= 10 && selStart === selEnd) {
                        e.preventDefault();
                    }
                });

                // 2. Real-time input cleaner & validator (handles paste, autofill, mobile IME)
                inputEl.addEventListener('input', function() {
                    let val = this.value.replace(/[^0-9]/g, '');

                    // Strip common country code prefixes if pasted (+91, 91, or leading 0)
                    if (val.length > 10 && val.startsWith('91')) val = val.substring(2);
                    if (val.length > 10 && val.startsWith('0')) val = val.substring(1);

                    // Strictly reject if first digit is not 6, 7, 8, or 9
                    if (val.length > 0 && !['6', '7', '8', '9'].includes(val.charAt(0))) {
                        this.value = '';
                        showFieldError(fieldId, fieldLabel + ' must start with 6, 7, 8, or 9');
                        return;
                    }

                    // Strictly cap to 10 digits
                    val = val.slice(0, 10);
                    this.value = val;

                    // Real-time status
                    if (val.length === 10) {
                        if (fieldId === 'phone2' && phone1Input && val === phone1Input.value.trim()) {
                            showFieldError('phone2', 'Alternate mobile cannot be the same as WhatsApp number');
                        } else {
                            clearFieldError(fieldId);
                            // Also if editing phone1 and phone2 had collision error, re-check phone2
                            if (fieldId === 'phone1' && phone2Input && phone2Input.value.trim().length === 10) {
                                if (phone2Input.value.trim() === val) {
                                    showFieldError('phone2', 'Alternate mobile cannot be the same as WhatsApp number');
                                } else {
                                    clearFieldError('phone2');
                                }
                            }
                        }
                    } else if (val.length === 0) {
                        if (!isRequired) {
                            clearFieldError(fieldId);
                        }
                    } else {
                        // While user is actively typing valid digits (1-9 digits starting with 6-9),
                        // clear any "must start with 6, 7, 8, 9" error so user has a smooth typing experience
                        clearFieldError(fieldId);
                    }
                });

                // 3. Blur event: check completeness when focus leaves the input
                inputEl.addEventListener('blur', function() {
                    const val = this.value.trim();
                    if (!val) {
                        if (isRequired) {
                            showFieldError(fieldId, fieldLabel + ' is required (10 digits)');
                        } else {
                            clearFieldError(fieldId);
                        }
                        return;
                    }

                    if (!/^[6-9]/.test(val)) {
                        this.value = '';
                        showFieldError(fieldId, fieldLabel + ' must start with 6, 7, 8, or 9');
                    } else if (val.length !== 10) {
                        showFieldError(fieldId, fieldLabel + ' must be exactly 10 digits');
                    } else if (fieldId === 'phone2' && phone1Input && val === phone1Input.value.trim()) {
                        showFieldError('phone2', 'Alternate mobile cannot be the same as WhatsApp number');
                    } else {
                        clearFieldError(fieldId);
                    }
                });
            }

            setupStrictMobileInput(phone1Input, 'phone1', true, 'WhatsApp mobile number');
            setupStrictMobileInput(phone2Input, 'phone2', false, 'Alternate mobile number');

            if (deliveryAddressInput) {
                deliveryAddressInput.addEventListener('input', function() {
                    // Keep alphanumeric, spaces, and address punctuation (/ . , - # : ( ))
                    // Strictly disallow harmful characters: < > { } [ ] ~ ^ $ * ; " ' ! ? = + \ | %
                    this.value = this.value.replace(/[<>{}[\]~^$*;\"'!+?\\|%]/g, '');
                    if (this.value.trim().length >= 5) clearFieldError('delivery_address');
                });
            }

            if (cityInput) {
                cityInput.addEventListener('input', function() {
                    // Letters, spaces, dots, and hyphens only
                    this.value = this.value.replace(/[^a-zA-Z\s.-]/g, '');
                    if (this.value.trim().length >= 2) clearFieldError('city');
                });
            }

            if (pincodeInput) {
                pincodeInput.addEventListener('input', function() {
                    // Digits only, max 6
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                    if (this.value.length === 6) clearFieldError('pincode');
                });
            }

            // Attach quantity input listeners with 0-20 clamp
            qtyInputs.forEach(input => {
                const handleQtyInput = function() {
                    let val = parseInt(this.value);
                    if (isNaN(val) || val < 0) {
                        this.value = 0;
                    } else if (val > 20) {
                        this.value = 20;
                        DiwaliAlert.toast({
                            type: 'warning',
                            message: 'Maximum 20 units allowed per item'
                        });
                    }
                    recalcCart();
                };
                input.addEventListener('input', handleQtyInput);
                input.addEventListener('change', handleQtyInput);
                input.addEventListener('focus', function() {
                    if (this.value === '0') this.select();
                });
            });

            searchInput.addEventListener('input', debouncedPerformSearch);

            // Document Ready & Order Submission
            document.addEventListener('DOMContentLoaded', () => {
                recalcCart();
                updateActiveCategoryDisplay('all');

                const orderForm = document.getElementById('orderForm');
                if (orderForm) {
                    let isOrderSubmitting = false;

                    orderForm.addEventListener('submit', function(e) {
                        let hasError = false;
                        let firstErrorEl = null;

                        // 1. Validate Cart is not empty
                        if (currentTotalItemsCount === 0) {
                            e.preventDefault();
                            DiwaliAlert.warning(
                                'Your Cart is Empty! 🛒',
                                'Please select at least one cracker variety with quantity greater than 0 before placing your order.',
                                'Choose Crackers'
                            );
                            selectCategory('all');
                            return false;
                        }

                        // Validate max 20 per item
                        let hasOverMaxQty = false;
                        qtyInputs.forEach(input => {
                            let val = parseInt(input.value) || 0;
                            if (val > 20) {
                                input.value = 20;
                                hasOverMaxQty = true;
                            }
                        });
                        if (hasOverMaxQty) {
                            recalcCart();
                            e.preventDefault();
                            DiwaliAlert.warning(
                                'Quantity Limit Exceeded ⚠️',
                                'Maximum 20 units allowed per item. Quantities have been adjusted to 20.',
                                'Review Order'
                            );
                            return false;
                        }

                        // 2. Validate Customer Name
                        const nameVal = nameInput ? nameInput.value.trim() : '';
                        if (!nameVal) {
                            showFieldError('name', 'Customer full name is required');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = nameInput;
                        } else if (nameVal.length < 3) {
                            showFieldError('name', 'Please enter a valid full name (minimum 3 letters)');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = nameInput;
                        } else if (nameVal.length > 60) {
                            showFieldError('name', 'Name must not exceed 60 characters');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = nameInput;
                        } else {
                            clearFieldError('name');
                        }

                        // 3. Validate Primary Phone
                        const phone1Val = phone1Input ? phone1Input.value.trim() : '';
                        if (!phone1Val) {
                            showFieldError('phone1', 'WhatsApp mobile number is required (10 digits)');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = phone1Input;
                        } else if (!/^[6-9][0-9]{9}$/.test(phone1Val)) {
                            if (!/^[6-9]/.test(phone1Val)) {
                                showFieldError('phone1',
                                    'WhatsApp mobile number must start with 6, 7, 8, or 9');
                            } else {
                                showFieldError('phone1', 'WhatsApp mobile number must be exactly 10 digits');
                            }
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = phone1Input;
                        } else {
                            clearFieldError('phone1');
                        }

                        // 4. Validate Alternate Phone (Optional)
                        const phone2Val = phone2Input ? phone2Input.value.trim() : '';
                        if (phone2Val) {
                            if (!/^[6-9][0-9]{9}$/.test(phone2Val)) {
                                if (!/^[6-9]/.test(phone2Val)) {
                                    showFieldError('phone2',
                                        'Alternate mobile number must start with 6, 7, 8, or 9');
                                } else {
                                    showFieldError('phone2',
                                        'Alternate mobile number must be exactly 10 digits');
                                }
                                hasError = true;
                                if (!firstErrorEl) firstErrorEl = phone2Input;
                            } else if (phone2Val === phone1Val) {
                                showFieldError('phone2',
                                    'Alternate mobile cannot be the same as WhatsApp number');
                                hasError = true;
                                if (!firstErrorEl) firstErrorEl = phone2Input;
                            } else {
                                clearFieldError('phone2');
                            }
                        } else {
                            clearFieldError('phone2');
                        }

                        // 5. Validate Delivery Address
                        const addressVal = deliveryAddressInput ? deliveryAddressInput.value.trim() : '';
                        if (!addressVal) {
                            showFieldError('delivery_address',
                                'Delivery address / nearest transport hub is required');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = deliveryAddressInput;
                        } else if (addressVal.length < 5) {
                            showFieldError('delivery_address',
                                'Please enter complete address (minimum 5 characters)');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = deliveryAddressInput;
                        } else if (addressVal.length > 250) {
                            showFieldError('delivery_address',
                                'Address must not exceed 250 characters');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = deliveryAddressInput;
                        } else {
                            clearFieldError('delivery_address');
                        }

                        // 6. Validate City
                        const cityVal = cityInput ? cityInput.value.trim() : '';
                        if (!cityVal) {
                            showFieldError('city', 'City / Town name is required');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = cityInput;
                        } else if (cityVal.length < 2) {
                            showFieldError('city', 'Please enter a valid city name (minimum 2 letters)');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = cityInput;
                        } else if (cityVal.length > 50) {
                            showFieldError('city', 'City name must not exceed 50 characters');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = cityInput;
                        } else {
                            clearFieldError('city');
                        }

                        // 7. Validate Pincode
                        const pincodeVal = pincodeInput ? pincodeInput.value.trim() : '';
                        if (!pincodeVal) {
                            showFieldError('pincode', 'Pincode is required');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = pincodeInput;
                        } else if (!/^[0-9]{6}$/.test(pincodeVal)) {
                            showFieldError('pincode', 'Please enter a valid 6-digit postal pincode');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = pincodeInput;
                        } else {
                            clearFieldError('pincode');
                        }

                        // If validation failed, scroll to first error and block submission
                        if (hasError) {
                            e.preventDefault();
                            if (firstErrorEl) {
                                firstErrorEl.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                                firstErrorEl.focus();
                            }
                            DiwaliAlert.warning(
                                'Missing Required Details! ⚠️',
                                'Please fill in all the customer details marked in red before submitting your order.',
                                'Review Details'
                            );
                            return false;
                        }

                        // Prevent double submit
                        if (isOrderSubmitting) {
                            e.preventDefault();
                            return false;
                        }

                        const btn = document.getElementById('submitOrderBtn');
                        if (btn) {
                            isOrderSubmitting = true;
                            btn.disabled = true;
                            btn.classList.add('opacity-75', 'cursor-not-allowed');
                            btn.innerHTML =
                                '<i class="fa-solid fa-spinner fa-spin text-base mr-2"></i> <span>Placing Order... Please wait</span>';
                        }
                    });
                }

                // ==========================================
                // Promotional Banner Carousel Controller
                // ==========================================
                let currentBannerSlide = 0;
                const bannerSlides = document.querySelectorAll('.carousel-slide');
                const bannerDots = document.querySelectorAll('.banner-dot');
                let bannerTimer = null;

                function showBannerSlide(n) {
                    if (!bannerSlides.length) return;
                    currentBannerSlide = (n + bannerSlides.length) % bannerSlides.length;

                    bannerSlides.forEach((slide, idx) => {
                        if (idx === currentBannerSlide) {
                            slide.classList.remove('opacity-0', 'scale-105', 'pointer-events-none', 'z-0');
                            slide.classList.add('opacity-100', 'scale-100', 'z-10');
                        } else {
                            slide.classList.remove('opacity-100', 'scale-100', 'z-10');
                            slide.classList.add('opacity-0', 'scale-105', 'pointer-events-none', 'z-0');
                        }
                    });

                    bannerDots.forEach((dot, idx) => {
                        if (idx === currentBannerSlide) {
                            dot.className =
                                'banner-dot rounded-full transition-all duration-300 cursor-pointer bg-amber-400 w-5 h-2';
                        } else {
                            dot.className =
                                'banner-dot rounded-full transition-all duration-300 cursor-pointer bg-white/60 hover:bg-white w-2 h-2';
                        }
                    });
                }

                window.nextBannerSlide = function() {
                    showBannerSlide(currentBannerSlide + 1);
                    resetBannerTimer();
                };

                window.prevBannerSlide = function() {
                    showBannerSlide(currentBannerSlide - 1);
                    resetBannerTimer();
                };

                window.goToBannerSlide = function(idx) {
                    showBannerSlide(idx);
                    resetBannerTimer();
                };

                function resetBannerTimer() {
                    if (bannerTimer) clearInterval(bannerTimer);
                    if (bannerSlides.length > 1) {
                        bannerTimer = setInterval(() => {
                            showBannerSlide(currentBannerSlide + 1);
                        }, 2000);
                    }
                }

                const carouselSection = document.getElementById('heroCarouselSection');
                if (carouselSection) {

                    // Mobile touch swipe gestures
                    let touchStartX = 0;
                    let touchEndX = 0;
                    carouselSection.addEventListener('touchstart', (e) => {
                        touchStartX = e.changedTouches[0].screenX;
                    }, {
                        passive: true
                    });
                    carouselSection.addEventListener('touchend', (e) => {
                        touchEndX = e.changedTouches[0].screenX;
                        if (touchStartX - touchEndX > 45) {
                            nextBannerSlide();
                        } else if (touchEndX - touchStartX > 45) {
                            prevBannerSlide();
                        }
                    }, {
                        passive: true
                    });

                    resetBannerTimer();
                }
            });
        </script>
    @endpush
@endsection
