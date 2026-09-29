<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreVisit extends Model
{
    protected $fillable = [
        'session_id',
        'ip_address',
        'visited_date',
        'page_views',
        'device',
        'platform',
        'browser',
        'first_page',
        'last_page',
        'referrer_source',
    ];

    protected $casts = [
        'visited_date' => 'date',
        'page_views' => 'integer',
    ];

    public function scopeToday($query)
    {
        return $query->whereDate('visited_date', Carbon::today());
    }

    public function scopeThisMonth($query)
    {
        return $query->where('visited_date', '>=', Carbon::now()->startOfMonth());
    }

    /**
     * Record or update customer portal visit.
     */
    public static function recordVisit(Request $request): void
    {
        try {
            $today = Carbon::today()->toDateString();
            
            // Get or generate a persistent visitor session identifier
            $sessionId = $request->session()->getId();
            if (empty($sessionId)) {
                $sessionId = md5($request->ip() . '-' . $today);
            }

            $ip = $request->ip();
            $path = '/' . ltrim($request->path(), '/');
            $userAgent = (string) $request->userAgent();

            // Detect device type
            $device = 'desktop';
            if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgent)) {
                $device = 'tablet';
            } elseif (preg_match('/(Mobile|Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini)/i', $userAgent)) {
                $device = 'mobile';
            }

            // Detect Platform / OS
            $platform = 'Other';
            if (stripos($userAgent, 'Android') !== false) {
                $platform = 'Android';
            } elseif (preg_match('/(iPhone|iPad|iPod)/i', $userAgent)) {
                $platform = 'iOS';
            } elseif (stripos($userAgent, 'Windows') !== false) {
                $platform = 'Windows';
            } elseif (stripos($userAgent, 'Macintosh') !== false) {
                $platform = 'macOS';
            } elseif (stripos($userAgent, 'Linux') !== false) {
                $platform = 'Linux';
            }

            // Detect Browser
            $browser = 'Other';
            if (stripos($userAgent, 'Chrome') !== false && stripos($userAgent, 'Edg') === false && stripos($userAgent, 'OPR') === false) {
                $browser = 'Chrome';
            } elseif (stripos($userAgent, 'Safari') !== false && stripos($userAgent, 'Chrome') === false) {
                $browser = 'Safari';
            } elseif (stripos($userAgent, 'Firefox') !== false) {
                $browser = 'Firefox';
            } elseif (stripos($userAgent, 'Edg') !== false) {
                $browser = 'Edge';
            }

            // Detect Referrer / Traffic Source
            $referrer = (string) $request->header('referer');
            $referrerSource = 'Direct / QR Code';
            if (!empty($referrer)) {
                $host = strtolower((string) parse_url($referrer, PHP_URL_HOST));
                $currentHost = strtolower((string) $request->getHost());

                if (!empty($host) && $host !== $currentHost) {
                    if (str_contains($host, 'whatsapp') || str_contains($referrer, 'whatsapp') || str_contains($referrer, 'wa.me')) {
                        $referrerSource = 'WhatsApp';
                    } elseif (str_contains($host, 'google')) {
                        $referrerSource = 'Google';
                    } elseif (str_contains($host, 'facebook') || str_contains($host, 'fb.com')) {
                        $referrerSource = 'Facebook';
                    } elseif (str_contains($host, 'instagram')) {
                        $referrerSource = 'Instagram';
                    } elseif (str_contains($host, 'youtube')) {
                        $referrerSource = 'YouTube';
                    } else {
                        $referrerSource = $host;
                    }
                }
            }

            // Upsert today's visit for this session
            $visit = static::where('visited_date', $today)
                ->where('session_id', $sessionId)
                ->first();

            if ($visit) {
                $visit->increment('page_views');
                $visit->update([
                    'last_page' => $path,
                ]);
            } else {
                static::create([
                    'visited_date' => $today,
                    'session_id' => $sessionId,
                    'ip_address' => $ip,
                    'page_views' => 1,
                    'device' => $device,
                    'platform' => $platform,
                    'browser' => $browser,
                    'first_page' => $path,
                    'last_page' => $path,
                    'referrer_source' => $referrerSource,
                ]);
            }
        } catch (\Throwable $e) {
            // Never break user browsing if tracking fails
            Log::warning('StoreVisit tracking error: ' . $e->getMessage());
        }
    }
}
