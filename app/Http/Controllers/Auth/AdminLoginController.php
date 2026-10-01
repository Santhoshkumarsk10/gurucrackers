<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            \App\Services\AuditLogger::logAuth('login', 'Admin logged in: ' . Auth::user()->name . ' (' . Auth::user()->email . ')', Auth::user());

            // If WhatsApp is disconnected, redirect to connect WhatsApp first
            try {
                $waStatus = \App\Services\WhatsAppOrderService::checkServerStatus();
                if (empty($waStatus['connected'])) {
                    return redirect()->route('admin.whatsapp.index')
                        ->with('warning', '⚠️ வாட்ஸ்அப் தற்போது இணைக்கப்படவில்லை! வாடிக்கையாளர் OTP & பில்கள் செல்ல முதலில் WhatsApp-ஐ QR Scan செய்து இணைக்கவும்.');
                }
            } catch (\Throwable $e) {}

            return redirect()->intended(route('admin.dashboard'));
        }

        \App\Services\AuditLogger::log('login', 'auth', 'Failed login attempt with email: ' . $request->input('email'));

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Invalid email or password.']);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            \App\Services\AuditLogger::logAuth('logout', 'Admin logged out: ' . $user->name . ' (' . $user->email . ')', $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
