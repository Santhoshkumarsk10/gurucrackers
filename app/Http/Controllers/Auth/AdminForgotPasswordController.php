<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use App\Services\WhatsAppOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class AdminForgotPasswordController extends Controller
{
    /**
     * Show form to request password reset via WhatsApp OTP.
     */
    public function showForgotForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.forgot-password');
    }

    /**
     * Generate and dispatch 6-digit OTP to Admin WhatsApp.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|min:3|max:100',
        ], [
            'identifier.required' => 'Please enter your Admin Email or WhatsApp Phone Number.',
        ]);

        $identifier = trim($request->input('identifier'));
        $cleanPhone = preg_replace('/[^0-9]/', '', $identifier);
        if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
            $cleanPhone = substr($cleanPhone, 2);
        } elseif (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        } elseif (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        if (!str_contains($identifier, '@')) {
            if (!preg_match('/^[6-9][0-9]{9}$/', $cleanPhone)) {
                return back()
                    ->withInput()
                    ->withErrors(['identifier' => 'Mobile number must be exactly 10 digits starting with 6, 7, 8, or 9.']);
            }
        }

        $user = User::where('email', $identifier)
            ->orWhere(function ($query) use ($cleanPhone) {
                if (!empty($cleanPhone) && strlen($cleanPhone) === 10) {
                    $query->where('phone', $cleanPhone);
                }
            })
            ->first();

        if (!$user) {
            session([
                'reset_user_id' => 0,
                'reset_masked_phone' => 'your registered number',
            ]);

            return redirect()
                ->route('admin.password.verify')
                ->with('status', 'If an administrator account matches your details, a 6-digit verification code has been dispatched to your WhatsApp number.');
        }

        // Rate limit: 3 attempts per 10 minutes per user
        $rateLimitKey = 'admin-otp-send:' . $user->id;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);
            return back()
                ->withInput()
                ->withErrors(['identifier' => "Too many OTP requests. Please wait {$minutes} minute(s) before requesting again."]);
        }
        RateLimiter::hit($rateLimitKey, 600);

        // Generate cryptographically secure 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // Store OTP in Cache for 10 minutes
        $cacheKey = 'admin_pw_reset_' . $user->id;
        Cache::put($cacheKey, [
            'otp' => $otp,
            'user_id' => $user->id,
            'attempts' => 0,
        ], now()->addMinutes(10));

        // Get recipient phone (fallback to shop phone if user phone is blank)
        $shop = Shop::current();
        $targetPhone = $user->phone ?: ($shop->whatsapp_phone ?: $shop->phone);

        if (empty($targetPhone)) {
            session([
                'reset_user_id' => 0,
                'reset_masked_phone' => 'your registered number',
            ]);
            return redirect()
                ->route('admin.password.verify')
                ->with('status', 'If an administrator account matches your details, a 6-digit verification code has been dispatched to your WhatsApp number.');
        }

        $message = "🔐 *Guru Crackers Admin Password Reset*\n\n"
                 . "Your 6-digit verification code is: *{$otp}*\n\n"
                 . "⏰ Valid for 10 minutes.\n"
                 . "⚠️ Do NOT share this code with anyone.\n\n"
                 . "If you did not request this password reset, please ignore this message.";

        $sendResult = WhatsAppOrderService::sendDirectMessage($targetPhone, $message);

        // Mask phone for privacy in UI: +91 97898 **** 81
        $digitsOnly = preg_replace('/[^0-9]/', '', $targetPhone);
        $maskedPhone = substr($digitsOnly, 0, 4) . ' **** ' . substr($digitsOnly, -2);

        session([
            'reset_user_id' => $user->id,
            'reset_masked_phone' => $maskedPhone,
        ]);

        $notice = "A 6-digit verification OTP has been sent to your WhatsApp number ({$maskedPhone}).";
        if (!$sendResult['success']) {
            $notice .= " (Note: WhatsApp gateway response: " . ($sendResult['error'] ?? 'Pending delivery') . ")";
        }

        return redirect()
            ->route('admin.password.verify')
            ->with('status', $notice);
    }

    /**
     * Show OTP verification and new password form.
     */
    public function showVerifyForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $userId = session('reset_user_id');
        if (!$userId) {
            return redirect()
                ->route('admin.password.request')
                ->withErrors(['identifier' => 'Please enter your Admin Email or Phone first.']);
        }

        $maskedPhone = session('reset_masked_phone', 'your registered number');

        return view('auth.verify-otp-reset', compact('maskedPhone'));
    }

    /**
     * Verify OTP and reset password.
     */
    public function resetPassword(Request $request)
    {
        $userId = session('reset_user_id');
        if ($userId === null || $userId === '') {
            return redirect()
                ->route('admin.password.request')
                ->withErrors(['identifier' => 'Session expired. Please request a new OTP.']);
        }

        $request->validate([
            'otp' => 'required|digits:6',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ], [
            'otp.required' => 'Please enter the 6-digit OTP sent to your WhatsApp.',
            'otp.digits' => 'OTP must be exactly 6 digits.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $cacheKey = 'admin_pw_reset_' . $userId;
        $cachedData = Cache::get($cacheKey);

        if (!$cachedData) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new code.']);
        }

        // Check attempts limit (max 5)
        if (($cachedData['attempts'] ?? 0) >= 5) {
            Cache::forget($cacheKey);
            session()->forget(['reset_user_id', 'reset_masked_phone']);
            return redirect()
                ->route('admin.password.request')
                ->withErrors(['identifier' => 'Too many failed OTP attempts. Please request a new code.']);
        }

        if ($cachedData['otp'] !== trim($request->input('otp'))) {
            $cachedData['attempts'] = ($cachedData['attempts'] ?? 0) + 1;
            Cache::put($cacheKey, $cachedData, now()->addMinutes(10));

            $remaining = 5 - $cachedData['attempts'];
            return back()->withErrors(['otp' => "Incorrect OTP. {$remaining} attempt(s) remaining."]);
        }

        // OTP is correct! Update password
        $user = User::findOrFail($userId);
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Invalidate OTP cache and session
        Cache::forget($cacheKey);
        session()->forget(['reset_user_id', 'reset_masked_phone']);

        // Send confirmation WhatsApp message
        $targetPhone = $user->phone ?: Shop::current()->whatsapp_phone;
        if (!empty($targetPhone)) {
            $alertMsg = "✅ *Guru Crackers Admin Alert*\n\n"
                      . "Your admin password was successfully updated on " . now()->format('d M Y, h:i A') . ".\n\n"
                      . "If you did not perform this change, please immediately secure your account.";
            WhatsAppOrderService::sendDirectMessage($targetPhone, $alertMsg);
        }

        // Automatically log in the user and regenerate session ID
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'Your password has been reset successfully! Welcome back.');
    }
}
