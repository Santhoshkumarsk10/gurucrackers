@extends('layouts.app')

@section('title', ($shop->name ?? 'Guru Crackers') . ' - Sivakasi Direct Price List & Online Booking 2026')

@section('content')
    <div class="space-y-6 pb-24 sm:pb-20">

        {{-- ===================== HERO FESTIVE BANNER & CAROUSEL ===================== --}}
        @php
            $activeBanners = isset($banners) && $banners->isNotEmpty() ? $banners : collect();
            $totalHeroSlides = $activeBanners->count() + 1; // Slide 0 is the Festive Countdown Slide!
        @endphp

        <section
            class="relative overflow-hidden rounded-2xl sm:rounded-3xl shadow-xl shadow-rose-900/15 group select-none min-h-[295px] sm:min-h-[275px] md:min-h-[260px] bg-slate-950"
            id="heroCarouselSection">
            <div class="relative w-full h-full min-h-[295px] sm:min-h-[275px] md:min-h-[260px]"
                id="bannerCarouselTrack">

                <!-- SLIDE 0: Diwali 2026 Festival Countdown & Online Booking Closing Card (Default 1st Slide) -->
                <div class="carousel-slide absolute inset-0 w-full h-full transition-all duration-700 ease-out opacity-100 scale-100 z-10 bg-gradient-to-br from-slate-950 via-rose-950 to-slate-900 border border-amber-500/30 p-3 sm:p-5 pb-9 sm:pb-6 flex flex-col justify-center overflow-hidden"
                    data-index="0">

                    <!-- Ambient festive background glows -->
                    <div
                        class="absolute -top-12 -right-12 w-48 h-48 bg-amber-500/15 rounded-full blur-2xl pointer-events-none">
                    </div>
                    <div
                        class="absolute -bottom-12 -left-12 w-48 h-48 bg-rose-600/20 rounded-full blur-2xl pointer-events-none">
                    </div>

                    <div class="relative z-10 space-y-2.5 sm:space-y-3.5 max-w-4xl mx-auto w-full px-1">
                        <!-- Header Badge & Alert Notice -->
                        <div class="flex items-center justify-between gap-1.5 border-b border-white/10 pb-1.5 sm:pb-2">
                            <div
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 sm:py-1 rounded-full bg-amber-500/15 border border-amber-400/30 text-amber-300 text-[10px] sm:text-xs font-extrabold tracking-wide">
                                <span class="animate-bounce">🪔</span>
                                <span>DIWALI 2026 COUNTDOWN</span>
                            </div>
                            <div class="inline-flex items-center gap-1 text-[9px] sm:text-xs font-bold text-rose-300">
                                <i class="fa-solid fa-bolt text-amber-400 text-[10px]"></i>
                                <span>Direct Transport Booking</span>
                            </div>
                        </div>

                        <!-- 3 Core Highlight Columns: Closing Date | Live Countdown Clock | Diwali Date -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-3.5 items-stretch">

                            <!-- 1. Online Booking Closing Date Card -->
                            <div
                                class="col-span-1 order-1 sm:order-1 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl sm:rounded-2xl p-2 sm:p-3 flex items-center gap-2 sm:gap-3 transition-colors min-h-[58px] sm:min-h-[64px]">
                                <div
                                    class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 flex items-center justify-center text-white shrink-0 shadow-md shadow-rose-600/30 text-xs sm:text-base">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span
                                        class="block text-[8px] sm:text-[11px] uppercase font-bold text-rose-300 tracking-wider truncate">
                                        Booking Closes
                                    </span>
                                    <div class="text-xs sm:text-lg font-black text-amber-300 font-heading leading-tight truncate">
                                        25 Oct 2026
                                    </div>
                                    <span class="block text-[8px] sm:text-[10px] text-slate-300 truncate">
                                        பார்சல் கடைசி நாள்
                                    </span>
                                </div>
                            </div>

                            <!-- 2. Interactive Digital Live Countdown Clock -->
                            <div
                                class="col-span-2 sm:col-span-1 order-3 sm:order-2 bg-black/60 border border-amber-400/30 rounded-xl sm:rounded-2xl px-2.5 py-2 sm:p-3 text-center shadow-inner flex flex-col justify-center">
                                <div
                                    class="text-[9px] sm:text-[11px] uppercase tracking-wider font-bold text-amber-200/90 mb-1.5 flex items-center justify-center gap-1">
                                    <i class="fa-regular fa-clock text-amber-400 text-xs"></i>
                                    <span>Time Left to Order (மீதமுள்ள நேரம்)</span>
                                </div>
                                <div class="flex items-center justify-center gap-1.5 sm:gap-2">
                                    <!-- Days -->
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="bg-slate-900/95 border border-amber-400/25 rounded-lg sm:rounded-xl px-2 sm:px-2.5 py-0.5 sm:py-1 min-w-[44px] sm:min-w-[54px] text-center shadow-md">
                                            <span id="cdDays"
                                                class="block text-base sm:text-2xl font-black text-amber-300 font-heading leading-tight">00</span>
                                        </div>
                                        <span
                                            class="block text-[8px] sm:text-[9px] uppercase font-bold text-slate-400 mt-0.5">Days</span>
                                    </div>
                                    <span class="text-amber-400 font-black text-xs sm:text-base -mt-3 select-none">:</span>
                                    <!-- Hours -->
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="bg-slate-900/95 border border-amber-400/25 rounded-lg sm:rounded-xl px-2 sm:px-2.5 py-0.5 sm:py-1 min-w-[44px] sm:min-w-[54px] text-center shadow-md">
                                            <span id="cdHours"
                                                class="block text-base sm:text-2xl font-black text-amber-300 font-heading leading-tight">00</span>
                                        </div>
                                        <span
                                            class="block text-[8px] sm:text-[9px] uppercase font-bold text-slate-400 mt-0.5">Hours</span>
                                    </div>
                                    <span class="text-amber-400 font-black text-xs sm:text-base -mt-3 select-none">:</span>
                                    <!-- Mins -->
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="bg-slate-900/95 border border-amber-400/25 rounded-lg sm:rounded-xl px-2 sm:px-2.5 py-0.5 sm:py-1 min-w-[44px] sm:min-w-[54px] text-center shadow-md">
                                            <span id="cdMins"
                                                class="block text-base sm:text-2xl font-black text-amber-300 font-heading leading-tight">00</span>
                                        </div>
                                        <span
                                            class="block text-[8px] sm:text-[9px] uppercase font-bold text-slate-400 mt-0.5">Mins</span>
                                    </div>
                                    <span class="text-amber-400 font-black text-xs sm:text-base -mt-3 select-none">:</span>
                                    <!-- Secs -->
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="bg-slate-900/95 border border-amber-400/25 rounded-lg sm:rounded-xl px-2 sm:px-2.5 py-0.5 sm:py-1 min-w-[44px] sm:min-w-[54px] text-center shadow-md">
                                            <span id="cdSecs"
                                                class="block text-base sm:text-2xl font-black text-rose-400 font-heading leading-tight">00</span>
                                        </div>
                                        <span
                                            class="block text-[8px] sm:text-[9px] uppercase font-bold text-slate-400 mt-0.5">Secs</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Diwali Festival Date Card -->
                            <div
                                class="col-span-1 order-2 sm:order-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl sm:rounded-2xl p-2 sm:p-3 flex items-center gap-2 sm:gap-3 transition-colors min-h-[58px] sm:min-h-[64px]">
                                <div
                                    class="w-8 h-8 sm:w-11 sm:h-11 rounded-lg sm:rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-400 flex items-center justify-center text-slate-950 shrink-0 shadow-md shadow-amber-500/30 text-xs sm:text-base">
                                    <span>🪔</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span
                                        class="block text-[8px] sm:text-[11px] uppercase font-bold text-amber-300 tracking-wider truncate">
                                        Diwali Festival
                                    </span>
                                    <div class="text-xs sm:text-lg font-black text-amber-200 font-heading leading-tight truncate">
                                        08 Nov 2026
                                    </div>
                                    <span class="block text-[8px] sm:text-[10px] text-slate-300 truncate">
                                        தீபாவளி பண்டிகை
                                    </span>
                                </div>
                            </div>

                        </div>

                        <!-- Bottom Motivation Banner -->
                        <div
                            class="flex items-center justify-center gap-1.5 text-center text-[10px] sm:text-xs text-amber-200 font-medium px-2 leading-tight">
                            <span class="text-amber-400 animate-pulse shrink-0">🔥</span>
                            <span class="line-clamp-1 sm:line-clamp-none">விரைந்து ஆர்டர் செய்யுங்கள்! சிவகாசி பேக்கிங் நெரிசலை தவிர்க்க முந்துங்கள்!</span>
                        </div>
                    </div>
                </div>

                <!-- SLIDES 1..N: Promotional Banners -->
                @foreach ($activeBanners as $idx => $banner)
                    <div class="carousel-slide absolute inset-0 w-full h-full transition-all duration-700 ease-out opacity-0 scale-105 pointer-events-none z-0 bg-slate-950 overflow-hidden"
                        data-index="{{ $idx + 1 }}">
                        <a href="{{ $banner->safe_link }}" class="block w-full h-full cursor-pointer"
                            title="{{ $banner->title ?: 'Diwali Offer' }}">
                            <picture class="block w-full h-full">
                                @if (!empty($banner->mobile_image))
                                    <source media="(max-width: 640px)" srcset="{{ $banner->mobile_image_url }}">
                                @endif
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title ?: 'Festival Cracker Banner' }}"
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
                <!-- Prev / Next Arrows (Desktop & Tablet) -->
                {{-- <button type="button" onclick="prevBannerSlide()"
                        class="hidden sm:flex absolute left-2.5 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/50 hover:bg-black/80 text-white items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm shadow-md"
                        aria-label="Previous Slide">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <button type="button" onclick="nextBannerSlide()"
                        class="hidden sm:flex absolute right-2.5 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/50 hover:bg-black/80 text-white items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm shadow-md"
                        aria-label="Next Slide">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button> --}}

                <!-- Dots Indicators -->
                <div
                    class="absolute bottom-2 sm:bottom-2.5 left-1/2 -translate-x-1/2 z-30 flex items-center gap-1.5 bg-black/60 backdrop-blur-sm px-2.5 py-1 rounded-full border border-white/10 shadow-lg">
                    @for ($i = 0; $i < $totalHeroSlides; $i++)
                        <button type="button" onclick="goToBannerSlide({{ $i }})"
                            class="banner-dot rounded-full transition-all duration-300 cursor-pointer {{ $i === 0 ? 'bg-amber-400 w-5 h-2' : 'bg-white/60 hover:bg-white w-2 h-2' }}"
                            aria-label="Go to slide {{ $i + 1 }}"></button>
                    @endfor
                </div>
            @endif
        </section>


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
            <input type="hidden" name="is_otp_verified" id="isOtpVerifiedInput" value="0">

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
                                    <div>
                                        <span
                                            class="text-sm sm:text-base font-extrabold text-slate-900 font-heading block">Net
                                            Order Amount:</span>
                                        <span
                                            class="text-[10px] sm:text-[11px] font-bold text-amber-800 bg-amber-100/90 px-1.5 py-0.5 rounded inline-block mt-0.5">
                                            Without Delivery Charges (டெலிவரி கட்டணம் தனி)
                                        </span>
                                    </div>
                                    <span class="text-xl sm:text-2xl font-black text-rose-700 font-heading"
                                        id="summaryPayableTotal">₹0.00</span>
                                </div>

                                <!-- Delivery Charges Notice Card -->
                                <div
                                    class="rounded-xl p-2.5 bg-amber-50/90 border border-amber-300/80 text-xs flex items-start gap-2.5">
                                    <i class="fa-solid fa-truck-fast text-amber-600 text-sm mt-0.5 shrink-0"></i>
                                    <div class="leading-tight">
                                        <div class="font-extrabold text-amber-950 flex items-center justify-between gap-1">
                                            <span>Delivery Charges (டெலிவரி கட்டணம்):</span>
                                            <span
                                                class="text-[9px] font-bold bg-amber-200 text-amber-900 px-1.5 py-0.5 rounded shrink-0">Extra
                                                / To Pay at Hub</span>
                                        </div>
                                        <p class="text-[11px] text-slate-600 mt-1 leading-snug">
                                            Delivery charges may differ depending on the transport partner (டிரான்ஸ்போர்ட்
                                            நிறுவனத்தைப் பொறுத்து டெலிவரி கட்டணம் மாறுபடும்). பார்சல் உங்கள் ஊர் கிளைக்கு
                                            வந்ததும் இந்த கட்டணத்தைச் செலுத்தி பெற்றுக்கொள்ளலாம்.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Minimum Order Value Alert & Live Progress -->
                            @php
                                $minOrderAmt = $shop->getMinOrderAmount();
                            @endphp
                            <div id="minOrderAlertBox"
                                class="rounded-xl p-3 text-xs transition-all border bg-rose-50 border-rose-200 text-rose-900">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-1.5 font-bold">
                                        <i class="fa-solid fa-circle-exclamation text-rose-600" id="minOrderIcon"></i>
                                        <span id="minOrderTitle">Minimum Order:
                                            ₹{{ number_format($minOrderAmt, 0) }}</span>
                                    </div>
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-rose-200 text-rose-800"
                                        id="minOrderBadge">
                                        குறைந்தபட்ச ஆர்டர்
                                    </span>
                                </div>
                                <p class="text-[11px] mt-1 text-slate-600 leading-relaxed" id="minOrderText">
                                    குறைந்தபட்ச ஆர்டர் தொகை <strong>₹{{ number_format($minOrderAmt, 0) }}</strong> ஆகும்.
                                    ஆர்டர் செய்ய கார்ட்டில் மேலும் பட்டாசுகளைச் சேர்க்கவும்.
                                </p>
                                <div class="w-full bg-rose-200/80 rounded-full h-1.5 mt-2 overflow-hidden">
                                    <div id="minOrderProgressBar"
                                        class="bg-rose-600 h-1.5 rounded-full transition-all duration-300"
                                        style="width: 0%;"></div>
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
                                        <div class="flex items-center justify-between min-h-[22px] mb-1.5">
                                            <label
                                                class="block font-bold text-slate-700 text-xs flex items-center gap-1.5 whitespace-nowrap"
                                                for="phone1Input">
                                                <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                                                <span>WhatsApp Mobile No&nbsp;<span class="text-rose-600">*</span></span>
                                            </label>
                                            <span
                                                class="text-[9px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-1.5 py-0.5 rounded shrink-0 whitespace-nowrap">
                                                Active WhatsApp
                                            </span>
                                        </div>
                                        <div class="relative flex rounded-xl border border-slate-200 bg-slate-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-rose-500 focus-within:border-rose-500 transition-all overflow-hidden h-[42px]"
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
                                            <i class="fa-solid fa-circle-info text-emerald-600 text-[10px] shrink-0"></i>
                                            <span class="leading-tight">Invoice &amp; parcel updates will be sent to this
                                                WhatsApp.</span>
                                        </p>
                                        <p id="err-phone1"
                                            class="field-error-msg hidden text-red-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                            <span></span>
                                        </p>
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between min-h-[22px] mb-1.5">
                                            <label
                                                class="block font-bold text-slate-700 text-xs flex items-center gap-1.5 whitespace-nowrap"
                                                for="phone2Input">
                                                <i class="fa-solid fa-phone text-slate-400 text-xs"></i>
                                                <span>Alternate Mobile&nbsp;<span
                                                        class="text-slate-400 font-normal text-[11px]">(Optional)</span></span>
                                            </label>
                                            <span
                                                class="text-[9px] font-bold text-slate-600 bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded shrink-0 whitespace-nowrap">
                                                Voice Call
                                            </span>
                                        </div>
                                        <div class="relative flex rounded-xl border border-slate-200 bg-slate-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-rose-500 focus-within:border-rose-500 transition-all overflow-hidden h-[42px]"
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
                                            <i class="fa-solid fa-phone-volume text-slate-400 text-[10px] shrink-0"></i>
                                            <span class="leading-tight">To call if your WhatsApp number is
                                                unreachable.</span>
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

                                <!-- Delivery Scope Notice -->
                                <div
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/80 text-emerald-800 text-[11px] font-semibold">
                                    <i class="fa-solid fa-truck-fast text-emerald-600 text-xs"></i>
                                    <span>Direct parcel delivery available across <strong>Tamil Nadu only</strong>
                                        (தமிழ்நாடு எல்லைக்குள் மட்டுமே).</span>
                                </div>

                                <!-- Pincode, City / Town & State (Auto-fill on Pincode) -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <div class="flex items-center justify-between min-h-[22px] mb-1.5">
                                            <label class="block font-bold text-slate-700 text-xs" for="pincodeInput">
                                                Pincode&nbsp;<span class="text-rose-600">*</span>
                                            </label>
                                            <span id="pincodeBadgeHint"
                                                class="text-[9px] font-bold text-rose-600 bg-rose-50 border border-rose-200/70 px-1.5 py-0.5 rounded shrink-0 flex items-center gap-1 whitespace-nowrap">
                                                <i class="fa-solid fa-bolt text-[8px] text-amber-500"></i> Auto-fill
                                            </span>
                                        </div>
                                        <div class="relative">
                                            <i
                                                class="fa-solid fa-map-pin absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                            <input type="text" name="pincode" id="pincodeInput"
                                                value="{{ old('pincode') }}" required
                                                placeholder="6-digit PIN (e.g. 626123)" pattern="6[0-4][0-9]{4}"
                                                minlength="6" maxlength="6" inputmode="numeric"
                                                autocomplete="postal-code"
                                                class="w-full pl-8 pr-8 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all font-medium placeholder:text-slate-400 h-[42px]">
                                            <span
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs flex items-center pointer-events-none">
                                                <i id="pincodeSpinner"
                                                    class="fa-solid fa-circle-notch fa-spin text-rose-600 hidden"></i>
                                                <i id="pincodeSuccessIcon"
                                                    class="fa-solid fa-circle-check text-emerald-600 hidden"></i>
                                            </span>
                                        </div>
                                        <p id="err-pincode"
                                            class="field-error-msg hidden text-red-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                            <span></span>
                                        </p>
                                        <p id="pincodeSuccessMsg"
                                            class="hidden text-emerald-700 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check text-[10px] text-emerald-600"></i>
                                            <span id="pincodeSuccessText" class="truncate"></span>
                                        </p>
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between min-h-[22px] mb-1.5">
                                            <label class="block font-bold text-slate-700 text-xs" for="cityInput">
                                                City / Town&nbsp;<span class="text-rose-600">*</span>
                                            </label>
                                            <span class="text-[9px] font-medium text-slate-400 shrink-0 whitespace-nowrap">
                                                Town / City
                                            </span>
                                        </div>
                                        <div class="relative">
                                            <i
                                                class="fa-solid fa-city absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                            <input type="text" name="city" id="cityInput" list="citySuggestions"
                                                value="{{ old('city') }}" required minlength="2" maxlength="50"
                                                placeholder="e.g. Madurai" autocomplete="address-level2"
                                                class="w-full pl-8 pr-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all font-medium placeholder:text-slate-400 h-[42px]">
                                            <datalist id="citySuggestions"></datalist>
                                        </div>
                                        <p id="err-city"
                                            class="field-error-msg hidden text-red-600 text-[11px] font-semibold mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i>
                                            <span></span>
                                        </p>
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between min-h-[22px] mb-1.5">
                                            <label class="block font-bold text-slate-700 text-xs" for="stateInput">
                                                State&nbsp;<span class="text-rose-600">*</span>
                                            </label>
                                            <span
                                                class="text-[9px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200/70 px-1.5 py-0.5 rounded shrink-0 flex items-center gap-1 whitespace-nowrap">
                                                <i class="fa-solid fa-location-dot text-[8px] text-emerald-600"></i> TN
                                                Only
                                            </span>
                                        </div>
                                        <div class="relative">
                                            <i
                                                class="fa-solid fa-map-location-dot absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                            <select name="state" id="stateInput" required autocomplete="address-level1"
                                                class="w-full pl-8 pr-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all font-bold text-slate-800 h-[42px]">
                                                <option value="Tamil Nadu" selected>Tamil Nadu</option>
                                            </select>
                                        </div>
                                        <p id="err-state"
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

                                <div id="minOrderWarningNote"
                                    class="text-xs font-bold text-amber-900 text-center mt-2.5 flex items-center justify-center gap-1.5 bg-amber-50 border border-amber-300/80 py-2 px-3 rounded-xl">
                                    <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                    <span id="minOrderWarningText">குறைந்தபட்ச ஆர்டர் தொகை:
                                        ₹{{ number_format($minOrderAmt, 0) }}</span>
                                </div>

                                <p class="text-[11px] text-slate-500 text-center mt-2 leading-tight">
                                    🚚 <strong>Note:</strong> Total amount is <strong>without delivery charges</strong>.
                                    Delivery charges may differ depending on the transport partner and are payable upon
                                    parcel collection at your local hub.
                                </p>
                                <p class="text-[10px] text-slate-400 text-center mt-1">
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

    {{-- ===================== WHATSAPP OTP VERIFICATION MODAL ===================== --}}
    <div id="otpModalBackdrop" onclick="closeOtpModal()"
        class="hidden fixed inset-0 z-[100] bg-slate-950/80 backdrop-blur-md transition-opacity duration-300 flex items-center justify-center p-4">

        <div id="otpModalCard"
            class="bg-white rounded-3xl border border-slate-100 shadow-2xl max-w-sm sm:max-w-md w-full overflow-hidden relative transform transition-all duration-300 scale-95 opacity-0"
            onclick="event.stopPropagation()">

            <!-- Decorative header background -->
            <div
                class="bg-gradient-to-br from-emerald-600 via-teal-700 to-emerald-900 text-white p-6 relative overflow-hidden text-center">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none">
                </div>
                <div class="absolute -left-6 -top-6 w-24 h-24 bg-amber-400/20 rounded-full blur-xl pointer-events-none">
                </div>

                <!-- Close button -->
                <button type="button" onclick="closeOtpModal()"
                    class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-sm cursor-pointer"
                    title="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <!-- Icon Badge -->
                <div
                    class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center text-2xl text-emerald-300 shadow-inner">
                    <i class="fa-brands fa-whatsapp text-3xl text-emerald-300"></i>
                </div>

                <h3 class="text-lg font-black font-heading text-white tracking-wide">
                    WhatsApp Verification
                </h3>
                <p class="text-xs text-emerald-100/90 mt-1 font-medium">
                    ஆர்டரை உறுதி செய்ய வாட்ஸ்அப் OTP உள்ளிடவும்
                </p>
            </div>

            <!-- Modal Content -->
            <div class="p-6">
                <!-- Phone Info Banner -->
                <div
                    class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-3 mb-5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span
                            class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                            <i class="fa-solid fa-mobile-screen"></i>
                        </span>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Sent to WhatsApp
                            </div>
                            <div id="otpDisplayPhone" class="text-xs font-black text-slate-800 font-mono tracking-wide">
                                +91 ----------</div>
                        </div>
                    </div>
                    <button type="button" onclick="editPhoneFromOtpModal()"
                        class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 bg-white border border-emerald-200 hover:border-emerald-300 px-2.5 py-1 rounded-lg transition-colors flex items-center gap-1 shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-pen text-[10px]"></i> Edit
                    </button>
                </div>

                <!-- OTP Input Boxes (4 Digits) -->
                <div class="mb-4">
                    <label class="block text-center text-xs font-bold text-slate-700 mb-3">
                        Enter 4-Digit Verification Code
                    </label>
                    <div class="flex items-center justify-center gap-2.5 sm:gap-3" id="otpInputsContainer">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-digit-input w-12 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-black font-mono bg-slate-50 border-2 border-slate-200 rounded-2xl focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 focus:outline-none transition-all"
                            data-index="0" autocomplete="one-time-code">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-digit-input w-12 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-black font-mono bg-slate-50 border-2 border-slate-200 rounded-2xl focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 focus:outline-none transition-all"
                            data-index="1">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-digit-input w-12 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-black font-mono bg-slate-50 border-2 border-slate-200 rounded-2xl focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 focus:outline-none transition-all"
                            data-index="2">
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                            class="otp-digit-input w-12 h-14 sm:w-14 sm:h-16 text-center text-xl sm:text-2xl font-black font-mono bg-slate-50 border-2 border-slate-200 rounded-2xl focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 focus:outline-none transition-all"
                            data-index="3">
                    </div>
                </div>

                <!-- Error & Success Status Box -->
                <div id="otpStatusMsg"
                    class="hidden text-center text-xs font-bold py-2.5 px-3 rounded-xl mb-4 transition-all"></div>

                <!-- Verify Button -->
                <button type="button" id="verifyOtpBtn" onclick="submitOtpVerification()"
                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-sm font-heading shadow-lg shadow-emerald-600/25 active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="verifyOtpBtnText">✅ Confirm & Place Order</span>
                    <i id="verifyOtpSpinner" class="fa-solid fa-circle-notch fa-spin hidden text-sm"></i>
                </button>

                <!-- Resend Timer -->
                <div class="text-center mt-4 text-xs font-semibold text-slate-500">
                    <div id="otpTimerBox">
                        Didn't receive code? Resend in <span id="otpCountdown" class="font-bold text-slate-800">30s</span>
                    </div>
                    <button type="button" id="resendOtpBtn" onclick="requestOrderOtp(true)"
                        class="hidden text-emerald-700 hover:text-emerald-800 font-extrabold underline transition-colors cursor-pointer">
                        <i class="fa-solid fa-rotate-right text-[11px] mr-1"></i> Resend OTP on WhatsApp
                    </button>
                </div>

                <!-- Help Note -->
                <p class="text-[11px] text-slate-400 text-center mt-4 leading-relaxed">
                    <i class="fa-solid fa-shield-halved text-emerald-600 mr-1"></i> 100% Secure Diwali Crackers Booking. We
                    only deliver inside Tamil Nadu.
                </p>
            </div>
        </div>
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
                        <span
                            class="text-[9px] font-bold text-amber-300 bg-amber-400/20 border border-amber-400/30 px-1.5 py-0.2 rounded hidden sm:inline shrink-0">Excl.
                            Delivery</span>
                    </div>
                </div>

                <div id="floatingSavingsPill"
                    class="hidden md:inline-flex items-center gap-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full shrink-0">
                    <i class="fa-solid fa-gift text-xs"></i>
                    <span>Save: <span id="floatingSavingsAmount">₹0.00</span></span>
                </div>

                <div id="floatingMinOrderBadge"
                    class="hidden md:inline-flex items-center gap-1 bg-amber-400/20 text-amber-300 border border-amber-400/30 text-[10px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-xs" id="floatingMinOrderIcon"></i>
                    <span id="floatingMinOrderText">Min: ₹{{ number_format($minOrderAmt, 0) }}</span>
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
            let currentPayableTotal = 0;
            const MIN_ORDER_AMOUNT = {{ (float) $shop->getMinOrderAmount() }};

            const minOrderAlertBox = document.getElementById('minOrderAlertBox');
            const minOrderIcon = document.getElementById('minOrderIcon');
            const minOrderTitle = document.getElementById('minOrderTitle');
            const minOrderBadge = document.getElementById('minOrderBadge');
            const minOrderText = document.getElementById('minOrderText');
            const minOrderProgressBar = document.getElementById('minOrderProgressBar');
            const minOrderWarningNote = document.getElementById('minOrderWarningNote');
            const minOrderWarningText = document.getElementById('minOrderWarningText');
            const floatingMinOrderBadge = document.getElementById('floatingMinOrderBadge');
            const floatingMinOrderText = document.getElementById('floatingMinOrderText');
            const floatingMinOrderIcon = document.getElementById('floatingMinOrderIcon');

            function formatINR(val) {
                return '₹' + Number(val).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function updateMinOrderUI(totalPayable) {
                currentPayableTotal = totalPayable;
                const percent = Math.min(100, Math.round((totalPayable / MIN_ORDER_AMOUNT) * 100));
                if (minOrderProgressBar) {
                    minOrderProgressBar.style.width = percent + '%';
                }

                if (totalPayable >= MIN_ORDER_AMOUNT) {
                    if (minOrderAlertBox) {
                        minOrderAlertBox.className =
                            'rounded-xl p-3 text-xs transition-all border bg-emerald-50 border-emerald-200 text-emerald-900';
                    }
                    if (minOrderIcon) minOrderIcon.className = 'fa-solid fa-circle-check text-emerald-600';
                    if (minOrderTitle) minOrderTitle.textContent = 'Minimum Order Met (₹' + Number(MIN_ORDER_AMOUNT)
                        .toLocaleString('en-IN') + '+)';
                    if (minOrderBadge) {
                        minOrderBadge.className =
                            'text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-800';
                        minOrderBadge.textContent = 'தகுதி பெறப்பட்டது ✅';
                    }
                    if (minOrderText) minOrderText.textContent =
                        'சூப்பர்! உங்கள் ஆர்டர் தொகை குறைந்தபட்ச ஆர்டர் தகுதியை எட்டியுள்ளது (' + formatINR(totalPayable) +
                        '). நீங்கள் ஆர்டர் சமர்ப்பிக்கலாம்!';
                    if (minOrderProgressBar) minOrderProgressBar.className =
                        'bg-emerald-600 h-1.5 rounded-full transition-all duration-300';

                    if (minOrderWarningNote) {
                        minOrderWarningNote.className =
                            'text-xs font-bold text-emerald-800 text-center mt-2.5 flex items-center justify-center gap-1.5 bg-emerald-50 border border-emerald-200 py-2 px-3 rounded-xl';
                        if (minOrderWarningText) minOrderWarningText.textContent = 'குறைந்தபட்ச ஆர்டர் தகுதி பெறப்பட்டது (' +
                            formatINR(totalPayable) + ') ✅';
                    }

                    if (floatingMinOrderBadge) {
                        floatingMinOrderBadge.className =
                            'hidden md:inline-flex items-center gap-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full shrink-0';
                        if (floatingMinOrderText) floatingMinOrderText.textContent = 'Min Met ✅';
                        if (floatingMinOrderIcon) floatingMinOrderIcon.className =
                            'fa-solid fa-circle-check text-xs text-emerald-400';
                    }
                } else {
                    const diff = Math.max(0, MIN_ORDER_AMOUNT - totalPayable);
                    if (minOrderAlertBox) {
                        minOrderAlertBox.className =
                            'rounded-xl p-3 text-xs transition-all border bg-amber-50 border-amber-300 text-amber-950';
                    }
                    if (minOrderIcon) minOrderIcon.className = 'fa-solid fa-triangle-exclamation text-amber-600';
                    if (minOrderTitle) minOrderTitle.textContent = 'Minimum Order: ₹' + Number(MIN_ORDER_AMOUNT).toLocaleString(
                        'en-IN');
                    if (minOrderBadge) {
                        minOrderBadge.className = 'text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-200 text-amber-900';
                        minOrderBadge.textContent = '₹' + Number(diff).toLocaleString('en-IN') + ' தேவை';
                    }
                    if (minOrderText) {
                        minOrderText.innerHTML = 'குறைந்தபட்ச ஆர்டர் தொகை <strong>₹' + Number(MIN_ORDER_AMOUNT).toLocaleString(
                                'en-IN') + '</strong>. உங்கள் ஆர்டரை உறுதி செய்ய இன்னும் <strong>' + formatINR(diff) +
                            '</strong> மதிப்புள்ள பட்டாசுகளை Cart-ல் சேர்க்க வேண்டும்.';
                    }
                    if (minOrderProgressBar) minOrderProgressBar.className =
                        'bg-amber-500 h-1.5 rounded-full transition-all duration-300';

                    if (minOrderWarningNote) {
                        minOrderWarningNote.className =
                            'text-xs font-bold text-amber-900 text-center mt-2.5 flex items-center justify-center gap-1.5 bg-amber-50 border border-amber-300/80 py-2 px-3 rounded-xl';
                        if (minOrderWarningText) minOrderWarningText.innerHTML = 'குறைந்தபட்ச ஆர்டர்: ₹' + Number(
                                MIN_ORDER_AMOUNT).toLocaleString('en-IN') + ' (இன்னும் <strong>' + formatINR(diff) +
                            '</strong> தேவை)';
                    }

                    if (floatingMinOrderBadge) {
                        floatingMinOrderBadge.className =
                            'hidden md:inline-flex items-center gap-1 bg-amber-400/20 text-amber-300 border border-amber-400/30 text-[10px] sm:text-[11px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full shrink-0';
                        if (floatingMinOrderText) floatingMinOrderText.textContent = 'Min: ₹' + Number(MIN_ORDER_AMOUNT)
                            .toLocaleString('en-IN');
                        if (floatingMinOrderIcon) floatingMinOrderIcon.className =
                            'fa-solid fa-triangle-exclamation text-xs text-amber-300';
                    }
                }
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

                // Live Update Minimum Order Requirement Alert & Progress
                updateMinOrderUI(totalPayable);

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
            const stateInput = document.getElementById('stateInput');
            const pincodeSpinner = document.getElementById('pincodeSpinner');
            const pincodeSuccessIcon = document.getElementById('pincodeSuccessIcon');
            const pincodeSuccessMsg = document.getElementById('pincodeSuccessMsg');
            const pincodeSuccessText = document.getElementById('pincodeSuccessText');
            const citySuggestions = document.getElementById('citySuggestions');

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

                    // If primary WhatsApp phone is changed, invalidate previous OTP verification
                    if (fieldId === 'phone1') {
                        const verifiedEl = document.getElementById('isOtpVerifiedInput');
                        if (verifiedEl) verifiedEl.value = '0';
                    }

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
                    // Letters, spaces, dots, hyphens, and brackets allowed
                    this.value = this.value.replace(/[^a-zA-Z0-9\s.()/-]/g, '');
                    if (this.value.trim().length >= 2) clearFieldError('city');
                });
            }

            let lastFetchedPincode = '';
            let pincodeLookupTimer = null;

            function autoLookupPincode(pin, isInitialLoad = false) {
                if (!pin || pin.length !== 6) return;

                // Restrict strictly to Tamil Nadu (Pincodes 600000 - 649999)
                if (!/^6[0-4][0-9]{4}$/.test(pin)) {
                    if (pincodeSpinner) pincodeSpinner.classList.add('hidden');
                    if (pincodeSuccessIcon) pincodeSuccessIcon.classList.add('hidden');
                    if (pincodeSuccessMsg) pincodeSuccessMsg.classList.add('hidden');
                    showFieldError('pincode', 'Delivery is available inside Tamil Nadu only. (Pincode: 60xxxx - 64xxxx)');
                    return;
                }

                if (!isInitialLoad && pin === lastFetchedPincode) return;

                if (pincodeSpinner) pincodeSpinner.classList.remove('hidden');
                if (pincodeSuccessIcon) pincodeSuccessIcon.classList.add('hidden');
                if (pincodeSuccessMsg) pincodeSuccessMsg.classList.add('hidden');

                const requestedPin = pin;
                fetch(`/api/pincode/${pin}`)
                    .then(response => response.json())
                    .then(data => {
                        if (pincodeInput && pincodeInput.value !== requestedPin) return; // Stale request

                        if (pincodeSpinner) pincodeSpinner.classList.add('hidden');

                        if (data && data.success && data.is_serviceable !== false) {
                            lastFetchedPincode = requestedPin;
                            if (pincodeSuccessIcon) pincodeSuccessIcon.classList.remove('hidden');

                            // 1. Auto-fill City / Town
                            if (cityInput) {
                                if (!isInitialLoad || !cityInput.value.trim()) {
                                    cityInput.value = data.city || data.district || '';
                                    cityInput.classList.add('ring-2', 'ring-emerald-400', 'bg-emerald-50/50');
                                    setTimeout(() => {
                                        cityInput.classList.remove('ring-2', 'ring-emerald-400',
                                        'bg-emerald-50/50');
                                    }, 1200);
                                }
                                clearFieldError('city');
                            }

                            // 2. Populate suggestions datalist
                            if (citySuggestions && Array.isArray(data.places)) {
                                citySuggestions.innerHTML = '';
                                data.places.forEach(place => {
                                    const opt = document.createElement('option');
                                    opt.value = place;
                                    citySuggestions.appendChild(opt);
                                });
                            }

                            // 3. Match & Auto-select State (Tamil Nadu)
                            if (stateInput) {
                                stateInput.value = 'Tamil Nadu';
                                clearFieldError('state');
                            }

                            // 4. Show auto-detected badge
                            if (pincodeSuccessMsg && pincodeSuccessText) {
                                pincodeSuccessText.textContent = `Auto-detected: ${data.city || data.district}, Tamil Nadu`;
                                pincodeSuccessMsg.classList.remove('hidden');
                            }
                        } else {
                            if (pincodeSuccessIcon) pincodeSuccessIcon.classList.add('hidden');
                            if (pincodeSuccessMsg) pincodeSuccessMsg.classList.add('hidden');
                            const errMsg = (data && data.message) ? data.message :
                                'Delivery is available inside Tamil Nadu only.';
                            showFieldError('pincode', errMsg);
                        }
                    })
                    .catch(err => {
                        console.warn('Pincode auto-lookup error:', err);
                        if (pincodeSpinner) pincodeSpinner.classList.add('hidden');
                        if (pincodeSuccessIcon) pincodeSuccessIcon.classList.add('hidden');
                        if (pincodeSuccessMsg) pincodeSuccessMsg.classList.add('hidden');
                    });
            }

            if (pincodeInput) {
                pincodeInput.addEventListener('input', function() {
                    // Digits only, max 6
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
                    if (this.value.length < 6) {
                        lastFetchedPincode = '';
                        if (pincodeSpinner) pincodeSpinner.classList.add('hidden');
                        if (pincodeSuccessIcon) pincodeSuccessIcon.classList.add('hidden');
                        if (pincodeSuccessMsg) pincodeSuccessMsg.classList.add('hidden');
                    } else if (this.value.length === 6) {
                        clearFieldError('pincode');
                        clearTimeout(pincodeLookupTimer);
                        pincodeLookupTimer = setTimeout(() => {
                            autoLookupPincode(this.value);
                        }, 120);
                    }
                });

                pincodeInput.addEventListener('change', function() {
                    if (this.value.length === 6) {
                        autoLookupPincode(this.value);
                    }
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

            // ==========================================
            // WhatsApp OTP Verification Modal Controller
            // ==========================================
            let otpCountdownInterval = null;
            let lastOtpSentPhone = '';
            let lastOtpSentTimestamp = 0;

            window.openOtpModal = function() {
                const modalBackdrop = document.getElementById('otpModalBackdrop');
                const modalCard = document.getElementById('otpModalCard');
                const displayPhone = document.getElementById('otpDisplayPhone');
                const digitInputs = document.querySelectorAll('.otp-digit-input');

                if (!modalBackdrop || !modalCard) return;

                const rawPhone = phone1Input ? phone1Input.value.trim() : '';
                if (displayPhone) {
                    displayPhone.textContent = rawPhone ? `+91 ${rawPhone}` : '+91 ----------';
                }

                modalBackdrop.classList.remove('hidden');
                modalBackdrop.classList.add('flex');
                document.body.style.overflow = 'hidden';

                setTimeout(() => {
                    modalBackdrop.classList.add('opacity-100');
                    modalCard.classList.remove('scale-95', 'opacity-0');
                    modalCard.classList.add('scale-100', 'opacity-100');

                    if (digitInputs.length > 0) {
                        digitInputs[0].focus();
                        digitInputs[0].select();
                    }
                }, 20);
            };

            window.closeOtpModal = function() {
                const modalBackdrop = document.getElementById('otpModalBackdrop');
                const modalCard = document.getElementById('otpModalCard');

                if (!modalBackdrop || !modalCard) return;

                modalCard.classList.remove('scale-100', 'opacity-100');
                modalCard.classList.add('scale-95', 'opacity-0');
                modalBackdrop.classList.remove('opacity-100');

                setTimeout(() => {
                    modalBackdrop.classList.add('hidden');
                    modalBackdrop.classList.remove('flex');
                    document.body.style.overflow = '';
                }, 250);
            };

            window.editPhoneFromOtpModal = function() {
                closeOtpModal();
                if (phone1Input) {
                    phone1Input.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    setTimeout(() => {
                        phone1Input.focus();
                        phone1Input.select();
                    }, 300);
                }
            };

            function showOtpStatus(type, message) {
                const statusBox = document.getElementById('otpStatusMsg');
                if (!statusBox) return;

                statusBox.className = 'text-center text-xs font-bold py-2.5 px-3 rounded-xl mb-4 transition-all';
                if (type === 'error') {
                    statusBox.classList.add('bg-rose-50', 'text-rose-700', 'border', 'border-rose-200');
                    statusBox.innerHTML = `<i class="fa-solid fa-triangle-exclamation mr-1.5"></i> ${message}`;
                } else if (type === 'success') {
                    statusBox.classList.add('bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200');
                    statusBox.innerHTML = `<i class="fa-solid fa-circle-check mr-1.5"></i> ${message}`;
                } else {
                    statusBox.classList.add('bg-slate-50', 'text-slate-700', 'border', 'border-slate-200');
                    statusBox.innerHTML = message;
                }
                statusBox.classList.remove('hidden');
            }

            function clearOtpStatus() {
                const statusBox = document.getElementById('otpStatusMsg');
                if (statusBox) {
                    statusBox.classList.add('hidden');
                    statusBox.innerHTML = '';
                }
            }

            function startOtpCountdown(seconds = 30) {
                clearInterval(otpCountdownInterval);
                const timerBox = document.getElementById('otpTimerBox');
                const resendBtn = document.getElementById('resendOtpBtn');
                const countdownEl = document.getElementById('otpCountdown');

                if (timerBox) timerBox.classList.remove('hidden');
                if (resendBtn) resendBtn.classList.add('hidden');

                let remaining = seconds;
                if (countdownEl) countdownEl.textContent = `${remaining}s`;

                otpCountdownInterval = setInterval(() => {
                    remaining--;
                    if (countdownEl) countdownEl.textContent = `${remaining}s`;
                    if (remaining <= 0) {
                        clearInterval(otpCountdownInterval);
                        if (timerBox) timerBox.classList.add('hidden');
                        if (resendBtn) {
                            resendBtn.classList.remove('hidden');
                            resendBtn.disabled = false;
                        }
                    }
                }, 1000);
            }

            window.requestOrderOtp = async function(isResend = false) {
                const phone = phone1Input ? phone1Input.value.trim() : '';
                const name = nameInput ? nameInput.value.trim() : '';
                const resendBtn = document.getElementById('resendOtpBtn');
                const digitInputs = document.querySelectorAll('.otp-digit-input');
                const displayPhone = document.getElementById('otpDisplayPhone');

                if (!phone || !/^[6-9][0-9]{9}$/.test(phone)) {
                    showOtpStatus('error', 'Please enter a valid 10-digit WhatsApp number.');
                    return;
                }

                // If not resending and we already sent an OTP within the last 30s for the exact same phone:
                const now = Date.now();
                if (!isResend && phone === lastOtpSentPhone && (now - lastOtpSentTimestamp < 30000)) {
                    const remainingSec = Math.ceil((30000 - (now - lastOtpSentTimestamp)) / 1000);
                    showOtpStatus('success', 'Verification code already sent to WhatsApp. Please enter the 4 digits.');
                    startOtpCountdown(remainingSec);
                    return;
                }

                if (isResend && resendBtn) {
                    resendBtn.disabled = true;
                    resendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Resending...';
                }

                clearOtpStatus();
                digitInputs.forEach(input => input.value = '');
                if (digitInputs.length > 0) digitInputs[0].focus();

                try {
                    const response = await fetch("{{ route('order.send_otp') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value ||
                                '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            phone1: phone,
                            name: name
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        lastOtpSentPhone = phone;
                        lastOtpSentTimestamp = Date.now();
                        showOtpStatus('success', data.message || 'OTP sent to your WhatsApp number!');
                        startOtpCountdown(data.cooldown || 30);
                        if (data.masked_phone && displayPhone) {
                            displayPhone.textContent = data.masked_phone;
                        }
                        if (data.debug_otp) {
                            console.log('%c[GURU CRACKERS OTP] Debug Code: ' + data.debug_otp,
                                'color: #10b981; font-weight: bold; font-size: 14px;');
                        }
                    } else {
                        showOtpStatus('error', data.message || 'Failed to send OTP. Please try again.');
                        if (data.cooldown) {
                            startOtpCountdown(30);
                        } else if (resendBtn) {
                            resendBtn.classList.remove('hidden');
                            resendBtn.disabled = false;
                        }
                    }
                } catch (err) {
                    console.error('OTP request error:', err);
                    showOtpStatus('error', 'Network error. Please check your internet connection and try again.');
                    if (resendBtn) {
                        resendBtn.classList.remove('hidden');
                        resendBtn.disabled = false;
                    }
                } finally {
                    if (isResend && resendBtn) {
                        resendBtn.innerHTML =
                            '<i class="fa-solid fa-rotate-right text-[11px] mr-1"></i> Resend OTP on WhatsApp';
                    }
                }
            };

            let isVerifyingOtp = false;
            window.submitOtpVerification = async function() {
                if (isVerifyingOtp) return;
                const phone = phone1Input ? phone1Input.value.trim() : '';
                const digitInputs = document.querySelectorAll('.otp-digit-input');
                const otp = Array.from(digitInputs).map(input => input.value.trim()).join('');
                const verifyBtn = document.getElementById('verifyOtpBtn');
                const verifyBtnText = document.getElementById('verifyOtpBtnText');
                const verifySpinner = document.getElementById('verifyOtpSpinner');
                const verifiedInput = document.getElementById('isOtpVerifiedInput');

                if (otp.length !== 4) {
                    showOtpStatus('error', 'Please enter all 4 digits of the OTP sent to your WhatsApp.');
                    const firstEmpty = Array.from(digitInputs).find(input => !input.value.trim());
                    if (firstEmpty) firstEmpty.focus();
                    return;
                }

                isVerifyingOtp = true;
                if (verifyBtn) {
                    verifyBtn.disabled = true;
                    if (verifySpinner) verifySpinner.classList.remove('hidden');
                    if (verifyBtnText) verifyBtnText.textContent = 'Verifying...';
                }
                clearOtpStatus();

                try {
                    const response = await fetch("{{ route('order.verify_otp') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value ||
                                '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            phone1: phone,
                            otp: otp
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        showOtpStatus('success', 'Mobile verified successfully! Placing order...');
                        if (verifiedInput) verifiedInput.value = '1';

                        setTimeout(() => {
                            closeOtpModal();
                            const orderForm = document.getElementById('orderForm');
                            if (orderForm) {
                                if (typeof orderForm.requestSubmit === 'function') {
                                    orderForm.requestSubmit();
                                } else {
                                    const submitBtn = document.getElementById('submitOrderBtn');
                                    if (submitBtn) {
                                        submitBtn.disabled = true;
                                        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                                        submitBtn.innerHTML =
                                            '<i class="fa-solid fa-spinner fa-spin text-base mr-2"></i> <span>Placing Order... Please wait</span>';
                                    }
                                    orderForm.submit();
                                }
                            }
                        }, 500);
                    } else {
                        showOtpStatus('error', data.message || 'Incorrect OTP code. Please try again.');
                        digitInputs.forEach(i => {
                            i.classList.add('border-rose-400', 'bg-rose-50/50');
                            i.value = '';
                        });
                        setTimeout(() => {
                            digitInputs.forEach(i => i.classList.remove('border-rose-400', 'bg-rose-50/50'));
                        }, 1500);
                        if (digitInputs.length > 0) digitInputs[0].focus();

                        if (verifyBtn) {
                            verifyBtn.disabled = false;
                            if (verifySpinner) verifySpinner.classList.add('hidden');
                            if (verifyBtnText) verifyBtnText.textContent = '✅ Confirm & Place Order';
                        }
                        isVerifyingOtp = false;
                    }
                } catch (err) {
                    console.error('OTP verification error:', err);
                    showOtpStatus('error', 'Network error while verifying OTP. Please try again.');
                    if (verifyBtn) {
                        verifyBtn.disabled = false;
                        if (verifySpinner) verifySpinner.classList.add('hidden');
                        if (verifyBtnText) verifyBtnText.textContent = '✅ Confirm & Place Order';
                    }
                    isVerifyingOtp = false;
                }
            };

            // Attach event listeners to OTP 4-digit input boxes
            const otpDigitInputs = document.querySelectorAll('.otp-digit-input');
            otpDigitInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const clean = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = clean ? clean.slice(-1) : '';

                    if (e.target.value && index < otpDigitInputs.length - 1) {
                        otpDigitInputs[index + 1].focus();
                        otpDigitInputs[index + 1].select();
                    }

                    // Auto-submit if all 4 digits are filled
                    const fullOtp = Array.from(otpDigitInputs).map(i => i.value.trim()).join('');
                    if (fullOtp.length === 4) {
                        submitOtpVerification();
                    }
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace') {
                        if (!input.value && index > 0) {
                            otpDigitInputs[index - 1].focus();
                            otpDigitInputs[index - 1].select();
                        }
                    } else if (e.key === 'ArrowLeft' && index > 0) {
                        e.preventDefault();
                        otpDigitInputs[index - 1].focus();
                        otpDigitInputs[index - 1].select();
                    } else if (e.key === 'ArrowRight' && index < otpDigitInputs.length - 1) {
                        e.preventDefault();
                        otpDigitInputs[index + 1].focus();
                        otpDigitInputs[index + 1].select();
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        submitOtpVerification();
                    }
                });

                input.addEventListener('focus', function() {
                    this.select();
                });

                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = (e.clipboardData || window.clipboardData).getData('text') || '';
                    const digits = pastedData.replace(/[^0-9]/g, '').slice(0, 4);
                    if (digits) {
                        digits.split('').forEach((d, i) => {
                            if (otpDigitInputs[i]) otpDigitInputs[i].value = d;
                        });
                        const focusIdx = Math.min(digits.length, otpDigitInputs.length - 1);
                        otpDigitInputs[focusIdx].focus();
                        if (digits.length === 4) {
                            submitOtpVerification();
                        }
                    }
                });
            });

            // Close OTP modal on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    const backdrop = document.getElementById('otpModalBackdrop');
                    if (backdrop && !backdrop.classList.contains('hidden')) {
                        closeOtpModal();
                    }
                }
            });

            // Document Ready & Order Submission
            document.addEventListener('DOMContentLoaded', () => {
                recalcCart();
                updateActiveCategoryDisplay('all');

                // Auto-lookup pincode if already filled (e.g. from old input / validation redirect)
                if (pincodeInput && pincodeInput.value.trim().length === 6) {
                    autoLookupPincode(pincodeInput.value.trim(), true);
                }

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

                        // 6. Validate Pincode
                        const pincodeVal = pincodeInput ? pincodeInput.value.trim() : '';
                        if (!pincodeVal) {
                            showFieldError('pincode', 'Pincode is required');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = pincodeInput;
                        } else if (!/^[0-9]{6}$/.test(pincodeVal)) {
                            showFieldError('pincode', 'Please enter a valid 6-digit postal pincode');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = pincodeInput;
                        } else if (!/^6[0-4][0-9]{4}$/.test(pincodeVal)) {
                            showFieldError('pincode',
                                'Delivery is available inside Tamil Nadu only (Pincode: 60xxxx - 64xxxx)');
                            hasError = true;
                            if (!firstErrorEl) firstErrorEl = pincodeInput;
                        } else {
                            clearFieldError('pincode');
                        }

                        // 7. Validate City
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

                        // Validate Minimum Order Amount
                        if (currentPayableTotal < MIN_ORDER_AMOUNT) {
                            e.preventDefault();
                            const diff = Math.max(0, MIN_ORDER_AMOUNT - currentPayableTotal);
                            DiwaliAlert.warning(
                                'குறைந்தபட்ச ஆர்டர் ₹' + Number(MIN_ORDER_AMOUNT).toLocaleString('en-IN') +
                                '! ⚠️',
                                'எங்கள் இணையதளத்தில் குறைந்தபட்ச ஆர்டர் தொகை ₹' + Number(MIN_ORDER_AMOUNT)
                                .toLocaleString('en-IN') + ' ஆகும். உங்கள் தற்போதைய கார்ட் மதிப்பு ' +
                                formatINR(currentPayableTotal) + ' மட்டுமே உள்ளது.\n\nதயவுசெய்து மேலும் ' +
                                formatINR(diff) +
                                ' மதிப்புள்ள பட்டாசுகளை Cart-ல் சேர்த்து சமர்ப்பிக்கவும்.',
                                'பட்டாசுகளைச் சேர்க்கவும்'
                            );
                            const firstCategory = document.querySelector('.category-section');
                            if (firstCategory) {
                                firstCategory.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }
                            return false;
                        }

                        // 8. Validate WhatsApp Mobile Verification OTP
                        const isVerifiedEl = document.getElementById('isOtpVerifiedInput');
                        if (!isVerifiedEl || isVerifiedEl.value !== '1') {
                            e.preventDefault();
                            openOtpModal();
                            requestOrderOtp(false);
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
                        }, 5000);
                    }
                }

                const carouselSection = document.getElementById('heroCarouselSection');
                if (carouselSection) {
                    // Desktop hover: pause auto-slide on mouseenter, resume on mouseleave
                    carouselSection.addEventListener('mouseenter', () => {
                        if (bannerTimer) clearInterval(bannerTimer);
                    });
                    carouselSection.addEventListener('mouseleave', () => {
                        resetBannerTimer();
                    });

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
