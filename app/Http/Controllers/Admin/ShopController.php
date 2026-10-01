<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    /**
     * Show the form for editing shop details & festival offer.
     */
    public function edit()
    {
        $shop = Shop::current();
        return view('admin.shop.edit', compact('shop'));
    }

    /**
     * Update shop details & festival offer in storage.
     */
    public function update(Request $request)
    {
        $shop = Shop::current();

        if ($request->filled('phone')) {
            $cleanPhone = preg_replace('/[^0-9]/', '', (string) $request->input('phone'));
            if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
                $cleanPhone = substr($cleanPhone, 2);
            } elseif (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                $cleanPhone = substr($cleanPhone, 1);
            } elseif (strlen($cleanPhone) > 10) {
                $cleanPhone = substr($cleanPhone, -10);
            }
            $request->merge(['phone' => !empty($cleanPhone) ? $cleanPhone : null]);
        }

        if ($request->filled('secondary_phone')) {
            $cleanSecPhone = preg_replace('/[^0-9]/', '', (string) $request->input('secondary_phone'));
            if (strlen($cleanSecPhone) === 12 && str_starts_with($cleanSecPhone, '91')) {
                $cleanSecPhone = substr($cleanSecPhone, 2);
            } elseif (strlen($cleanSecPhone) === 11 && str_starts_with($cleanSecPhone, '0')) {
                $cleanSecPhone = substr($cleanSecPhone, 1);
            } elseif (strlen($cleanSecPhone) > 10) {
                $cleanSecPhone = substr($cleanSecPhone, -10);
            }
            $request->merge(['secondary_phone' => !empty($cleanSecPhone) ? $cleanSecPhone : null]);
        }

        if ($request->filled('whatsapp_phone')) {
            $cleanWa = preg_replace('/[^0-9]/', '', (string) $request->input('whatsapp_phone'));
            if (strlen($cleanWa) === 12 && str_starts_with($cleanWa, '91')) {
                $cleanWa = substr($cleanWa, 2);
            } elseif (strlen($cleanWa) === 11 && str_starts_with($cleanWa, '0')) {
                $cleanWa = substr($cleanWa, 1);
            } elseif (strlen($cleanWa) > 10) {
                $cleanWa = substr($cleanWa, -10);
            }
            $request->merge(['whatsapp_phone' => !empty($cleanWa) ? $cleanWa : null]);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'tagline' => 'nullable|string|min:2|max:150',
            'email' => 'nullable|email|min:5|max:80',
            'phone' => ['required', 'string', 'regex:/^[6-9][0-9]{9}$/'],
            'secondary_phone' => ['nullable', 'string', 'regex:/^[6-9][0-9]{9}$/'],
            'whatsapp_phone' => ['nullable', 'string', 'regex:/^[6-9][0-9]{9}$/'],
            'website' => ['nullable', 'string', 'max:150', 'regex:/^https?:\/\//i'],
            'address' => 'nullable|string|min:5|max:250',
            'city' => 'nullable|string|min:2|max:50',
            'pincode' => 'nullable|string|min:6|max:6',
            'offer' => 'nullable|string|min:3|max:150',
            'offer_percentage' => 'nullable|integer|min:0|max:100',
            'min_order_amount' => 'nullable|numeric|min:0',
            'banner_notice' => 'nullable|string|min:5|max:255',
            'upi_id' => 'nullable|string|min:5|max:60',
            'upi_name' => 'nullable|string|min:2|max:60',
            'bank_details' => 'nullable|string|max:500',
            'facebook_url' => ['nullable', 'string', 'max:200', 'regex:/^https?:\/\//i'],
            'instagram_url' => ['nullable', 'string', 'max:200', 'regex:/^https?:\/\//i'],
            'youtube_url' => ['nullable', 'string', 'max:200', 'regex:/^https?:\/\//i'],
            'maps_url' => ['nullable', 'string', 'max:300', 'regex:/^https?:\/\//i'],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'upi_qr_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'phone.required' => 'Primary shop phone number is required.',
            'phone.regex' => 'Primary shop phone must be exactly 10 digits starting with 6, 7, 8, or 9.',
            'secondary_phone.regex' => 'Secondary shop phone must be exactly 10 digits starting with 6, 7, 8, or 9.',
            'whatsapp_phone.regex' => 'Shop WhatsApp phone must be exactly 10 digits starting with 6, 7, 8, or 9.',
            'website.regex' => 'The website URL must start with http:// or https://.',
            'facebook_url.regex' => 'The Facebook URL must start with http:// or https://.',
            'instagram_url.regex' => 'The Instagram URL must start with http:// or https://.',
            'youtube_url.regex' => 'The YouTube URL must start with http:// or https://.',
            'maps_url.regex' => 'The Google Maps URL must start with http:// or https://.',
        ]);

        if ($request->hasFile('logo')) {
            if ($shop->logo && Storage::disk('public')->exists($shop->logo)) {
                Storage::disk('public')->delete($shop->logo);
            }
            $validated['logo'] = $request->file('logo')->store('shops', 'public');
        }

        if ($request->hasFile('upi_qr_image')) {
            if ($shop->upi_qr_image && Storage::disk('public')->exists($shop->upi_qr_image)) {
                Storage::disk('public')->delete($shop->upi_qr_image);
            }
            $validated['upi_qr_image'] = $request->file('upi_qr_image')->store('shops', 'public');
        }

        $shop->update($validated);
        Shop::clearCache();

        return redirect()
            ->route('admin.shop.edit')
            ->with('status', 'Shop details, UPI payment & festival offer updated successfully!');
    }
}
