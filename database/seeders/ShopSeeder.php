<?php

namespace Database\Seeders;

use App\Models\Shop;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shop::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Guru Crackers',
                'tagline' => 'Sivakasi Direct Wholesale & Retail Crackers',
                'email' => 'contact@gurucrackers.com',
                'phone' => '+91 98765 43210',
                'secondary_phone' => null,
                'whatsapp_phone' => '+91 98765 43210',
                'website' => 'https://gurucrackers.com',
                'address' => '123, Byepass Road, Near New Bus Stand',
                'city' => 'Sivakasi',
                'pincode' => '626123',
                'offer' => '💥 DIWALI 2026 SPECIAL OFFER | தீபாவளி மெகா தள்ளுபடி',
                'offer_percentage' => 90,
                'banner_notice' => '✨ Sivakasi Direct Factory Prices | 100% Genuine Green Crackers | Mega Festival Discount',
                'is_active' => true,
            ]
        );
    }
}
