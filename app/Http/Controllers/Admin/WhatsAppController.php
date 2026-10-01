<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shop;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppOrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\CsvSanitizer;

class WhatsAppController extends Controller
{
    /**
     * Show WhatsApp Connection Dashboard, Real-time Customer Live Chat & QR Code.
     */
    public function index(Request $request)
    {
        $status = WhatsAppOrderService::checkServerStatus();
        $shop = Shop::current();
        $chats = $status['connected'] ? WhatsAppOrderService::getCustomerChatsList() : [];

        // KPI summary for quick header stats
        $stats = [
            'total_messages' => WhatsAppMessage::count(),
            'total_outbound' => WhatsAppMessage::where('from_me', true)->count(),
            'total_inbound' => WhatsAppMessage::where('from_me', false)->count(),
            'total_customers' => WhatsAppMessage::select('phone')->distinct()->count(),
        ];

        return view('admin.whatsapp.index', compact('status', 'shop', 'chats', 'stats'));
    }

    /**
     * JSON status endpoint for live polling the QR code and connection state.
     */
    public function status()
    {
        return response()->json(WhatsAppOrderService::checkServerStatus());
    }

    /**
     * JSON customer chat conversations list endpoint for live search and live polling.
     */
    public function chats(Request $request)
    {
        $search = $request->query('search');
        $chats = WhatsAppOrderService::getCustomerChatsList($search);

        return response()->json([
            'success' => true,
            'chats' => $chats,
            'total_unread' => collect($chats)->sum('unread_count'),
        ]);
    }

    /**
     * JSON messages thread endpoint for a selected customer phone number.
     */
    public function messages(Request $request)
    {
        $phone = $request->query('phone');
        if (empty($phone)) {
            return response()->json(['success' => false, 'error' => 'Phone parameter is required.'], 400);
        }

        $data = WhatsAppOrderService::getChatMessages($phone);

        return response()->json([
            'success' => true,
            'customer' => $data['customer'],
            'messages' => $data['messages'],
        ]);
    }

    /**
     * Send direct real-time WhatsApp message to a customer.
     */
    public function sendChat(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $validated['phone']);
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $res = WhatsAppOrderService::sendDirectMessage($cleanPhone, $validated['message'], null, 'live_chat');

