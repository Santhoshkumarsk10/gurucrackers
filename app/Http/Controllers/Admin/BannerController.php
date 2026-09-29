<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Display a listing of the promotional banners.
     */
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->orderBy('id', 'desc')->get();
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Store a newly created banner in storage, converting to .webp format.
     */
    public function store(Request $request)
    {
        $desktopImagePath = $this->processUploadedImageData($request, 'image', 'image_base64');
        $mobileImagePath = $this->processUploadedImageData($request, 'mobile_image', 'mobile_image_base64');

        // Desktop banner is required
        if (!$desktopImagePath) {
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_INI_SIZE) {
                return back()
                    ->withErrors(['image' => 'The desktop banner exceeds PHP file size limit. Please re-select the image to auto-compress as WebP.'])
                    ->withInput();
            }

            return back()
                ->withErrors(['image' => 'Please select a valid desktop banner image to upload (1120 × 250 px recommended).'])
                ->withInput();
        }

        $validatedData = $request->validate([
            'title' => 'nullable|string|max:120',
            'link' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $trimmed = trim($value);
                    if ($trimmed === '') return;
                    if (preg_match('/^\s*(?:javascript|data|vbscript):/i', $trimmed)) {
                        $fail('The ' . $attribute . ' contains an insecure URL scheme.');
                        return;
                    }
                    if (!preg_match('/^(?:https?:\/\/|\/|#)/i', $trimmed)) {
                        $fail('The ' . $attribute . ' must start with http://, https://, /, or #.');
                    }
                },
            ],
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'nullable',
        ]);

        Banner::create([
            'title' => $validatedData['title'] ?? null,
            'image' => $desktopImagePath,
            'mobile_image' => $mobileImagePath,
            'link' => $validatedData['link'] ?? null,
            'sort_order' => isset($validatedData['sort_order']) && $validatedData['sort_order'] !== '' ? (int) $validatedData['sort_order'] : (Banner::max('sort_order') + 1),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()
            ->route('admin.banners.index')
            ->with('status', 'Banner converted to .WebP and published successfully!');
    }

    /**
     * Update the specified banner in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $newDesktopImage = $this->processUploadedImageData($request, 'image', 'image_base64');
        $newMobileImage = $this->processUploadedImageData($request, 'mobile_image', 'mobile_image_base64');

        // Delete old desktop image if new one was uploaded
        if ($newDesktopImage && $banner->image && $banner->image !== $newDesktopImage) {
            $this->deleteBannerImageFile($banner->image);
        }

        // Delete old mobile image if new one was uploaded
        if ($newMobileImage && $banner->mobile_image && $banner->mobile_image !== $newMobileImage) {
            $this->deleteBannerImageFile($banner->mobile_image);
        }

        $validatedData = $request->validate([
            'title' => 'nullable|string|max:120',
            'link' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $trimmed = trim($value);
                    if ($trimmed === '') return;
                    if (preg_match('/^\s*(?:javascript|data|vbscript):/i', $trimmed)) {
                        $fail('The ' . $attribute . ' contains an insecure URL scheme.');
                        return;
                    }
                    if (!preg_match('/^(?:https?:\/\/|\/|#)/i', $trimmed)) {
                        $fail('The ' . $attribute . ' must start with http://, https://, /, or #.');
                    }
                },
            ],
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'nullable',
        ]);

        $updateData = [
            'title' => $validatedData['title'] ?? null,
            'link' => $validatedData['link'] ?? null,
            'sort_order' => isset($validatedData['sort_order']) && $validatedData['sort_order'] !== '' ? (int) $validatedData['sort_order'] : $banner->sort_order,
            'is_active' => $request->has('is_active'),
        ];

        if ($newDesktopImage) {
            $updateData['image'] = $newDesktopImage;
        }

        if ($newMobileImage) {
            $updateData['mobile_image'] = $newMobileImage;
        } elseif ($request->boolean('remove_mobile_image')) {
            if ($banner->mobile_image) {
                $this->deleteBannerImageFile($banner->mobile_image);
            }
            $updateData['mobile_image'] = null;
        }

        $banner->update($updateData);

        return redirect()
            ->route('admin.banners.index')
            ->with('status', 'Banner updated & images refreshed successfully!');
    }

    /**
     * Helper to process either client-side compressed base64 or direct multipart file upload.
     */
    private function processUploadedImageData(Request $request, string $fileInputName, string $base64InputName): ?string
    {
        // 1. Process client-converted / compressed base64 image (WebP or JPEG)
        if ($request->filled($base64InputName) && str_starts_with($request->input($base64InputName), 'data:image')) {
            $base64Data = $request->input($base64InputName);
            $parts = explode(',', $base64Data, 2);
            if (isset($parts[1])) {
                $decoded = base64_decode($parts[1]);
                if ($decoded !== false) {
                    return $this->convertAndSaveAsWebp($decoded);
                }
            }
        }

        // 2. Process standard file upload (if uploaded directly)
        if ($request->hasFile($fileInputName) && $request->file($fileInputName)->isValid()) {
            $fileBinary = file_get_contents($request->file($fileInputName)->getRealPath());
            return $this->convertAndSaveAsWebp($fileBinary);
        }

        return null;
    }

    /**
     * Delete an existing banner image file from public storage to keep disk size clean.
     */
    private function deleteBannerImageFile(?string $image): void
    {
        if (empty($image)) {
            return;
        }

        // 1. Direct path check in Storage public disk (e.g. banners/abc.webp)
        if (Storage::disk('public')->exists($image)) {
            Storage::disk('public')->delete($image);
        }

        // 2. Clean path if stored with storage/ or /storage/ prefix
        $cleanPath = ltrim(preg_replace('#^/?(public/|storage/)?#', '', $image), '/');
        if (!empty($cleanPath) && Storage::disk('public')->exists($cleanPath)) {
            Storage::disk('public')->delete($cleanPath);
        }

        // 3. Direct filesystem unlink check
        $absStorage = storage_path('app/public/' . $cleanPath);
        if (is_file($absStorage) && file_exists($absStorage)) {
            @unlink($absStorage);
        }

        $absPublic = public_path('storage/' . $cleanPath);
        if (is_file($absPublic) && file_exists($absPublic)) {
            @unlink($absPublic);
        }
    }

    /**
     * Converts any image binary (PNG, JPG, WebP, etc.) to optimized WebP format and stores it.
     */
    private function convertAndSaveAsWebp(string $binaryData): ?string
    {
        $img = @imagecreatefromstring($binaryData);
        if ($img === false) {
            return null;
        }

        imagepalettetotruecolor($img);
        imagealphablending($img, false);
        imagesavealpha($img, true);

        ob_start();
        imagewebp($img, null, 88);
        $webpData = ob_get_clean();
        imagedestroy($img);

        if (!$webpData) {
            return null;
        }

        $filename = 'banners/' . Str::random(40) . '.webp';
        Storage::disk('public')->put($filename, $webpData);

        return $filename;
    }

    /**
     * Toggle the active status of the specified banner.
     */
    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return redirect()
            ->route('admin.banners.index')
            ->with('status', 'Banner status updated to ' . ($banner->is_active ? 'Active' : 'Inactive'));
    }

    /**
     * Remove the specified banner from storage.
     */
    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            $this->deleteBannerImageFile($banner->image);
        }
        if ($banner->mobile_image) {
            $this->deleteBannerImageFile($banner->mobile_image);
        }

        $banner->delete();

        return redirect()
            ->route('admin.banners.index')
            ->with('status', 'Banner deleted successfully!');
    }
}
