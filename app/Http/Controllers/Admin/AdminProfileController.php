<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\Shop;
use App\Services\AuditLogger;
use App\Services\WhatsAppOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
    /**
     * Show Admin Profile & Account Settings view.
     */
    public function index()
    {
        $user = Auth::user();
        $shop = Shop::current();

        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'account_created' => $user->created_at ? $user->created_at->format('d M Y') : 'N/A',
            'last_login' => AuditLog::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('user_name', $user->name);
            })->where('event', 'login')->latest('id')->first()?->created_at?->diffForHumans() ?? 'Active now',
        ];

        $recentLogs = AuditLog::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('user_name', $user->name);
        })->latest('id')->limit(8)->get();

        return view('admin.profile.index', compact('user', 'shop', 'stats', 'recentLogs'));
    }

    /**
     * Update admin profile details (Name, Email, WhatsApp Phone).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

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

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:190|unique:users,email,' . $user->id,
            'phone' => ['nullable', 'string', 'regex:/^[6-9][0-9]{9}$/'],
        ], [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'phone.regex' => 'Admin WhatsApp phone must be exactly 10 digits starting with 6, 7, 8, or 9.',
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->save();

        AuditLogger::logAuth('profile_update', 'Admin updated profile details (name, email, phone)', $user);

        return back()->with('status', 'Profile details updated successfully!');
    }

    /**
     * Update Admin Password and WhatsApp recovery phone from inside the panel.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

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

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->mixedCase()->numbers()],
            'phone' => ['nullable', 'string', 'regex:/^[6-9][0-9]{9}$/'],
        ], [
            'current_password.required' => 'Please enter your current password.',
            'current_password.current_password' => 'The current password you entered is incorrect.',
            'password.required' => 'Please enter a new password.',
            'password.confirmed' => 'New password confirmation does not match.',
            'password.different' => 'New password must be different from your current password.',
            'phone.regex' => 'Admin WhatsApp recovery phone must be exactly 10 digits starting with 6, 7, 8, or 9.',
        ]);

        $newPassword = $request->input('password');
        $user->password = Hash::make($newPassword);

        if ($request->filled('phone')) {
            $user->phone = $request->input('phone');
        }

        $user->save();

        // Invalidate active sessions on all other devices and refresh current session
        Auth::logoutOtherDevices($newPassword);
        $request->session()->regenerate();

        AuditLogger::logAuth('password_change', 'Admin changed password from settings panel', $user);

        // Send alert on WhatsApp
        $targetPhone = $user->phone ?: Shop::current()->whatsapp_phone;
        if (!empty($targetPhone)) {
            $msg = "✅ *Guru Crackers Security Alert*\n\n"
                 . "Your admin password was changed inside the Admin Panel at " . now()->format('d M Y, h:i A') . ".\n\n"
                 . "If you did not make this change, please reset your password immediately.";
            WhatsAppOrderService::sendDirectMessage($targetPhone, $msg);
        }

        return back()->with('status', 'Password updated successfully!');
    }
}