        if ($res['success']) {
            $msg = WhatsAppMessage::where('message_id', $res['messageId'])->latest()->first();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $msg?->id,
                    'message_id' => $msg?->message_id ?? $res['messageId'],
                    'from_me' => true,
                    'sender_name' => 'Guru Crackers',
                    'message_type' => 'text',
                    'message_text' => $validated['message'],
                    'media_filename' => null,
                    'media_url' => null,
                    'status' => 'sent',
                    'trigger_source' => 'live_chat',
                    'time' => now()->format('h:i A'),
                    'date' => now()->format('d M Y'),
                    'is_today' => true,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $res['error'] ?? 'Failed to send WhatsApp message.',
        ], 422);
    }

    /**
     * Internal webhook called by Node.js microservice upon incoming customer messages.
     */
    public function webhook(Request $request)
    {
        // Enforce loopback check: internal webhook should only be called from local microservice
        if (!in_array($request->ip(), ['127.0.0.1', '::1'])) {
            return response()->json(['error' => 'Forbidden: Internal webhook is restricted to local loopback.'], 403);
        }

        $expectedSecret = (string) config('services.whatsapp.secret', 'gc-whatsapp-internal-2026');
        $receivedSecret = (string) $request->header('X-Internal-Secret', '');

        if (empty($receivedSecret) || !hash_equals($expectedSecret, $receivedSecret)) {
            return response()->json(['error' => 'Unauthorized: Invalid internal secret.'], 401);
        }

        $phone = preg_replace('/[^0-9]/', '', (string) $request->input('phone'));
        $remoteJid = (string) $request->input('remote_jid');

        // Safety fallback: if remote_jid ends with @lid, resolve via auth_info reverse file
        if (str_ends_with($remoteJid, '@lid')) {
            $lidUser = explode('@', $remoteJid)[0];
            $lidUser = explode(':', $lidUser)[0];
            $reverseFile = base_path('whatsapp-service/auth_info/lid-mapping-' . $lidUser . '_reverse.json');
            if (file_exists($reverseFile)) {
                $rawPn = json_decode(file_get_contents($reverseFile), true);
                if ($rawPn) {
                    $cleanPn = preg_replace('/[^0-9]/', '', (string) $rawPn);
                    if (strlen($cleanPn) > 10) {
                        $cleanPn = substr($cleanPn, -10);
                    }
                    if (strlen($cleanPn) === 10) {
                        $phone = $cleanPn;
                    }
                }
            }
        }

        if (strlen($phone) > 10) {
            $phone = substr($phone, -10);
        }

        if (empty($phone)) {
            return response()->json(['ignored' => true]);
        }

        $order = Order::where('phone1', 'like', "%{$phone}%")
            ->orWhere('phone2', 'like', "%{$phone}%")
            ->latest()
            ->first();

        $fromMe = (bool) $request->input('from_me');
        $messageId = $request->input('message_id') ?: ('in_' . uniqid('', true));
        $timestamp = now();
        if ($request->filled('timestamp')) {
            $rawTs = $request->input('timestamp');
            try {
                if (is_numeric($rawTs)) {
                    $timestamp = Carbon::createFromTimestamp((int) $rawTs, config('app.timezone', 'Asia/Kolkata'));
                } else {
                    $timestamp = Carbon::parse($rawTs)->setTimezone(config('app.timezone', 'Asia/Kolkata'));
                }
            } catch (\Throwable $e) {
                $timestamp = now();
            }
        }

        $existing = WhatsAppMessage::where('message_id', $messageId)->first();
        if ($existing) {
            $existing->update([
                'order_id' => $existing->order_id ?: $order?->id,
                'status' => $fromMe ? ($existing->status ?: 'sent') : 'received',
                'sender_name' => $existing->sender_name ?: ($request->input('sender_name') ?: ($order?->name ?? 'Customer')),
                'media_url' => $existing->media_url ?: $request->input('media_url'),
                'media_filename' => $existing->media_filename ?: $request->input('media_filename'),
            ]);
            return response()->json(['success' => true, 'updated' => true]);
        }

        WhatsAppMessage::create([
            'order_id' => $order?->id,
            'message_id' => $messageId,
            'remote_jid' => $request->input('remote_jid'),
            'phone' => $phone,
            'from_me' => $fromMe,
            'sender_name' => $request->input('sender_name') ?: ($order?->name ?? 'Customer'),
            'message_type' => $request->input('message_type', 'text'),
            'message_text' => $request->input('message_text'),
            'media_url' => $request->input('media_url'),
            'media_filename' => $request->input('media_filename'),
            'status' => $fromMe ? 'sent' : 'received',
            'trigger_source' => $fromMe ? 'shop_outbound' : 'customer_reply',
            'is_read' => $fromMe ? true : false,
            'sent_at' => $timestamp,
        ]);

        return response()->json(['success' => true, 'created' => true]);
    }

    /**
     * Send live test WhatsApp message to verify connection.
     */
    public function sendTest(Request $request)
    {
        if ($request->filled('phone')) {
            $cleanPhone = preg_replace('/[^0-9]/', '', (string) $request->input('phone'));
            if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
                $cleanPhone = substr($cleanPhone, 2);
            } elseif (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '0')) {
                $cleanPhone = substr($cleanPhone, 1);
            } elseif (strlen($cleanPhone) > 10) {
                $cleanPhone = substr($cleanPhone, -10);
            }
            $request->merge(['phone' => $cleanPhone]);
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'regex:/^[6-9][0-9]{9}$/'],
            'message' => 'required|string|max:1000',
        ], [
            'phone.required' => 'Recipient mobile number is required.',
            'phone.regex' => 'Recipient mobile number must be exactly 10 digits starting with 6, 7, 8, or 9.',
        ]);

        $res = WhatsAppOrderService::sendDirectMessage($validated['phone'], $validated['message'], null, 'test_message');

        if ($res['success']) {
            return back()->with('status', "Test WhatsApp message sent successfully to {$validated['phone']}! (ID: {$res['messageId']})");
        }

        return back()->withErrors(['whatsapp' => 'Failed to send: ' . ($res['error'] ?? 'Unknown error')]);
    }

    /**
     * 100% WhatsApp Audit Tracking Log Page.
     */
    public function auditLog(Request $request)
    {
        $query = WhatsAppMessage::with('order')->latest('sent_at')->latest('id');

        // Direction filter
        if ($request->filled('direction')) {
            if ($request->direction === 'sent') {
                $query->where('from_me', true);
            } elseif ($request->direction === 'received') {
                $query->where('from_me', false);
            }
        }

        // Trigger source filter
        if ($request->filled('source') && $request->source !== 'all') {
            $query->where('trigger_source', $request->source);
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Message type filter
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('message_type', $request->type);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('sent_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sent_at', '<=', $request->date_to);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%")
                    ->orWhere('message_text', 'like', "%{$search}%")
                    ->orWhere('media_filename', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_number', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        // Compute KPIs
        $totalLogs = (clone $query)->count();
        $totalSent = WhatsAppMessage::where('from_me', true)->count();
        $totalReceived = WhatsAppMessage::where('from_me', false)->count();
        $totalFailed = WhatsAppMessage::where('status', 'failed')->count();
        $successRate = ($totalSent + $totalReceived) > 0
            ? round((($totalSent + $totalReceived - $totalFailed) / ($totalSent + $totalReceived)) * 100, 1)
            : 100;

        $kpis = [
            'total_filtered' => $totalLogs,
            'total_sent' => $totalSent,
            'total_received' => $totalReceived,
            'total_failed' => $totalFailed,
            'success_rate' => $successRate,
        ];

        $logs = $query->paginate(10)->withQueryString();
        $shop = Shop::current();
        $status = WhatsAppOrderService::checkServerStatus();

        // Fetch only sources and statuses that actually have recorded messages to prevent unwanted empty filtering
        $availableSources = WhatsAppMessage::selectRaw('trigger_source, count(*) as count')
            ->whereNotNull('trigger_source')
            ->where('trigger_source', '!=', '')
            ->groupBy('trigger_source')
            ->orderByDesc('count')
            ->get();

        $availableStatuses = WhatsAppMessage::selectRaw('status, count(*) as count')
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();

        return view('admin.whatsapp.audit_log', compact('logs', 'kpis', 'shop', 'status', 'availableSources', 'availableStatuses'));
    }

    /**
     * Export WhatsApp 100% Audit Log to CSV.
     */
    public function exportAuditLog(Request $request): StreamedResponse
    {
        $query = WhatsAppMessage::with('order')->latest('sent_at')->latest('id');

        if ($request->filled('direction')) {
            if ($request->direction === 'sent') {
                $query->where('from_me', true);
            } elseif ($request->direction === 'received') {
                $query->where('from_me', false);
            }
        }
        if ($request->filled('source') && $request->source !== 'all') {
            $query->where('trigger_source', $request->source);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('sent_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sent_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                    ->orWhere('sender_name', 'like', "%{$search}%")
                    ->orWhere('message_text', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($oq) use ($search) {
                        $oq->where('order_number', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        $fileName = 'whatsapp-audit-log-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'Log ID',
                'Date & Time',
                'Direction',
                'Trigger Source',
                'Customer Phone',
                'Customer Name',
                'Order Number',
                'Message Type',
                'Media File',
                'Status',
                'Error Message',
                'Full Message Text',
            ]);

            $query->chunk(200, function ($messages) use ($handle) {
                foreach ($messages as $msg) {
                    fputcsv($handle, CsvSanitizer::sanitizeRow([
                        $msg->id,
                        $msg->sent_at ? $msg->sent_at->format('Y-m-d H:i:s') : $msg->created_at->format('Y-m-d H:i:s'),
                        $msg->from_me ? 'OUTBOUND (Sent)' : 'INBOUND (Received)',
                        strtoupper(str_replace('_', ' ', $msg->trigger_source ?: 'live_chat')),
                        '+91 ' . $msg->phone,
                        $msg->order?->name ?: ($msg->sender_name ?: 'Customer'),
                        $msg->order?->order_number ?: 'N/A',
                        strtoupper($msg->message_type),
                        $msg->media_filename ?: 'N/A',
                        strtoupper($msg->status),
                        $msg->error_message ?: 'None',
                        $msg->message_text,
                    ]));
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Internal webhook called by Node.js microservice to sync session to MySQL.
     */
    public function syncSession(Request $request)
    {
        if (!in_array($request->ip(), ['127.0.0.1', '::1'])) {
            return response()->json(['error' => 'Forbidden: Internal endpoint restricted to local loopback.'], 403);
        }

        $expectedSecret = (string) config('services.whatsapp.secret', 'gc-whatsapp-internal-2026');
        $receivedSecret = (string) $request->header('X-Internal-Secret', '');
        if (empty($receivedSecret) || !hash_equals($expectedSecret, $receivedSecret)) {
            return response()->json(['error' => 'Unauthorized: Invalid internal secret.'], 401);
        }

        $phone = $request->input('phone');
        $res = WhatsAppOrderService::backupSessionToDatabase($phone);

        return response()->json($res);
    }

    /**
     * Internal webhook called by Node.js microservice upon logout to clear MySQL session.
     */
    public function clearSession(Request $request)
    {
        if (!in_array($request->ip(), ['127.0.0.1', '::1'])) {
            return response()->json(['error' => 'Forbidden: Internal endpoint restricted to local loopback.'], 403);
        }

        $expectedSecret = (string) config('services.whatsapp.secret', 'gc-whatsapp-internal-2026');
        $receivedSecret = (string) $request->header('X-Internal-Secret', '');
        if (empty($receivedSecret) || !hash_equals($expectedSecret, $receivedSecret)) {
            return response()->json(['error' => 'Unauthorized: Invalid internal secret.'], 401);
        }

        WhatsAppOrderService::clearStoredSession();

        return response()->json(['success' => true]);
    }

    /**
     * Unlink device / Logout.
     */
    public function logout()
    {
        WhatsAppOrderService::logout();

        return back()->with('status', 'WhatsApp device disconnected. A new QR code is being generated.');
    }
}
