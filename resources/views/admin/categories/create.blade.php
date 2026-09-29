@extends('layouts.app')

@section('title', 'Add New Category - Admin')

@section('content')
<div class="max-w-xl mx-auto space-y-5">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-folder-plus"></i>
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading">
                Add New Category
            </h1>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 bg-slate-100 px-3 py-1.5 rounded-lg transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Categories
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
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4 text-xs">
            @csrf

            <!-- Name -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Category Name <span class="text-rose-600">*</span>
                </label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    minlength="2"
                    maxlength="60"
                    placeholder="e.g. Special Crackers, Giant Rockets, Sound Bombs..."
                    class="w-full px-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium"
                >
            </div>

            <!-- Icon / Emoji -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">
                    Category Icon / Emoji
                </label>
                <div class="flex items-center gap-2">
                    <input
                        type="text"
                        name="icon"
                        id="iconInput"
                        value="{{ old('icon', '💥') }}"
                        minlength="1"
                        maxlength="10"
                        class="w-20 text-center text-lg px-2 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500"
                    >
                    <div class="flex flex-wrap items-center gap-1.5 p-1.5 bg-slate-50 border border-slate-200 rounded-xl">
                        @foreach (['💥', '🌸', '🌀', '✨', '💣', '🚀', '🏮', '⛲', '🎇', '🪔', '🕯️', '🌙', '👑', '🎯', '🌌', '🎁', '🏷️', '⚡'] as $emoji)
                            <button
                                type="button"
                                onclick="document.getElementById('iconInput').value = '{{ $emoji }}'"
                                class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white hover:shadow-sm transition-all text-sm"
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
                    value="{{ old('sort_order', $nextOrder) }}"
                    min="0"
                    max="9999"
                    class="w-32 px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-medium"
                >
                <p class="text-[11px] text-slate-400 mt-1">Lower numbers appear first on the order price list.</p>
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
                    <span class="font-bold text-slate-700">Active (Visible on public order page)</span>
                </label>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 font-semibold text-slate-500 hover:text-slate-700">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="bg-gradient-to-r from-rose-600 to-amber-600 hover:from-rose-700 hover:to-amber-700 text-white font-extrabold px-5 py-2.5 rounded-xl shadow-md transition-all font-heading"
                >
                    Save Category
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
