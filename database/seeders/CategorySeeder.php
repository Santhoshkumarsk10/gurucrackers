<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categoryIcons = [
            'One Sount Crackers' => '⚡',
            'Flower Pots' => '🌸',
            'Ground Chakkaram' => '🌀',
            'Twinkling Star' => '✨',
            'New Veriety' => '🎆',
            'Bijili Crackers' => '💥',
            'Auto Bombs' => '💣',
            'Rockets' => '🚀',
            'Colour Matches' => '🏮',
            'Variety Fountain' => '⛲',
            'Crackling Fountain' => '🎇',
            'Shower Variety' => '🪔',
            'Candle Variety' => '🕯️',
            'Night Variety' => '🌙',
            'Guru' => '👑',
            'Single Shot Variety' => '🎯',
            'Multi Crackers' => '🌌',
            'Giftbox' => '🎁',
            'Sparklers' => '✨',
            'Combo Offer' => '🏷️',
        ];

        // Fetch distinct categories from existing products table
        $categoryNames = Product::select('category')
            ->distinct()
            ->pluck('category');

        $order = 1;
        foreach ($categoryNames as $catName) {
            $icon = $categoryIcons[$catName] ?? '💥';
            $category = Category::firstOrCreate(
                ['name' => $catName],
                [
                    'slug' => Str::slug($catName),
                    'icon' => $icon,
                    'sort_order' => $order++,
                    'is_active' => true,
                ]
            );

            // Update all products with this category string
            Product::where('category', $catName)->update([
                'category_id' => $category->id
            ]);
        }
    }
}
