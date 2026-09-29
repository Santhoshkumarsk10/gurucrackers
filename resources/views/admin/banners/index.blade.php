@extends('layouts.app')

@section('title', 'Manage Promotional Banners - Admin')

@section('content')
    <div class="space-y-6 pb-20">

        <!-- Header Section -->
        <div
            class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-600 to-amber-500 text-white flex items-center justify-center text-xl shadow-md shadow-rose-600/20">
                    <i class="fa-solid fa-images"></i>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">
                        Hero Promotional Banners
                    </h1>
                    <p class="text-xs text-slate-500 font-medium">
                        Manage dynamic carousel banners for Desktop (1120&times;250 px) & Mobile (520&times;460 px).
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('order.create') }}" target="_blank"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 shadow-xs">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                    <span>View Order Page</span>
                </a>
                <button type="button" onclick="openUploadBannerModal()"
                    class="px-4 py-2.5 bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-rose-600/20 font-heading flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-plus"></i>
                    <span>Upload New Banner</span>
                </button>
            </div>
        </div>

        <!-- Status Alert -->
        @if (session('status'))
            <div
                class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-sm animate-fadeIn">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Validation Error Alert -->
        @if ($errors->any())
            <div
                class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1.5 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-sm">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                    <span>Upload Issue Detected:</span>
                </div>
                <ul class="list-disc list-inside pl-1 text-[11px] space-y-0.5 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Recommended Banner Specs Card -->
        <div
            class="bg-gradient-to-br from-amber-50/90 via-white to-rose-50/70 p-5 rounded-3xl border-2 border-amber-200/80 shadow-sm relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1 text-[11px] font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-amber-500 text-white">
                            <i class="fa-solid fa-ruler-combined"></i> Recommended Banner Sizes
                        </span>
                        <span class="text-xs font-bold text-slate-800">Responsive Dual Resolution</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div class="bg-white/90 p-3 rounded-2xl border border-amber-200 shadow-2xs">
                            <div class="flex items-center gap-2 text-rose-700 font-extrabold text-xs">
                                <i class="fa-solid fa-desktop text-sm"></i>
                                <span>Desktop View: <strong>1120 &times; 250 px</strong></span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                Ultra-wide horizontal ratio (~4.5:1). Perfect for laptops, PCs and tablets.
                            </p>
                        </div>
                        <div class="bg-white/90 p-3 rounded-2xl border border-indigo-200 shadow-2xs">
                            <div class="flex items-center gap-2 text-indigo-700 font-extrabold text-xs">
                                <i class="fa-solid fa-mobile-screen text-sm"></i>
                                <span>Mobile View: <strong>460 &times; 160 px</strong></span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                Card ratio (~1.13:1). Keeps text, 90% offer badges & cracker photos completely visible
                                without side clipping.
                            </p>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 italic pt-1">
                        * If you don't upload a separate mobile image, the system will automatically fall back to the
                        desktop banner image.
                    </p>
                </div>
                <div class="shrink-0 flex items-center">
                    <button type="button" onclick="openUploadBannerModal()"
                        class="w-full lg:w-auto px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition-all shadow-sm font-heading flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        <span>Upload Banner</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Banners Listing -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span>Active Carousel Banners</span>
                    <span
                        class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-200 text-slate-800">{{ $banners->count() }}</span>
                </h2>
                @if ($banners->isEmpty())
                    <span class="text-xs text-amber-700 bg-amber-100 px-2.5 py-1 rounded-lg font-semibold">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i> No image banners uploaded &mdash; Store card
                        is currently showing exclusively!
                    </span>
                @endif
            </div>

            @if ($banners->isEmpty())
                <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-10 text-center space-y-4">
                    <div
                        class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-500 mx-auto flex items-center justify-center text-3xl">
                        <i class="fa-solid fa-panorama"></i>
                    </div>
                    <div class="space-y-1 max-w-sm mx-auto">
                        <h3 class="text-base font-bold text-slate-900 font-heading">No Promotional Banners Yet</h3>
                        <p class="text-xs text-slate-500">
                            Upload your festival announcement banners (1120&times;250 px Desktop and 520&times;460 px
                            Mobile) to make the store stand out!
                        </p>
                    </div>
                    <button type="button" onclick="openUploadBannerModal()"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md transition-all font-heading cursor-pointer">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload First Banner</span>
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($banners as $banner)
                        <div
                            class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between group hover:shadow-md transition-all">
                            <!-- Desktop Banner Preview -->
                            <div class="relative bg-slate-900 aspect-[112/25] sm:aspect-[16/7] overflow-hidden">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title ?? 'Festival Banner' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <!-- Status Overlay Badge -->
                                <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5 flex-wrap">
                                    @if ($banner->is_active)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500 text-white shadow-sm">
                                            <i class="fa-solid fa-circle text-[7px] animate-pulse"></i> Live
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-700 text-white shadow-sm">
                                            <i class="fa-solid fa-pause"></i> Inactive
                                        </span>
                                    @endif

                                    @if (!empty($banner->mobile_image))
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-600 text-white shadow-sm"
                                            title="Dedicated Mobile Image 520x460 is active">
                                            <i class="fa-solid fa-mobile-screen text-[9px]"></i> 520&times;460
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-black/60 text-slate-200 backdrop-blur-xs"
                                            title="Uses desktop image on mobile">
                                            <i class="fa-solid fa-mobile-screen text-[9px]"></i> Auto
                                        </span>
                                    @endif

                                    <span class="bg-black/60 text-white px-2 py-0.5 rounded-full text-[10px] font-bold">
                                        #{{ $banner->sort_order }}
                                    </span>
                                </div>

                                <!-- Click Link Overlay if exists -->
                                @if (!empty($banner->link))
                                    <div
                                        class="absolute bottom-2 left-2 right-2 bg-black/60 backdrop-blur-sm text-white px-2.5 py-1 rounded-lg text-[10px] font-mono truncate">
                                        <i class="fa-solid fa-link text-amber-300 mr-1"></i> {{ $banner->link }}
                                    </div>
                                @endif
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="text-sm font-bold text-slate-900 line-clamp-1">
                                            {{ $banner->title ?: 'Untitled Festival Banner' }}
                                        </h3>
                                        @if (!empty($banner->mobile_image))
                                            <a href="{{ $banner->mobile_image_url }}" target="_blank"
                                                class="shrink-0 text-[10px] font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100 flex items-center gap-1"
                                                title="Preview Mobile Image">
                                                <i class="fa-solid fa-mobile-screen"></i> Mobile Image
                                            </a>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        Uploaded
                                        {{ $banner->created_at ? $banner->created_at->diffForHumans() : 'Recently' }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                                    <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}"
                                        class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 cursor-pointer {{ $banner->is_active ? 'bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200' }}"
                                            title="{{ $banner->is_active ? 'Pause this banner' : 'Activate this banner' }}">
                                            <i
                                                class="fa-solid {{ $banner->is_active ? 'fa-pause' : 'fa-play' }} text-[10px]"></i>
                                            <span>{{ $banner->is_active ? 'Pause' : 'Activate' }}</span>
                                        </button>
                                    </form>

                                    <div class="flex items-center gap-1.5">
                                        <button type="button" onclick="openEditBannerModal({{ json_encode($banner) }})"
                                            class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs transition-colors cursor-pointer"
                                            title="Edit Banner">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this banner?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs transition-colors cursor-pointer"
                                                title="Delete Banner">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <!-- ==================== UPLOAD BANNER MODAL ==================== -->
    <div id="uploadBannerModal"
        class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 transition-all"
        onclick="closeUploadBannerModal()">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl relative space-y-4 border border-rose-100 animate-fadeIn max-h-[90vh] overflow-y-auto"
            onclick="event.stopPropagation()">
            <button type="button" onclick="closeUploadBannerModal()"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm transition-colors cursor-pointer"
                title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="flex items-center gap-2.5 pr-6">
                <span
                    class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-rose-600 to-amber-500 text-white flex items-center justify-center text-lg shadow-md shadow-rose-600/20">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </span>
                <div>
                    <h3 class="text-lg font-black text-slate-900 font-heading">
                        Upload Festival Banner
                    </h3>
                    <p class="text-xs text-slate-500">
                        Upload Desktop (1120&times;250 px) and Mobile (460&times;160 px) banner images.
                    </p>
                </div>
            </div>

            <form id="uploadBannerForm" method="POST" action="{{ route('admin.banners.store') }}"
                enctype="multipart/form-data" class="space-y-4 pt-1"
                onsubmit="return prepareBannerSubmit(event, 'upload')">
                @csrf
                <!-- Hidden Base64 inputs for auto-compressed images -->
                <input type="hidden" name="image_base64" id="uploadBannerBase64">
                <input type="hidden" name="mobile_image_base64" id="uploadMobileBannerBase64">

                <!-- Dual Image Upload: Desktop & Mobile -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- 1. Desktop Banner File Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-rose-700">
                                <i class="fa-solid fa-desktop"></i> Desktop Banner <span class="text-rose-600">*</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">1120 &times; 250 px</span>
                        </label>
                        <label for="bannerFileInput"
                            class="block relative border-2 border-dashed border-rose-200 hover:border-rose-400 bg-rose-50/20 rounded-2xl p-3 text-center cursor-pointer transition-colors min-h-[170px] flex flex-col justify-center items-center">
                            <input type="file" name="image" id="bannerFileInput"
                                accept="image/jpeg,image/png,image/webp,image/jpg,image/jfif,image/avif" class="hidden"
                                onchange="handleBannerFileSelect(event, 'upload', 'desktop')">
                            <div id="bannerUploadPlaceholder" class="space-y-1.5 py-3">
                                <i class="fa-solid fa-desktop text-2xl text-rose-500"></i>
                                <div class="text-xs text-slate-800 font-bold">
                                    Select Desktop Image
                                </div>
                                <p class="text-[10px] text-slate-400">
                                    Best: <strong>1120 &times; 250 px</strong>
                                </p>
                            </div>
                            <div id="bannerUploadPreviewContainer" class="hidden w-full">
                                <img id="bannerUploadPreview" src="#" alt="Preview"
                                    class="max-h-28 rounded-xl mx-auto object-cover shadow-xs border border-slate-200">
                                <p
                                    class="text-[10px] text-emerald-600 font-bold mt-1.5 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> <span id="uploadFileSizeText">Desktop WebP
                                        ready</span>
                                </p>
                            </div>
                        </label>
                    </div>

                    <!-- 2. Mobile Banner File Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-indigo-700">
                                <i class="fa-solid fa-mobile-screen"></i> Mobile Banner <span
                                    class="text-slate-400 font-normal">(Optional)</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">460 &times; 160 px</span>
                        </label>
                        <label for="mobileBannerFileInput"
                            class="block relative border-2 border-dashed border-indigo-200 hover:border-indigo-400 bg-indigo-50/20 rounded-2xl p-3 text-center cursor-pointer transition-colors min-h-[170px] flex flex-col justify-center items-center">
                            <input type="file" name="mobile_image" id="mobileBannerFileInput"
                                accept="image/jpeg,image/png,image/webp,image/jpg,image/jfif,image/avif" class="hidden"
                                onchange="handleBannerFileSelect(event, 'upload', 'mobile')">
                            <div id="mobileBannerUploadPlaceholder" class="space-y-1.5 py-3">
                                <i class="fa-solid fa-mobile-screen text-2xl text-indigo-500"></i>
                                <div class="text-xs text-slate-800 font-bold">
                                    Select Mobile Image
                                </div>
                                <p class="text-[10px] text-slate-400">
                                    Best: <strong>520 &times; 460 px</strong>
                                </p>
                            </div>
                            <div id="mobileBannerUploadPreviewContainer" class="hidden w-full">
                                <img id="mobileBannerUploadPreview" src="#" alt="Mobile Preview"
                                    class="max-h-28 rounded-xl mx-auto object-cover shadow-xs border border-slate-200">
                                <p
                                    class="text-[10px] text-indigo-600 font-bold mt-1.5 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> <span id="uploadMobileFileSizeText">Mobile
                                        WebP ready</span>
                                </p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Banner Title -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">
                        Banner Title / Label <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <input type="text" name="title" placeholder="e.g. Diwali Mega 90% Wholesale Discount"
                        maxlength="120"
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-900">
                </div>

                <!-- Optional Link URL -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">
                        Click Link / Action URL <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <input type="text" name="link" placeholder="e.g. #categories or https://wa.me/91XXXXXXXXXX"
                        maxlength="255"
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-mono text-slate-900">
                    <p class="text-[10px] text-slate-400">Leave blank to scroll to cracker categories on click.</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Sort Order -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Display Order</label>
                        <input type="number" name="sort_order" value="1" min="0" max="999"
                            class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-bold text-slate-900">
                    </div>

                    <!-- Active Toggle -->
                    <div class="space-y-1 flex flex-col justify-end">
                        <label
                            class="inline-flex items-center gap-2 cursor-pointer p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition-colors">
                            <input type="checkbox" name="is_active" value="1" checked
                                class="w-4 h-4 text-rose-600 rounded focus:ring-rose-500">
                            <span class="text-xs font-bold text-slate-700">Active Live</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" id="uploadBannerSubmitBtn"
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-bold text-xs shadow-md font-heading transition-all cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Upload & Publish to Carousel</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== EDIT BANNER MODAL ==================== -->
    <div id="editBannerModal"
        class="hidden fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 transition-all"
        onclick="closeEditBannerModal()">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 shadow-2xl relative space-y-4 border border-rose-100 animate-fadeIn max-h-[90vh] overflow-y-auto"
            onclick="event.stopPropagation()">
            <button type="button" onclick="closeEditBannerModal()"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-sm transition-colors cursor-pointer"
                title="Close">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="flex items-center gap-2.5 pr-6">
                <span class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-pen-to-square text-rose-600"></i>
                </span>
                <div>
                    <h3 class="text-lg font-black text-slate-900 font-heading">
                        Edit Banner Details
                    </h3>
                    <p class="text-xs text-slate-500">
                        Replace Desktop (1120&times;250 px) or Mobile (520&times;460 px) images.
                    </p>
                </div>
            </div>

            <form id="editBannerForm" method="POST" action="" enctype="multipart/form-data"
                class="space-y-4 pt-1" onsubmit="return prepareBannerSubmit(event, 'edit')">
                @csrf
                @method('PUT')
                <input type="hidden" name="image_base64" id="editBannerBase64">
                <input type="hidden" name="mobile_image_base64" id="editMobileBannerBase64">

                <!-- Dual Image Replace: Desktop & Mobile -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- 1. Desktop Image -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-rose-700">
                                <i class="fa-solid fa-desktop"></i> Desktop Image
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">1120 &times; 250 px</span>
                        </label>
                        <label for="editBannerFileInput"
                            class="block relative border-2 border-dashed border-slate-200 hover:border-rose-400 bg-slate-50/50 rounded-2xl p-3 text-center cursor-pointer transition-colors min-h-[160px] flex flex-col justify-center items-center">
                            <input type="file" name="image" id="editBannerFileInput"
                                accept="image/jpeg,image/png,image/webp,image/jpg,image/jfif,image/avif" class="hidden"
                                onchange="handleBannerFileSelect(event, 'edit', 'desktop')">
                            <img id="editBannerCurrentPreview" src="#" alt="Desktop Preview"
                                class="max-h-24 rounded-xl mx-auto object-cover shadow-xs border border-slate-200">
                            <p class="text-[10px] text-slate-500 font-bold mt-1.5" id="editBannerSizeText">
                                <i class="fa-solid fa-arrow-rotate-right"></i> Click to change desktop image
                            </p>
                        </label>
                    </div>

                    <!-- 2. Mobile Image -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-800 flex items-center justify-between">
                            <span class="flex items-center gap-1.5 text-indigo-700">
                                <i class="fa-solid fa-mobile-screen"></i> Mobile Image
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">520 &times; 460 px</span>
                        </label>
                        <label for="editMobileBannerFileInput"
                            class="block relative border-2 border-dashed border-slate-200 hover:border-indigo-400 bg-slate-50/50 rounded-2xl p-3 text-center cursor-pointer transition-colors min-h-[160px] flex flex-col justify-center items-center">
                            <input type="file" name="mobile_image" id="editMobileBannerFileInput"
                                accept="image/jpeg,image/png,image/webp,image/jpg,image/jfif,image/avif" class="hidden"
                                onchange="handleBannerFileSelect(event, 'edit', 'mobile')">
                            <img id="editMobileBannerCurrentPreview" src="#" alt="Mobile Preview"
                                class="max-h-24 rounded-xl mx-auto object-cover shadow-xs border border-slate-200">
                            <p class="text-[10px] text-slate-500 font-bold mt-1.5" id="editMobileBannerSizeText">
                                <i class="fa-solid fa-arrow-rotate-right"></i> Click to set/replace mobile image
                            </p>
                        </label>
                        <div id="removeMobileImageWrapper" class="hidden pt-1">
                            <label
                                class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-semibold cursor-pointer">
                                <input type="checkbox" name="remove_mobile_image" value="1"
                                    class="rounded text-rose-600 focus:ring-rose-500">
                                <span>Remove custom mobile image (revert to desktop image)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Title -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Banner Title</label>
                    <input type="text" name="title" id="editBannerTitle" maxlength="120"
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium text-slate-900">
                </div>

                <!-- Link -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">Click Link / URL</label>
                    <input type="text" name="link" id="editBannerLink" maxlength="255"
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-mono text-slate-900">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Sort Order -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">Display Order</label>
                        <input type="number" name="sort_order" id="editBannerSortOrder" min="0" max="999"
                            class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-rose-500 font-bold text-slate-900">
                    </div>

                    <!-- Active Toggle -->
                    <div class="space-y-1 flex flex-col justify-end">
                        <label
                            class="inline-flex items-center gap-2 cursor-pointer p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition-colors">
                            <input type="checkbox" name="is_active" id="editBannerIsActive" value="1"
                                class="w-4 h-4 text-rose-600 rounded focus:ring-rose-500">
                            <span class="text-xs font-bold text-slate-700">Active Live</span>
                        </label>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="editBannerSubmitBtn"
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-bold text-xs shadow-md font-heading transition-all cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUploadBannerModal() {
            const m = document.getElementById('uploadBannerModal');
            if (m) {
                m.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeUploadBannerModal() {
            const m = document.getElementById('uploadBannerModal');
            if (m) {
                m.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                // Reset desktop file input
                const fileInput = document.getElementById('bannerFileInput');
                if (fileInput) {
                    fileInput.setAttribute('name', 'image');
                    fileInput.value = '';
                }
                const base64Input = document.getElementById('uploadBannerBase64');
                if (base64Input) base64Input.value = '';
                const previewContainer = document.getElementById('bannerUploadPreviewContainer');
                const placeholder = document.getElementById('bannerUploadPlaceholder');
                if (previewContainer) previewContainer.classList.add('hidden');
                if (placeholder) placeholder.classList.remove('hidden');

                // Reset mobile file input
                const mobFileInput = document.getElementById('mobileBannerFileInput');
                if (mobFileInput) {
                    mobFileInput.setAttribute('name', 'mobile_image');
                    mobFileInput.value = '';
                }
                const mobBase64Input = document.getElementById('uploadMobileBannerBase64');
                if (mobBase64Input) mobBase64Input.value = '';
                const mobPreviewContainer = document.getElementById('mobileBannerUploadPreviewContainer');
                const mobPlaceholder = document.getElementById('mobileBannerUploadPlaceholder');
                if (mobPreviewContainer) mobPreviewContainer.classList.add('hidden');
                if (mobPlaceholder) mobPlaceholder.classList.remove('hidden');

                const submitBtn = document.getElementById('uploadBannerSubmitBtn');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitBtn.innerHTML =
                        '<i class="fa-solid fa-cloud-arrow-up mr-1.5"></i> <span>Upload & Publish to Carousel</span>';
                }
            }
        }

        // Client-side WebP image converter for desktop and mobile banner uploads
        function handleBannerFileSelect(event, mode, target = 'desktop') {
            const file = event.target.files[0];
            if (!file) return;

            let previewEl, base64Input, placeholder, container, sizeText;

            if (mode === 'upload') {
                if (target === 'desktop') {
                    previewEl = document.getElementById('bannerUploadPreview');
                    base64Input = document.getElementById('uploadBannerBase64');
                    placeholder = document.getElementById('bannerUploadPlaceholder');
                    container = document.getElementById('bannerUploadPreviewContainer');
                    sizeText = document.getElementById('uploadFileSizeText');
                } else {
                    previewEl = document.getElementById('mobileBannerUploadPreview');
                    base64Input = document.getElementById('uploadMobileBannerBase64');
                    placeholder = document.getElementById('mobileBannerUploadPlaceholder');
                    container = document.getElementById('mobileBannerUploadPreviewContainer');
                    sizeText = document.getElementById('uploadMobileFileSizeText');
                }
            } else {
                if (target === 'desktop') {
                    previewEl = document.getElementById('editBannerCurrentPreview');
                    base64Input = document.getElementById('editBannerBase64');
                    sizeText = document.getElementById('editBannerSizeText');
                } else {
                    previewEl = document.getElementById('editMobileBannerCurrentPreview');
                    base64Input = document.getElementById('editMobileBannerBase64');
                    sizeText = document.getElementById('editMobileBannerSizeText');
                }
            }

            const submitBtn = (mode === 'upload') ? document.getElementById('uploadBannerSubmitBtn') : document
                .getElementById('editBannerSubmitBtn');

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitBtn.innerHTML =
                    '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> <span>Converting to .WebP...</span>';
            }
            if (sizeText) {
                sizeText.innerHTML =
                    '<i class="fa-solid fa-spinner fa-spin text-amber-500 mr-1"></i> <span class="text-amber-600 font-medium">Processing .WebP...</span>';
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    // Target dimensions based on device:
                    // Desktop: max 1600x500 (recommended 1120x250)
                    // Mobile: max 800x800 (recommended 520x460)
                    const maxW = (target === 'desktop') ? 1600 : 800;
                    const maxH = (target === 'desktop') ? 500 : 800;
                    let width = img.width;
                    let height = img.height;

                    if (width > maxW) {
                        height = Math.round((height * maxW) / width);
                        width = maxW;
                    }
                    if (height > maxH) {
                        width = Math.round((width * maxH) / height);
                        height = maxH;
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    let webpBase64 = canvas.toDataURL('image/webp', 0.88);
                    if (!webpBase64 || !webpBase64.startsWith('data:image/webp')) {
                        webpBase64 = canvas.toDataURL('image/jpeg', 0.88);
                    }

                    if (base64Input) {
                        base64Input.value = webpBase64;
                    }

                    if (previewEl) {
                        previewEl.src = webpBase64;
                    }
                    if (placeholder) placeholder.classList.add('hidden');
                    if (container) container.classList.remove('hidden');

                    const approxKb = Math.round((webpBase64.length * 3 / 4) / 1024);
                    if (sizeText) {
                        sizeText.innerHTML =
                            `<span class="text-emerald-700 font-bold flex items-center justify-center gap-1"><i class="fa-solid fa-circle-check text-emerald-600"></i> ${width}&times;${height}px (${approxKb} KB .WebP)</span>`;
                    }

                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        submitBtn.innerHTML = (mode === 'upload') ?
                            '<i class="fa-solid fa-cloud-arrow-up mr-1.5"></i> <span>Upload & Publish to Carousel</span>' :
                            '<i class="fa-solid fa-floppy-disk mr-1.5"></i> <span>Save Changes</span>';
                    }
                };

                img.onerror = function() {
                    alert('Unable to process the selected image. Please choose another PNG or JPG image.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    }
                };

                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Prepares form submit by stripping raw file inputs if Base64 WebP is generated
        function prepareBannerSubmit(event, mode) {
            const fileInput = (mode === 'upload') ? document.getElementById('bannerFileInput') : document.getElementById(
                'editBannerFileInput');
            const base64Input = (mode === 'upload') ? document.getElementById('uploadBannerBase64') : document
                .getElementById('editBannerBase64');

            const mobFileInput = (mode === 'upload') ? document.getElementById('mobileBannerFileInput') : document
                .getElementById('editMobileBannerFileInput');
            const mobBase64Input = (mode === 'upload') ? document.getElementById('uploadMobileBannerBase64') : document
                .getElementById('editMobileBannerBase64');

            const submitBtn = (mode === 'upload') ? document.getElementById('uploadBannerSubmitBtn') : document
                .getElementById('editBannerSubmitBtn');

            // Desktop banner check
            if (base64Input && base64Input.value && base64Input.value.length > 50) {
                if (fileInput) fileInput.removeAttribute('name');
            } else if (mode === 'upload' && (!fileInput || !fileInput.files || fileInput.files.length === 0)) {
                event.preventDefault();
                alert('Please select a desktop banner image (1120 × 250 px recommended).');
                return false;
            }

            // Mobile banner check
            if (mobBase64Input && mobBase64Input.value && mobBase64Input.value.length > 50) {
                if (mobFileInput) mobFileInput.removeAttribute('name');
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitBtn.innerHTML =
                    '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> <span>Saving .WebP Banner...</span>';
            }

            return true;
        }

        function openEditBannerModal(banner) {
            const m = document.getElementById('editBannerModal');
            if (!m) return;

            document.getElementById('editBannerForm').action = `/admin/banners/${banner.id}`;
            document.getElementById('editBannerTitle').value = banner.title || '';
            document.getElementById('editBannerLink').value = banner.link || '';
            document.getElementById('editBannerSortOrder').value = banner.sort_order || 0;
            document.getElementById('editBannerIsActive').checked = !!banner.is_active;

            // Reset desktop file inputs
            const fileInput = document.getElementById('editBannerFileInput');
            if (fileInput) {
                fileInput.setAttribute('name', 'image');
                fileInput.value = '';
            }
            const base64Input = document.getElementById('editBannerBase64');
            if (base64Input) base64Input.value = '';

            // Set desktop preview
            const imgPreview = document.getElementById('editBannerCurrentPreview');
            if (banner.image) {
                imgPreview.src = banner.image_url || `/storage/${banner.image}`;
            }

            // Reset mobile file inputs
            const mobFileInput = document.getElementById('editMobileBannerFileInput');
            if (mobFileInput) {
                mobFileInput.setAttribute('name', 'mobile_image');
                mobFileInput.value = '';
            }
            const mobBase64Input = document.getElementById('editMobileBannerBase64');
            if (mobBase64Input) mobBase64Input.value = '';

            // Set mobile preview if available
            const mobImgPreview = document.getElementById('editMobileBannerCurrentPreview');
            const removeWrapper = document.getElementById('removeMobileImageWrapper');
            if (banner.mobile_image) {
                mobImgPreview.src = banner.mobile_image_url || `/storage/${banner.mobile_image}`;
                if (removeWrapper) removeWrapper.classList.remove('hidden');
            } else {
                mobImgPreview.src = banner.image_url || `/storage/${banner.image}`;
                if (removeWrapper) removeWrapper.classList.add('hidden');
            }

            const editSizeText = document.getElementById('editBannerSizeText');
            if (editSizeText) {
                editSizeText.innerHTML = '<i class="fa-solid fa-arrow-rotate-right"></i> Click to change desktop image';
            }

            const editMobSizeText = document.getElementById('editMobileBannerSizeText');
            if (editMobSizeText) {
                editMobSizeText.innerHTML = banner.mobile_image ?
                    '<i class="fa-solid fa-arrow-rotate-right"></i> Click to replace mobile image' :
                    '<i class="fa-solid fa-plus"></i> Click to upload custom 520&times;460 mobile image';
            }

            const submitBtn = document.getElementById('editBannerSubmitBtn');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk mr-1.5"></i> <span>Save Changes</span>';
            }

            m.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeEditBannerModal() {
            const m = document.getElementById('editBannerModal');
            if (m) {
                m.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                const fileInput = document.getElementById('editBannerFileInput');
                if (fileInput) {
                    fileInput.setAttribute('name', 'image');
                    fileInput.value = '';
                }
                const base64Input = document.getElementById('editBannerBase64');
                if (base64Input) base64Input.value = '';

                const mobFileInput = document.getElementById('editMobileBannerFileInput');
                if (mobFileInput) {
                    mobFileInput.setAttribute('name', 'mobile_image');
                    mobFileInput.value = '';
                }
                const mobBase64Input = document.getElementById('editMobileBannerBase64');
                if (mobBase64Input) mobBase64Input.value = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeUploadBannerModal();
                closeEditBannerModal();
            }
        });

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', () => {
                openUploadBannerModal();
            });
        @endif
    </script>
@endsection
