@extends('layouts.app')

@section('title', 'Shop Details & Festival Offer - Admin')

@section('content')
    <div class="mx-auto space-y-6">

        <!-- Header & Quick Actions -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading flex items-center gap-2">
                    <span>🏪 Shop Details & Offers fdgxx</span>
                    <span class="text-xs bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full font-bold">Live Settings</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Manage your store identity, contact info, website, logo, and active festival discount shown on the
                    customer price list.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('order.create') }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    <i class="fa-solid fa-arrow-up-right-from-square text-slate-500"></i>
                    <span>View Price List</span>
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('admin.shop.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- 1. FESTIVAL OFFER & PROMOTIONS CARD (TOP PRIORITY) --}}
            <div
                class="bg-gradient-to-br from-amber-500/10 via-rose-500/10 to-red-600/10 border-2 border-rose-300 rounded-2xl p-5 sm:p-6 space-y-5 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between border-b border-rose-200/80 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span
                            class="w-9 h-9 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-500 text-white flex items-center justify-center text-base shadow-sm">
                            💥
                        </span>
                        <div>
                            <h2 class="font-black text-base sm:text-lg text-slate-900 font-heading">
                                Festival Offer & Price List Promotion
                            </h2>
                            <p class="text-xs text-slate-600">
                                This offer and discount rate will be prominently displayed on the customer price list
                                banner.
                            </p>
                        </div>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-600 text-white text-[11px] font-extrabold uppercase tracking-wide">
                        Live Display
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Offer Headline -->
                    <div class="md:col-span-2 space-y-1.5">
                        <label for="offer" class="block text-xs font-bold text-slate-700">
                            Offer Headline / Banner Text <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="offer" id="offer" value="{{ old('offer', $shop->offer) }}"
                            required minlength="3" maxlength="150"
                            placeholder="e.g. 💥 DIWALI 2026 SPECIAL OFFER | தீபாவளி மெகா தள்ளுபடி"
                            class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 font-medium transition-all"
                            oninput="updateOfferPreview()">
                        <p class="text-[11px] text-slate-500">Appears in the festive badge and hero banner header.</p>
                    </div>

                    <!-- Offer Discount % -->
                    <div class="space-y-1.5">
                        <label for="offer_percentage" class="block text-xs font-bold text-slate-700">
                            Discount Percentage (%) <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="offer_percentage" id="offer_percentage"
                                value="{{ old('offer_percentage', $shop->offer_percentage) }}" required min="0"
                                max="100" placeholder="e.g. 90"
                                class="w-full pl-3.5 pr-8 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 font-black text-rose-700 transition-all"
                                oninput="updateOfferPreview()">
                            <span
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-sm">%</span>
                        </div>
                        <p class="text-[11px] text-slate-500">e.g. "Up to 90% Discount"</p>
                    </div>

                    <!-- Minimum Order Amount (₹) -->
                    <div class="space-y-1.5">
                        <label for="min_order_amount" class="block text-xs font-bold text-slate-700">
                            Minimum Order Amount (₹) <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-sm">₹</span>
                            <input type="number" name="min_order_amount" id="min_order_amount"
                                value="{{ old('min_order_amount', $shop->getMinOrderAmount()) }}" required min="0" step="50"
                                placeholder="2500"
                                class="w-full pl-8 pr-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 font-black text-slate-900 transition-all">
                        </div>
                        <p class="text-[11px] text-slate-500">Minimum cart amount required to checkout.</p>
                    </div>
                </div>

                <!-- Top Announcement Ticker Message -->
                <div class="space-y-1.5">
                    <label for="banner_notice" class="block text-xs font-bold text-slate-700">
                        Top Announcement Bar Message (Ticker)
                    </label>
                    <input type="text" name="banner_notice" id="banner_notice"
                        value="{{ old('banner_notice', $shop->banner_notice) }}" minlength="5" maxlength="255"
                        placeholder="e.g. ✨ Sivakasi Direct Factory Prices | 100% Genuine Green Crackers | Mega Festival Discount"
                        class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 font-medium transition-all">
                    <p class="text-[11px] text-slate-500">Shown in the thin top ticker at the very top of the website.</p>
                </div>

                <!-- Live Offer Preview Card -->
                <div class="pt-2">
                    <div
                        class="text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-eye text-rose-600"></i> Customer Price List Preview:
                    </div>
                    <div
                        class="rounded-xl bg-gradient-to-r from-rose-800 to-amber-700 text-white p-4 shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-amber-200 text-[11px] font-bold"
                                id="previewOfferBadge">
                                {{ $shop->offer ?? '💥 DIWALI 2026 SPECIAL OFFER' }}
                            </div>
                            <div class="text-sm font-bold text-rose-100">
                                Direct factory wholesale rates with up to <strong class="text-amber-300 font-black"
                                    id="previewDiscountText">{{ $shop->offer_percentage ?? 90 }}% Discount</strong>
                            </div>
                        </div>
                        <span
                            class="bg-amber-400 text-slate-950 text-xs font-extrabold px-3 py-1.5 rounded-lg shrink-0 text-center">
                            Active Offer
                        </span>
                    </div>
                </div>
            </div>

            {{-- 2. STORE IDENTITY (NAME, TAGLINE, WEBSITE, LOGO) --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 space-y-5 shadow-sm">
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                    <span
                        class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-store"></i>
                    </span>
                    <div>
                        <h2 class="font-extrabold text-base text-slate-900 font-heading">
                            Store Identity & Branding
                        </h2>
                        <p class="text-xs text-slate-500">Basic brand name, website URL, and store logo.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Shop Name -->
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-bold text-slate-700">
                            Shop Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $shop->name) }}" required
                            minlength="2" maxlength="100" placeholder="e.g. Guru Crackers"
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-semibold text-slate-900 transition-all">
                    </div>

                    <!-- Tagline -->
                    <div class="space-y-1.5">
                        <label for="tagline" class="block text-xs font-bold text-slate-700">
                            Tagline / Subtitle
                        </label>
                        <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $shop->tagline) }}"
                            minlength="2" maxlength="150" placeholder="e.g. Sivakasi Direct Wholesale & Retail Crackers"
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-800 transition-all">
                    </div>

                    <!-- Website URL -->
                    <div class="space-y-1.5">
                        <label for="website" class="block text-xs font-bold text-slate-700">
                            Official Website URL
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-globe absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" name="website" id="website"
                                value="{{ old('website', $shop->website) }}" minlength="5" maxlength="150"
                                placeholder="https://gurucrackers.com"
                                class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-800 transition-all">
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold text-slate-700">
                            Contact Email
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="email" name="email" id="email"
                                value="{{ old('email', $shop->email) }}" minlength="5" maxlength="80"
                                placeholder="contact@gurucrackers.com"
                                class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-800 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Logo Upload & Preview -->
                <div class="pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Shop Logo (Displays on Header & Invoice)
                    </label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div
                            class="w-20 h-20 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0 shadow-inner relative group">
                            @if ($shop->logo_url)
                                <img src="{{ $shop->logo_url }}" alt="Logo" id="logoPreview"
                                    class="w-full h-full object-contain p-1">
                            @else
                                <div id="logoFallback" class="text-center text-slate-400">
                                    <i class="fa-solid fa-image text-2xl mb-1"></i>
                                    <div class="text-[9px] font-bold uppercase">No Logo</div>
                                </div>
                                <img src="" alt="Preview" id="logoPreview"
                                    class="w-full h-full object-contain p-1 hidden">
                            @endif
                        </div>

                        <div class="space-y-1.5 flex-1">
                            <input type="file" name="logo" id="logoInput" accept="image/*"
                                onchange="previewUploadedLogo(event)"
                                class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 transition-all cursor-pointer">
                            <p class="text-[11px] text-slate-500">
                                Supported formats: PNG, JPG, WebP. Recommended square or rectangular logo (max 2MB).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. CONTACT & LOCATION DETAILS --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 space-y-5 shadow-sm">
                <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                    <span
                        class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-phone-volume"></i>
                    </span>
                    <div>
                        <h2 class="font-extrabold text-base text-slate-900 font-heading">
                            Contact & Dispatch Address
                        </h2>
                        <p class="text-xs text-slate-500">Phone numbers for WhatsApp ordering and factory dispatch
                            location.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Phone Number -->
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-bold text-slate-700">
                            Primary Phone Number <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="tel" name="phone" id="phone"
                                value="{{ old('phone', substr(preg_replace('/[^0-9]/', '', $shop->phone), -10)) }}"
                                required minlength="10" maxlength="10" inputmode="numeric" pattern="[6-9][0-9]{9}"
                                placeholder="10-digit mobile (e.g. 9876543210)"
                                title="10-digit mobile starting with 6, 7, 8, or 9"
                                oninput="let v = this.value.replace(/[^0-9]/g, ''); if(v.length > 10 && v.startsWith('91')) v = v.substring(2); if(v.length > 10 && v.startsWith('0')) v = v.substring(1); if(v.length > 10) v = v.slice(-10); this.value = v.slice(0, 10);"
                                class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-900 transition-all">
                        </div>
                    </div>

                    <!-- Secondary Phone -->
                    <div class="space-y-1.5">
                        <label for="secondary_phone" class="block text-xs font-bold text-slate-700">
                            Secondary Phone <span class="text-xs font-normal text-slate-400">(Optional)</span>
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-phone-volume absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="tel" name="secondary_phone" id="secondary_phone"
                                value="{{ old('secondary_phone', $shop->secondary_phone ? substr(preg_replace('/[^0-9]/', '', $shop->secondary_phone), -10) : '') }}"
                                minlength="10" maxlength="10" inputmode="numeric" pattern="[6-9][0-9]{9}"
                                placeholder="Alternate 10 digits (e.g. 9443123456)"
                                title="10-digit mobile starting with 6, 7, 8, or 9"
                                oninput="let v = this.value.replace(/[^0-9]/g, ''); if(v.length > 10 && v.startsWith('91')) v = v.substring(2); if(v.length > 10 && v.startsWith('0')) v = v.substring(1); if(v.length > 10) v = v.slice(-10); this.value = v.slice(0, 10);"
                                class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-900 transition-all">
                        </div>
                    </div>

                    <!-- WhatsApp Phone -->
                    <div class="space-y-1.5">
                        <label for="whatsapp_phone" class="block text-xs font-bold text-slate-700">
                            WhatsApp Number (Quick Connect)
                        </label>
                        <div class="relative">
                            <i
                                class="fa-brands fa-whatsapp absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-600 text-base"></i>
                            <input type="tel" name="whatsapp_phone" id="whatsapp_phone"
                                value="{{ old('whatsapp_phone', substr(preg_replace('/[^0-9]/', '', $shop->whatsapp_phone), -10)) }}"
                                minlength="10" maxlength="10" inputmode="numeric" pattern="[6-9][0-9]{9}"
                                placeholder="10-digit mobile (e.g. 8779981128)"
                                title="10-digit mobile starting with 6, 7, 8, or 9"
                                oninput="let v = this.value.replace(/[^0-9]/g, ''); if(v.length > 10 && v.startsWith('91')) v = v.substring(2); if(v.length > 10 && v.startsWith('0')) v = v.substring(1); if(v.length > 10) v = v.slice(-10); this.value = v.slice(0, 10);"
                                class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium text-slate-900 transition-all">
                        </div>
                    </div>

                    <!-- City -->
                    <div class="sm:col-span-1 lg:col-span-2 space-y-1.5">
                        <label for="city" class="block text-xs font-bold text-slate-700">
                            City / Town <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" name="city" id="city" value="{{ old('city', $shop->city) }}"
                            required minlength="2" maxlength="50" placeholder="Sivakasi"
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-900 transition-all">
                    </div>

                    <!-- Pincode -->
                    <div class="sm:col-span-1 lg:col-span-1 space-y-1.5">
                        <label for="pincode" class="block text-xs font-bold text-slate-700">
                            Pincode
                        </label>
                        <input type="text" name="pincode" id="pincode"
                            value="{{ old('pincode', $shop->pincode) }}" minlength="6" maxlength="6"
                            placeholder="626123"
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-900 transition-all">
                    </div>

                    <!-- Full Address -->
                    <div class="sm:col-span-2 lg:col-span-3 space-y-1.5">
                        <label for="address" class="block text-xs font-bold text-slate-700">
                            Street Address / Landmark
                        </label>
                        <textarea name="address" id="address" rows="2" minlength="5" maxlength="250"
                            placeholder="123, Byepass Road, Near New Bus Stand, Sivakasi"
                            class="w-full px-3.5 py-2 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-900 transition-all">{{ old('address', $shop->address) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- 4. SOCIAL MEDIA & GOOGLE MAPS LINKS --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 space-y-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span
                            class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-share-nodes"></i>
                        </span>
                        <div>
                            <h2 class="font-extrabold text-base text-slate-900 font-heading">
                                Social Media & Location Links
                            </h2>
                            <p class="text-xs text-slate-500">
                                Connect your social channels and Google Maps for customer trust and store directions.
                            </p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-slate-400">
                        Displayed in Footer & Header
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Facebook URL -->
                    <div class="space-y-1.5">
                        <label for="facebook_url"
                            class="block text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <i class="fa-brands fa-facebook text-[#1877F2]"></i> Facebook Page URL
                        </label>
                        <div class="relative">
                            <input type="url" name="facebook_url" id="facebook_url"
                                value="{{ old('facebook_url', $shop->facebook_url) }}" minlength="5" maxlength="200"
                                placeholder="https://facebook.com/your-page"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium text-slate-900 transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400">Full URL e.g. https://facebook.com/gurucrackers</p>
                    </div>

                    <!-- Instagram URL -->
                    <div class="space-y-1.5">
                        <label for="instagram_url"
                            class="block text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <i class="fa-brands fa-instagram text-[#E4405F]"></i> Instagram Profile URL
                        </label>
                        <div class="relative">
                            <input type="url" name="instagram_url" id="instagram_url"
                                value="{{ old('instagram_url', $shop->instagram_url) }}" minlength="5" maxlength="200"
                                placeholder="https://instagram.com/your-handle"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-500 font-medium text-slate-900 transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400">Full URL e.g. https://instagram.com/gurucrackers</p>
                    </div>

                    <!-- YouTube URL -->
                    <div class="space-y-1.5">
                        <label for="youtube_url" class="block text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <i class="fa-brands fa-youtube text-[#FF0000]"></i> YouTube Channel URL
                        </label>
                        <div class="relative">
                            <input type="url" name="youtube_url" id="youtube_url"
                                value="{{ old('youtube_url', $shop->youtube_url) }}" minlength="5" maxlength="200"
                                placeholder="https://youtube.com/@yourchannel"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 font-medium text-slate-900 transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400">Full URL e.g. https://youtube.com/@gurucrackers</p>
                    </div>

                    <!-- Google Maps URL -->
                    <div class="space-y-1.5">
                        <label for="maps_url" class="block text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-map-location-dot text-[#EA4335]"></i> Google Maps Location URL
                        </label>
                        <div class="relative">
                            <input type="url" name="maps_url" id="maps_url"
                                value="{{ old('maps_url', $shop->maps_url) }}" minlength="5" maxlength="300"
                                placeholder="https://maps.google.com/?q=..."
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium text-slate-900 transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400">Google Maps share link or location coordinates</p>
                    </div>
                </div>
            </div>

            {{-- 5. UPI PAYMENT & QR CODE SETTINGS --}}
            <div class="bg-white border-2 border-emerald-200 rounded-2xl p-5 sm:p-6 space-y-5 shadow-sm">
                <div class="flex items-center justify-between border-b border-emerald-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span
                            class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-qrcode"></i>
                        </span>
                        <div>
                            <h2 class="font-extrabold text-base text-slate-900 font-heading">
                                UPI Payment & QR Code Settings
                            </h2>
                            <p class="text-xs text-slate-500">
                                Configure UPI ID and QR code so customers can pay via GPay, PhonePe, Paytm, or BHIM.
                            </p>
                        </div>
                    </div>
                    <div
                        class="inline-flex items-center gap-2 bg-emerald-50/80 border border-emerald-200/80 px-3 py-1.5 rounded-xl">
                        <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider hidden sm:inline">We
                            Accept:</span>
                        @include('partials.payment-accepted-badges')
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- UPI ID -->
                    <div class="space-y-1.5">
                        <label for="upi_id" class="block text-xs font-bold text-slate-700">
                            UPI ID / VPA <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <i
                                class="fa-solid fa-wallet absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" name="upi_id" id="upi_id"
                                value="{{ old('upi_id', $shop->upi_id) }}" minlength="5" maxlength="60"
                                placeholder="e.g. 9789874381@apl or gurucrackers@oksbi"
                                class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono font-bold text-slate-900 transition-all">
                        </div>
                        <p class="text-[11px] text-slate-500">Customers can copy this or scan the generated QR code.</p>
                    </div>

                    <!-- UPI Payee Name -->
                    <div class="space-y-1.5">
                        <label for="upi_name" class="block text-xs font-bold text-slate-700">
                            Payee Name (Account Holder Name)
                        </label>
                        <input type="text" name="upi_name" id="upi_name"
                            value="{{ old('upi_name', $shop->upi_name) }}" minlength="2" maxlength="60"
                            placeholder="e.g. Guru Crackers"
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium text-slate-900 transition-all">
                        <p class="text-[11px] text-slate-500">Displays on the customer's UPI payment screen.</p>
                    </div>

                    <!-- Bank Account Details (Optional) -->
                    <div class="space-y-1.5 sm:col-span-2">
                        <label for="bank_details" class="block text-xs font-bold text-slate-700">
                            Bank Transfer Details (Optional for RTGS / NEFT / IMPS)
                        </label>
                        <textarea name="bank_details" id="bank_details" rows="2" maxlength="500"
                            placeholder="A/C Name: Guru Crackers | A/C No: 1234567890 | Bank: State Bank of India | IFSC: SBIN0001234 | Branch: Sivakasi"
                            class="w-full px-3.5 py-2 text-sm bg-slate-50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium text-slate-900 transition-all">{{ old('bank_details', $shop->bank_details) }}</textarea>
                    </div>
                </div>

                <!-- Custom QR Upload & Preview -->
                <div class="pt-2 border-t border-emerald-100">
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Custom UPI Payment QR Code (Optional)
                    </label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div
                            class="w-24 h-24 rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50/50 flex items-center justify-center overflow-hidden shrink-0 shadow-inner relative">
                            @if ($shop->upi_qr_image_url)
                                <img src="{{ $shop->upi_qr_image_url }}" alt="UPI QR" id="upiQrPreview"
                                    class="w-full h-full object-contain p-1">
                            @else
                                <div id="upiQrFallback" class="text-center text-emerald-600">
                                    <i class="fa-solid fa-qrcode text-3xl mb-1"></i>
                                    <div class="text-[9px] font-bold uppercase">Dynamic QR</div>
                                </div>
                                <img src="" alt="Preview" id="upiQrPreview"
                                    class="w-full h-full object-contain p-1 hidden">
                            @endif
                        </div>

                        <div class="space-y-1.5 flex-1">
                            <input type="file" name="upi_qr_image" id="upiQrInput" accept="image/*"
                                onchange="previewUploadedUpiQr(event)"
                                class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all cursor-pointer">
                            <p class="text-[11px] text-slate-500">
                                Upload your GPay / PhonePe / BharatPe QR screenshot. If left blank, a <strong>Dynamic
                                    Scan-to-Pay QR</strong> will be automatically generated for each order amount!
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Bar -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold text-sm shadow-md shadow-rose-600/20 hover:scale-[1.01] transition-all font-heading">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Save Shop Details, UPI & Live Offer</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function updateOfferPreview() {
            const offerInput = document.getElementById('offer').value;
            const discountInput = document.getElementById('offer_percentage').value;

            const badge = document.getElementById('previewOfferBadge');
            const discountText = document.getElementById('previewDiscountText');

            if (badge) {
                badge.innerText = offerInput ? offerInput : '💥 DIWALI 2026 SPECIAL OFFER';
            }
            if (discountText) {
                discountText.innerText = (discountInput ? discountInput : '90') + '% Discount';
            }
        }

        function previewUploadedLogo(event) {
            const file = event.target.files[0];
            if (file) {
                const preview = document.getElementById('logoPreview');
                const fallback = document.getElementById('logoFallback');
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                if (fallback) fallback.classList.add('hidden');
            }
        }

        function previewUploadedUpiQr(event) {
            const file = event.target.files[0];
            if (file) {
                const preview = document.getElementById('upiQrPreview');
                const fallback = document.getElementById('upiQrFallback');
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                if (fallback) fallback.classList.add('hidden');
            }
        }
    </script>
@endsection
