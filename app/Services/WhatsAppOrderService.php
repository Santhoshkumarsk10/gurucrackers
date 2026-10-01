<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WhatsAppOrderService
{
    /**
     * Clean and normalize phone number with country code for WhatsApp link.
     */
    public static function normalizePhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Keep only digits
        $digits = preg_replace('/[^0-9]/', '', $phone);

        // If 10 digits (standard Indian mobile), prepend 91
        if (strlen($digits) === 10) {
            return '91' . $digits;
        }

        // If starts with 0 and total 11 digits
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            return '91' . substr($digits, 1);
        }

        return $digits;
    }

    /**
     * Format clean single-line address.
     */
    public static function formatAddress(Order $order): string
    {
        $address = preg_replace("/[,\s]*[\r\n]+[,\s]*/", ", ", trim($order->delivery_address));
        $address = preg_replace("/\s*,\s*/", ", ", $address);
        $address = preg_replace("/(,\s*)+/", ", ", $address);
        $address = trim($address, ", ");

        return $address . ", " . trim($order->city) . " - " . trim($order->pincode);
    }


    /**
     * Format phone display (deduplicating if phone1 equals phone2).
     */
    public static function formatPhone(Order $order): string
    {
        $phone = trim($order->phone1);
        if (!empty($order->phone2) && trim($order->phone2) !== $phone) {
            $phone .= ' / ' . trim($order->phone2);
        }
        return $phone;
    }

    /**
     * Generate Customer Order Invoice WhatsApp Text (Clean, summarized modern format).
     */
    public static function formatCustomerInvoice(Order $order, Shop $shop): string
    {
        $order->loadMissing(['items.product']);

        $shopName = strtoupper(trim($shop->name ?: 'GURU CRACKERS'));
        $shopPhone = $shop->phone ?: '+91 9789874381';
        if (!empty($shop->secondary_phone)) {
            $shopPhone .= ' / ' . $shop->secondary_phone;
        }
        $delivery = self::formatAddress($order);

        $totalVarieties = $order->items->count();
        $totalQuantity = (int) $order->items->sum('quantity');
        $invoiceUrl = route('order.public_invoice', ['orderNumber' => $order->order_number, 'token' => $order->getInvoiceSignature()]);

        $lines = [];
        $lines[] = "✨ *{$shopName} - SIVAKASI* 🪔";
        $lines[] = "*Order Booking Confirmation*";
        $lines[] = "---------------------------------------------";
        $lines[] = "Hello *" . trim($order->name) . "*! 🙏";
        $lines[] = "Thank you for booking your Diwali crackers with us!";
        $lines[] = "";
        $lines[] = "📋 *Order Summary:*";
        $lines[] = "▫️ *Order No:* `#" . $order->order_number . "`";
        $lines[] = "▫️ *Date:* " . $order->created_at->format('d M Y, h:i A');
        $lines[] = "▫️ *Total Items:* *" . $totalVarieties . " " . ($totalVarieties === 1 ? 'Variety' : 'Varieties') . "*";
        $lines[] = "▫️ *Total Quantity:* *" . number_format($totalQuantity) . " Boxes/Packets*";
        $lines[] = "📍 *Delivery Address:*";
        $lines[] = $delivery;
        $lines[] = "";
        $lines[] = "---------------------------------------------";
        $lines[] = "💰 *Total Order Amount:* *Rs." . number_format($order->total_amount, 2) . "* *(Without Delivery Charges)*";
        $lines[] = "🚚 *Delivery Charges:* *Extra (Depends on transport partner)*";
        $lines[] = "   _(டெலிவரி கட்டணம் டிரான்ஸ்போர்ட் நிறுவனத்தைப் பொறுத்து மாறுபடும். பார்சல் அலுவலகத்தில் பெற்றுக்கொள்ளும்போது செலுத்த வேண்டும்)_";
        $lines[] = "---------------------------------------------";

        if (!empty($shop->upi_id)) {
            $lines[] = "";
            $lines[] = "📲 *Easy UPI Payment:*";
            $lines[] = "1️⃣ UPI ID: `" . $shop->upi_id . "`";
            $lines[] = "   *(Tap to copy)*";
            if (!empty($shop->upi_name)) {
                $lines[] = "2️⃣ Payee Name: *" . $shop->upi_name . "*";
            }
            $lines[] = "3️⃣ Pay *Rs." . number_format($order->total_amount, 2) . "* via Google Pay / PhonePe / Paytm and share the payment screenshot in this chat.";
            $lines[] = "📷 *Payment QR Code is attached below for instant scan & pay!*";
            $lines[] = "";
            $lines[] = "🚚 *Parcel Dispatch:*";
            $lines[] = "Once your payment screenshot is received, your order will be packed and dispatched directly from Sivakasi, and your Transport LR receipt (including exact delivery charges) will be shared here!";
        }

        $lines[] = "";
        $lines[] = "📄 *View & Download Full Itemized Invoice PDF:*";
        $lines[] = "👉 " . $invoiceUrl;
        $lines[] = "";
        $lines[] = "Wishing you and your family a Happy & Sparkling Diwali! 🎇✨";
        $lines[] = "*" . ($shop->name ?: 'Guru Crackers') . ", Sivakasi* | 📞 " . $shopPhone;

        return implode("\n", $lines);
    }

    /**
     * Generate Admin Order Notification WhatsApp Text (Concise summary alert for Admin/Warehouse).
     */
    public static function formatAdminPackingList(Order $order, Shop $shop): string
    {
        $order->loadMissing(['items.product']);

        $phone = self::formatPhone($order);
        $delivery = self::formatAddress($order);
        $totalUnits = $order->items->sum('quantity');
        $itemCount = $order->items->count();

        $lines = [];
        $lines[] = "🚨 *NEW ORDER RECEIVED! | " . strtoupper($shop->name ?: 'GURU CRACKERS') . "* 💥";
        $lines[] = "*DISPATCH & PACKING ALERT*";
        $lines[] = "----------------------------------------";
        $lines[] = "▫️ *Order No:* `#" . $order->order_number . "`";
        $lines[] = "▫️ *Date:* " . $order->created_at->format('d M Y, h:i A');
        $lines[] = "▫️ *Customer:* " . trim($order->name);
        $lines[] = "▫️ *Phone:* " . $phone;
        $lines[] = "📍 *Destination:* " . trim(strtoupper($order->city)) . " - " . trim($order->pincode);
        $lines[] = "🏠 *Address:* " . $delivery;
        $lines[] = "----------------------------------------";
        $lines[] = "📊 *ORDER OVERVIEW:*";
        $lines[] = "▫️ *Total Varieties:* " . $itemCount . " varieties";
        $lines[] = "▫️ *Total Units / Boxes:* " . $totalUnits . " boxes";
        $lines[] = "💰 *Order Value:* *Rs." . number_format($order->total_amount, 2) . "*";
        $lines[] = "💳 *Payment Status:* *" . strtoupper($order->payment_status ?: 'PENDING') . "*";
        $lines[] = "";
        $lines[] = "📦 *Warehouse Packing Checklist PDF is attached below!*";
        $lines[] = "*(Print or check directly on phone with [ ] tick marks)*";
        $lines[] = "";
        $lines[] = "👉 *Manage Order in Admin Panel:*";
        $lines[] = route('admin.orders.show', $order);

        return implode("\n", $lines);
    }

    /**
     * Customer message when sharing payment screenshot to Shop WhatsApp.
     */
    public static function formatPaymentScreenshotShare(Order $order, Shop $shop): string
    {
        $phone = self::formatPhone($order);

        $lines = [];
        $lines[] = "*PAYMENT SCREENSHOT FOR CRACKER ORDER*";
        $lines[] = "*Order No:* #" . $order->order_number;
        $lines[] = "*Customer Name:* " . trim($order->name);
        $lines[] = "*Mobile:* " . $phone;
        $lines[] = "*Total Amount:* Rs." . number_format($order->total_amount, 2);
        if (!empty($shop->upi_id)) {
            $lines[] = "*Transferred To UPI:* " . $shop->upi_id;
        }
        $lines[] = "========================================";
        $lines[] = "I have completed the UPI payment. Please find my payment screenshot attached above. Kindly verify and confirm my cracker order dispatch!";

        return implode("\n", $lines);
    }

    /**
     * Payment Confirmation Message sent from Admin to Customer.
     */
    public static function formatPaymentConfirmation(Order $order, Shop $shop): string
    {
        $invoiceUrl = route('order.public_invoice', ['orderNumber' => $order->order_number, 'token' => $order->getInvoiceSignature()]);
        $shopName = strtoupper($shop->name ?: 'GURU CRACKERS');
        $city = strtoupper(trim($order->city));
        $shopPhone = $shop->phone ?: '+91 8779981128';
        if (!empty($shop->secondary_phone)) {
            $shopPhone .= ' / ' . $shop->secondary_phone;
        }

        $lines = [];
        $lines[] = "🎉 *PAYMENT CONFIRMED & ORDER BOOKED!* 🪔";
        $lines[] = "*{$shopName} - SIVAKASI*";
        $lines[] = "---------------------------------------------";
        $lines[] = "Dear *" . trim($order->name) . "*,";
        $lines[] = "Thank you! We have successfully received your payment of *₹" . number_format($order->total_amount, 2) . "* for Order `#" . $order->order_number . "`.";
        $lines[] = "";
        $lines[] = "✅ *Status:* Payment Verified & Dispatch in Progress";
        $lines[] = "📍 *Destination Hub:* *" . $city . "*";
        $lines[] = "";
        $lines[] = "📦 *WHAT HAPPENS NEXT? (அடுத்த செயல்முறை):*";
        $lines[] = "1️⃣ *Safe Packing:* Your Diwali crackers are being packed in heavy-duty weatherproof boxes.";
        $lines[] = "2️⃣ *Transport Dispatch:* Your parcel will be handed over to certified Sivakasi transport.";
        $lines[] = "3️⃣ *LR Tracking Slip:* Once dispatched, your transport parcel LR copy & tracking details will be sent directly to your WhatsApp.";
        $lines[] = "";
        $lines[] = "---------------------------------------------";
        $lines[] = "📄 *Official Tax Invoice PDF is attached below!*";
        $lines[] = "👉 *Online Invoice Link:*";
        $lines[] = $invoiceUrl;
        $lines[] = "";
        if (!empty($shopPhone)) {
            $lines[] = "📞 *Shop Helpline:* " . $shopPhone;
        }
        $lines[] = "✨ *Wishing you and your family a Joyful, Safe & Sparkling Diwali!* 🎆🎇";

        return implode("\n", $lines);
    }

    /**
     * Get URL to open WhatsApp with Customer Invoice pre-filled to Customer's number.
     */
    public static function getCustomerInvoiceWhatsAppUrl(Order $order, Shop $shop): string
    {
        $phone = self::normalizePhone($order->phone1);
        $text = self::formatCustomerInvoice($order, $shop);

        return "https://api.whatsapp.com/send?phone={$phone}&text=" . rawurlencode($text);
    }

    /**
     * Get URL to send Packing List to Shop Admin / Warehouse WhatsApp number.
     */
    public static function getAdminPackingListWhatsAppUrl(Order $order, Shop $shop): string
    {
        $targetPhone = self::normalizePhone($shop->whatsapp_phone ?: $shop->phone);
        $text = self::formatAdminPackingList($order, $shop);

        return "https://api.whatsapp.com/send?phone={$targetPhone}&text=" . rawurlencode($text);
    }

    /**
     * Get URL for customer to send Payment Screenshot to Shop WhatsApp number.
     */
    public static function getPaymentScreenshotWhatsAppUrl(Order $order, Shop $shop): string
    {
        $shopPhone = self::normalizePhone($shop->whatsapp_phone ?: $shop->phone);
        $text = self::formatPaymentScreenshotShare($order, $shop);

        return "https://api.whatsapp.com/send?phone={$shopPhone}&text=" . rawurlencode($text);
    }

    /**
     * Get URL for Admin to send Payment & Dispatch Confirmation to Customer.
     */
    public static function getPaymentConfirmationWhatsAppUrl(Order $order, Shop $shop): string
    {
        $customerPhone = self::normalizePhone($order->phone1);
        $text = self::formatPaymentConfirmation($order, $shop);

        return "https://api.whatsapp.com/send?phone={$customerPhone}&text=" . rawurlencode($text);
    }


    /**
     * Check if local WhatsApp microservice is connected.
     * Enforces shop WhatsApp phone restriction.
     */
    public static function checkServerStatus(?string $expectedPhone = null): array
    {
        try {
            $shop = Shop::current();
            $targetPhone = $expectedPhone ?: ($shop->whatsapp_phone ?: $shop->phone);
            $cleanPhone = self::normalizePhone($targetPhone);

            $response = \Illuminate\Support\Facades\Http::timeout(3)->get('http://127.0.0.1:3001/status', [
                'allowed_phone' => $cleanPhone,
            ]);

            if ($response->successful()) {
                $isConnected = (bool) ($response->json('connected') ?? false);
                return [
                    'online' => true,
                    'connected' => $isConnected,
                    'linked' => $isConnected,
                    'user' => $response->json('user'),
                    'qr' => $response->json('qr'),
                    'allowedPhone' => $response->json('allowedPhone'),
                    'rejectedReason' => $response->json('rejectedReason'),
                ];
            }
        } catch (\Throwable $e) {
            // Service offline
        }

        return [
            'online' => false,
            'connected' => false,
            'linked' => false,
            'user' => null,
            'qr' => null,
            'allowedPhone' => null,
            'rejectedReason' => null,
        ];
    }

    /**
     * Send direct message via local WhatsApp microservice without user interaction.
     */
    public static function sendDirectMessage(string $phone, string $message, ?int $orderId = null, string $triggerSource = 'live_chat'): array
    {
        try {
            $secret = config('services.whatsapp.secret', 'gc-whatsapp-internal-2026');
            $response = \Illuminate\Support\Facades\Http::timeout(25)
                ->withHeaders(['X-Internal-Secret' => $secret])
                ->post('http://127.0.0.1:3001/send-message', [
                    'phone' => self::normalizePhone($phone),
                    'message' => $message,
                ]);

            if ($response->successful() && $response->json('success')) {
                $msgId = $response->json('messageId');
                self::recordOutgoingMessage($phone, 'text', $message, $msgId, null, $orderId, $triggerSource, 'sent');

                return [
                    'success' => true,
                    'messageId' => $msgId,
                    'error' => null,
                ];
            }

            $error = $response->json('error') ?? 'Failed to send message via WhatsApp gateway';
            self::recordOutgoingMessage($phone, 'text', $message, null, null, $orderId, $triggerSource, 'failed', $error);

            return [
                'success' => false,
                'messageId' => null,
                'notOnWhatsApp' => (bool) $response->json('notOnWhatsApp'),
                'error' => $error,
            ];
        } catch (\Throwable $e) {
            self::recordOutgoingMessage($phone, 'text', $message, null, null, $orderId, $triggerSource, 'failed', $e->getMessage());

            return [
                'success' => false,
                'messageId' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send Customer Text Invoice directly via WhatsApp Bot.
     */
    public static function sendTextInvoiceDirect(Order $order, Shop $shop): array
    {
        $status = self::checkServerStatus();
        if (!($status['online'] && $status['connected'])) {
            return [
                'success' => false,
                'error' => 'WhatsApp bot is not connected. Please scan QR in Admin to connect.',
            ];
        }

        $customerInvoice = self::formatCustomerInvoice($order, $shop);
        $resMessage = self::sendDirectMessage($order->phone1, $customerInvoice, $order->id, 'order_invoice');

        // Also send Payment QR Code photo to Customer
        self::sendCustomerPaymentQr($order, $shop);

        return $resMessage;
    }

    /**
     * Get or generate the local filesystem path to the Payment QR image.
     */
    public static function getPaymentQrImagePath(Order $order, Shop $shop): ?string
    {
        // 1. If shop has an uploaded custom UPI QR image in storage
        if (!empty($shop->upi_qr_image) && Storage::disk('public')->exists($shop->upi_qr_image)) {
            $uploadedPath = Storage::disk('public')->path($shop->upi_qr_image);
            if (file_exists($uploadedPath)) {
                return $uploadedPath;
            }
        }

        // 2. Dynamically generate high-res UPI QR code image with order amount & UPI payment link
        $upiUrl = $shop->getUpiPaymentUrl((float) $order->total_amount, $order->order_number);
        if (!empty($upiUrl)) {
            try {
                $qrDir = storage_path('app/public/temp_qr');
                if (!file_exists($qrDir)) {
                    @mkdir($qrDir, 0755, true);
                }
                $tempQrFile = $qrDir . '/QR-' . $order->order_number . '.png';
                $pngData = QrCode::format('png')
                    ->size(500)
                    ->margin(2)
                    ->generate($upiUrl);
                file_put_contents($tempQrFile, $pngData);
                if (file_exists($tempQrFile)) {
                    return $tempQrFile;
                }
            } catch (\Throwable $e) {
                Log::warning('Failed generating Payment QR PNG: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Send Payment QR Code image to Customer WhatsApp.
     */
    public static function sendCustomerPaymentQr(Order $order, Shop $shop): array
    {
        try {
            $qrPath = self::getPaymentQrImagePath($order, $shop);
            if (!$qrPath || !file_exists($qrPath)) {
                return [
                    'success' => false,
                    'error' => 'No payment QR image available.',
                ];
            }

            $caption = "📲 *Scan & Pay Rs." . number_format($order->total_amount, 2) . "*\n"
                . "▫️ UPI ID: `" . ($shop->upi_id ?: '') . "`\n"
                . ($shop->upi_name ? "▫️ Payee: *" . $shop->upi_name . "*\n" : "")
                . "▫️ Order: `#" . $order->order_number . "`\n\n"
                . "👉 Scan with GPay / PhonePe / Paytm and share payment screenshot in this chat! 🙏";

            return self::sendDirectImage($order->phone1, $qrPath, $caption, $order->id, 'order_invoice_qr');
        } catch (\Throwable $e) {
            Log::warning('Could not send Payment QR to WhatsApp: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send Warehouse Packing List alert and Checklist PDF directly to Shop/Warehouse WhatsApp via Bot.
     */
    public static function sendPackingListDirect(Order $order, Shop $shop, ?string $targetPhone = null): array
    {
        $status = self::checkServerStatus();
        if (!($status['online'] && $status['connected'])) {
            return [
                'success' => false,
                'error' => 'WhatsApp bot is not connected. Please scan QR in Admin to connect.',
            ];
        }

        $phone = $targetPhone ?: ($shop->whatsapp_phone ?: $shop->phone);
        if (empty($phone)) {
            return [
                'success' => false,
                'error' => 'No shop or warehouse phone number configured in Shop Details.',
            ];
        }

        // 1. Send concise summary alert text
        $packingAlert = self::formatAdminPackingList($order, $shop);
        $resText = self::sendDirectMessage($phone, $packingAlert, $order->id, 'warehouse_alert');

        // 2. Generate and deliver Warehouse Packing Checklist PDF
        $resPdf = ['success' => false];
        try {
            $pdfPath = self::generatePackingChecklistPdfFile($order, $shop);
            $fileName = 'PackingList-' . $order->order_number . '.pdf';
            $caption = "📦 Warehouse Packing Checklist for Order #{$order->order_number} ({$order->name} - {$order->city})";
            $resPdf = self::sendDirectDocument($phone, $pdfPath, $fileName, $caption, $order->id, 'warehouse_alert');
        } catch (\Throwable $e) {
            $resPdf = ['success' => false, 'error' => $e->getMessage()];
        }

        return [
            'success' => $resText['success'] || $resPdf['success'],
            'text_sent' => $resText['success'],
            'pdf_sent' => $resPdf['success'],
            'error' => $resText['error'] ?: ($resPdf['error'] ?? null),
        ];
    }

    /**
     * Automatically send Order Invoice to Customer and Packing Checklist PDF to Shop/Warehouse.
     */
    public static function sendAutomatedOrderInvoice(Order $order, Shop $shop): array
    {
        $status = self::checkServerStatus();
        if (!($status['online'] && $status['connected'])) {
            return [
                'customer_sent' => false,
                'admin_sent' => false,
                'reason' => 'WhatsApp gateway not connected',
            ];
        }

        // 1. Send Customer Invoice Text
        $customerInvoice = self::formatCustomerInvoice($order, $shop);
        $resCustomer = self::sendDirectMessage($order->phone1, $customerInvoice, $order->id, 'order_invoice');

        // 1b. Send Payment QR Code image right after the invoice message
        self::sendCustomerPaymentQr($order, $shop);

        // 2. Send Admin Packing List Alert + Checklist PDF to shop
        $adminPhone = $shop->whatsapp_phone ?: $shop->phone;
        $resAdmin = ['success' => false];
        if (!empty($adminPhone)) {
            $adminPackingAlert = self::formatAdminPackingList($order, $shop);
            $resAlert = self::sendDirectMessage($adminPhone, $adminPackingAlert, $order->id, 'warehouse_alert');

            // Generate and send Checklist PDF
            try {
                $pdfPath = self::generatePackingChecklistPdfFile($order, $shop);
                $fileName = 'PackingList-' . $order->order_number . '.pdf';
                $caption = "📦 Warehouse Packing Checklist for Order #{$order->order_number} ({$order->name})";
                $resDoc = self::sendDirectDocument($adminPhone, $pdfPath, $fileName, $caption, $order->id, 'warehouse_alert');
                $resAdmin = [
                    'success' => $resAlert['success'] || $resDoc['success'],
                ];
            } catch (\Throwable $e) {
                $resAdmin = $resAlert;
            }
        }

        return [
            'customer_sent' => $resCustomer['success'],
            'admin_sent' => $resAdmin['success'],
            'customer_error' => $resCustomer['error'] ?? null,
        ];
    }

    /**
     * Format WhatsApp LR Dispatch & Transport Tracking Message.
     */
    public static function formatDispatchLrMessage(Order $order, Shop $shop): string
    {
        $shopName = strtoupper($shop->name ?: 'GURU CRACKERS');
        $dispatchDate = $order->dispatch_date ? $order->dispatch_date->format('d M Y') : now()->format('d M Y');
        $city = strtoupper(trim($order->destination_hub ?: $order->city));
        $shopPhone = $shop->phone ?: '+91 8779981128';
        $invoiceUrl = route('order.public_invoice', ['orderNumber' => $order->order_number, 'token' => $order->getInvoiceSignature()]);

        $lines = [];
        $lines[] = "🚚 *PARCEL DISPATCHED & IN TRANSIT!* 💥";
        $lines[] = "*{$shopName} - SIVAKASI*";
        $lines[] = "---------------------------------------------";
        $lines[] = "Dear *" . trim($order->name) . "*,";
        $lines[] = "Great news! Your Diwali cracker parcel for Order `#" . $order->order_number . "` has been safely packed and handed over to the transport in Sivakasi!";
        $lines[] = "";
        $lines[] = "📋 *TRANSPORT & LR TRACKING DETAILS:*";
        $lines[] = "▫️ *Parcel Service:* *" . ($order->parcel_service_name ?: 'A1 Parcel Service') . "*";
        $lines[] = "▫️ *LR / Bilty No:* `" . ($order->lr_number ?: 'N/A') . "`";
        if (!empty($order->parcel_count)) {
            $lines[] = "▫️ *Total Parcels:* *" . $order->parcel_count . "*";
        }
        $lines[] = "▫️ *Dispatch Date:* " . $dispatchDate;
        $lines[] = "▫️ *Destination Hub:* *" . $city . "*";
        if ($order->delivery_charges !== null && (float)$order->delivery_charges > 0) {
            $lines[] = "💰 *Delivery / Transport Charges:* *Rs." . number_format($order->delivery_charges, 2) . "*";
            $lines[] = "   _(டிரான்ஸ்போர்ட் கட்டணம்: ரூ." . number_format($order->delivery_charges, 2) . " - பார்சல் பெறும் போது அலுவலகத்தில் செலுத்த வேண்டும்)_";
        } else {
            $lines[] = "💰 *Delivery / Transport Charges:* *As per Transport Partner Slip* (பார்சல் பெறும் போது செலுத்த வேண்டும்)";
        }
        if (!empty($order->transport_phone)) {
            $lines[] = "📞 *Transport Branch Phone:* *" . $order->transport_phone . "*";
        }
        if (!empty($order->dispatch_notes)) {
            $lines[] = "📝 *Note:* " . $order->dispatch_notes;
        }
        $lines[] = "";
        $lines[] = "---------------------------------------------";
        $lines[] = "📦 *HOW TO COLLECT YOUR PARCEL (பார்சல் பெறுவது எப்படி?):*";
        $lines[] = "1️⃣ உங்கள் பார்சல் இன்னும் 1-2 நாட்களில் உங்கள் ஊர் டிரான்ஸ்போர்ட் அலுவலகத்தை அடையும்.";
        $lines[] = "2️⃣ டிரான்ஸ்போர்ட் கிளையிலிருந்து உங்களுக்கு அழைப்பு வரும் (அல்லது மேலே உள்ள எண்ணை நீங்கள் தொடர்பு கொள்ளலாம்).";
        if ($order->delivery_charges !== null && (float)$order->delivery_charges > 0) {
            $lines[] = "3️⃣ பார்சல் அலுவலகத்தில் இந்த *LR எண்: " . ($order->lr_number ?: '') . "* மற்றும் அடையாள அட்டை (Aadhaar/ID Proof) காட்டி, டெலிவரி கட்டணம் *Rs." . number_format($order->delivery_charges, 2) . "* செலுத்தி உங்கள் பார்சலை பெற்றுக்கொள்ளலாம்.";
        } else {
            $lines[] = "3️⃣ பார்சல் அலுவலகத்தில் இந்த *LR எண்: " . ($order->lr_number ?: '') . "* மற்றும் தங்களின் அடையாள அட்டையைக் (Aadhaar/ID Proof) காட்டி பார்சலை பெற்றுக்கொள்ளலாம்.";
        }
        $lines[] = "";
        $lines[] = "---------------------------------------------";
        if (!empty($order->lr_receipt_image)) {
            $lines[] = "📷 *Physical LR Receipt Photo is attached below!*";
        }
        $lines[] = "👉 *Online Invoice & LR View:*";
        $lines[] = $invoiceUrl;
        $lines[] = "";
        if (!empty($shopPhone)) {
            $lines[] = "📞 *Shop Helpline:* " . $shopPhone;
        }
        $lines[] = "🎆 *Wishing you and your family a Sparkling & Happy Diwali!* 🪔✨";

        return implode("\n", $lines);
    }

    /**
     * Get URL to send LR Dispatch details via WhatsApp manually.
     */
    public static function getDispatchLrWhatsAppUrl(Order $order, Shop $shop): string
    {
        $customerPhone = self::normalizePhone($order->phone1);
        $text = self::formatDispatchLrMessage($order, $shop);

        return "https://api.whatsapp.com/send?phone={$customerPhone}&text=" . rawurlencode($text);
    }

    /**
     * Generate an invoice PDF file on local disk for WhatsApp document dispatch.
     */
    public static function generateInvoicePdfFile(Order $order, Shop $shop): string
    {
        $order->loadMissing(['items.product']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.invoice', compact('order', 'shop'))
            ->setPaper('a4', 'portrait');

        $dir = storage_path('app/public/temp_invoices');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filePath = $dir . '/Invoice-' . $order->order_number . '.pdf';
        $pdf->save($filePath);

        return $filePath;
    }

    /**
     * Generate a warehouse packing checklist PDF file on local disk for WhatsApp document dispatch.
     */
    public static function generatePackingChecklistPdfFile(Order $order, Shop $shop): string
    {
        $order->loadMissing(['items.product.category']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.packing_checklist', compact('order', 'shop'))
            ->setPaper('a4', 'portrait');

        $dir = storage_path('app/public/temp_invoices');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filePath = $dir . '/PackingList-' . $order->order_number . '.pdf';
        $pdf->save($filePath);

        return $filePath;
    }

    /**
     * Send direct document (e.g. PDF Invoice) via local WhatsApp microservice.
     */
    public static function sendDirectDocument(string $phone, string $filePath, string $fileName, ?string $caption = null, ?int $orderId = null, string $triggerSource = 'document_dispatch'): array
    {
        try {
            $secret = config('services.whatsapp.secret', 'gc-whatsapp-internal-2026');
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->withHeaders(['X-Internal-Secret' => $secret])
                ->post('http://127.0.0.1:3001/send-document', [
                    'phone' => self::normalizePhone($phone),
                    'filePath' => $filePath,
                    'fileName' => $fileName,
                    'caption' => $caption,
                    'mimetype' => 'application/pdf',
                ]);

            if ($response->successful() && $response->json('success')) {
                $msgId = $response->json('messageId');
                self::recordOutgoingMessage($phone, 'document', $caption ?: "📄 PDF Document: {$fileName}", $msgId, $fileName, $orderId, $triggerSource, 'sent');

                return [
                    'success' => true,
                    'messageId' => $msgId,
                    'error' => null,
                ];
            }

            $error = $response->json('error') ?? 'Failed to send PDF document';
            self::recordOutgoingMessage($phone, 'document', $caption ?: "📄 PDF Document: {$fileName}", null, $fileName, $orderId, $triggerSource, 'failed', $error);

            return [
                'success' => false,
                'messageId' => null,
                'notOnWhatsApp' => (bool) $response->json('notOnWhatsApp'),
                'error' => $error,
            ];
        } catch (\Throwable $e) {
            self::recordOutgoingMessage($phone, 'document', $caption ?: "📄 PDF Document: {$fileName}", null, $fileName, $orderId, $triggerSource, 'failed', $e->getMessage());

            return [
                'success' => false,
                'messageId' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send direct image (e.g. LR Receipt Photo) via local WhatsApp microservice.
     */
    public static function sendDirectImage(string $phone, string $filePath, ?string $caption = null, ?int $orderId = null, string $triggerSource = 'image_dispatch'): array
    {
        try {
            $secret = config('services.whatsapp.secret', 'gc-whatsapp-internal-2026');
            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->withHeaders(['X-Internal-Secret' => $secret])
                ->post('http://127.0.0.1:3001/send-image', [
                    'phone' => self::normalizePhone($phone),
                    'filePath' => $filePath,
                    'caption' => $caption,
                ]);

            if ($response->successful() && $response->json('success')) {
                $msgId = $response->json('messageId');
                self::recordOutgoingMessage($phone, 'image', $caption ?: "📷 Photo Attachment", $msgId, null, $orderId, $triggerSource, 'sent');

                return [
                    'success' => true,
                    'messageId' => $msgId,
                    'error' => null,
                ];
            }

            $error = $response->json('error') ?? 'Failed to send image';
            self::recordOutgoingMessage($phone, 'image', $caption ?: "📷 Photo Attachment", null, null, $orderId, $triggerSource, 'failed', $error);

            return [
                'success' => false,
                'messageId' => null,
                'notOnWhatsApp' => (bool) $response->json('notOnWhatsApp'),
                'error' => $error,
            ];
        } catch (\Throwable $e) {
            self::recordOutgoingMessage($phone, 'image', $caption ?: "📷 Photo Attachment", null, null, $orderId, $triggerSource, 'failed', $e->getMessage());

            return [
                'success' => false,
                'messageId' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send Payment Confirmation AND official Invoice PDF to Customer via WhatsApp.
     */
    public static function sendPaymentConfirmationWithPdf(Order $order, Shop $shop): array
    {
        $status = self::checkServerStatus();
        if (!($status['online'] && $status['connected'])) {
            return [
                'success' => false,
                'error' => 'WhatsApp gateway not connected',
            ];
        }

        // 1. Send confirmation text
        $text = self::formatPaymentConfirmation($order, $shop);
        $resText = self::sendDirectMessage($order->phone1, $text, $order->id, 'payment_confirmation');

        // 2. Generate and send Invoice PDF
        $pdfPath = self::generateInvoicePdfFile($order, $shop);
        $fileName = 'Invoice-' . $order->order_number . '.pdf';
        $caption = "Official Tax Invoice for Order #{$order->order_number} - {$shop->name}";
        $resPdf = self::sendDirectDocument($order->phone1, $pdfPath, $fileName, $caption, $order->id, 'payment_confirmation');

        return [
            'success' => $resText['success'] || $resPdf['success'],
            'text_sent' => $resText['success'],
            'pdf_sent' => $resPdf['success'],
            'error' => $resText['error'] ?: ($resPdf['error'] ?? null),
        ];
    }

    /**
     * Send LR Dispatch Notification and LR Receipt Image to Customer via WhatsApp.
     */
    public static function sendDispatchNotification(Order $order, Shop $shop): array
    {
        $status = self::checkServerStatus();
        if (!($status['online'] && $status['connected'])) {
            return [
                'success' => false,
                'error' => 'WhatsApp gateway not connected',
            ];
        }

        // 1. Send Dispatch LR text message
        $text = self::formatDispatchLrMessage($order, $shop);
        $resText = self::sendDirectMessage($order->phone1, $text, $order->id, 'lr_dispatch');

        // 2. If LR receipt image exists, send it
        $resImage = ['success' => false];
        if (!empty($order->lr_receipt_image)) {
            $imagePath = storage_path('app/public/' . $order->lr_receipt_image);
            if (file_exists($imagePath)) {
                $caption = "Official LR Receipt Slip for Order #{$order->order_number} (" . ($order->parcel_service_name ?: 'Transport') . " - LR: " . ($order->lr_number ?: '') . ")";
                $resImage = self::sendDirectImage($order->phone1, $imagePath, $caption, $order->id, 'lr_dispatch');
            }
        }

        return [
            'success' => $resText['success'] || $resImage['success'],
            'text_sent' => $resText['success'],
            'image_sent' => $resImage['success'],
            'error' => $resText['error'] ?: ($resImage['error'] ?? null),
        ];
    }

    /**
     * Record an outgoing message in the database for tracking & live chat history.
     */
    public static function recordOutgoingMessage(
        string $phone,
        string $type,
        ?string $text,
        ?string $messageId = null,
        ?string $fileName = null,
        ?int $orderId = null,
        string $triggerSource = 'live_chat',
        string $status = 'sent',
        ?string $errorMessage = null
    ): ?\App\Models\WhatsAppMessage {
        try {
            $clean = self::normalizePhone($phone);
            $shortPhone = strlen($clean) > 10 ? substr($clean, -10) : $clean;

            if (!$orderId) {
                $order = Order::where('phone1', 'like', "%{$shortPhone}%")
                    ->orWhere('phone2', 'like', "%{$shortPhone}%")
                    ->latest()
                    ->first();
                $orderId = $order?->id;
            }

            $record = \App\Models\WhatsAppMessage::create([
                'order_id' => $orderId,
                'message_id' => $messageId ?: ('out_' . uniqid('', true)),
                'remote_jid' => $clean . '@s.whatsapp.net',
                'phone' => $shortPhone,
                'from_me' => true,
                'sender_name' => 'Guru Crackers',
                'message_type' => $type,
                'message_text' => $text,
                'media_filename' => $fileName,
                'status' => $status,
                'trigger_source' => $triggerSource,
                'error_message' => $errorMessage,
                'is_read' => true,
                'sent_at' => now(),
            ]);

            \App\Services\AuditLogger::log(
                event: 'whatsapp_sent',
                module: 'whatsapp',
                summary: "Dispatched WhatsApp message [{$triggerSource}] to +91 {$shortPhone} (Status: {$status})",
                recordName: "WhatsApp -> +91 {$shortPhone}",
                newValues: ['phone' => $shortPhone, 'type' => $type, 'trigger' => $triggerSource, 'status' => $status]
            );

            return $record;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to record outgoing WhatsApp message: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get list of all customer conversations with details and unread counts.
     */
    public static function getCustomerChatsList(?string $search = null): array
    {
        // 1. Get all phones from WhatsAppMessage
        $messagePhones = \App\Models\WhatsAppMessage::select('phone')
            ->distinct()
            ->pluck('phone')
            ->toArray();

        // 2. Get all phones from Orders (limit to 100 recent)
        $orderPhones = Order::select('phone1')
            ->whereNotNull('phone1')
            ->where('phone1', '!=', '')
            ->latest()
            ->take(100)
            ->pluck('phone1')
            ->map(fn($p) => preg_replace('/[^0-9]/', '', (string) $p))
            ->map(fn($p) => strlen($p) > 10 ? substr($p, -10) : $p)
            ->filter(fn($p) => strlen($p) === 10)
            ->unique()
            ->toArray();

        $allPhones = array_values(array_unique(array_merge($messagePhones, $orderPhones)));

        $chats = [];

        foreach ($allPhones as $phone) {
            if (empty($phone) || strlen($phone) < 10) continue;

            // Find matched order
            $order = Order::where('phone1', 'like', "%{$phone}%")
                ->orWhere('phone2', 'like', "%{$phone}%")
                ->latest()
                ->first();

            // Find latest message
            $latestMsg = \App\Models\WhatsAppMessage::where('phone', $phone)
                ->latest('sent_at')
                ->latest('id')
                ->first();

            // Count unread incoming messages
            $unreadCount = \App\Models\WhatsAppMessage::where('phone', $phone)
                ->where('from_me', false)
                ->where('is_read', false)
                ->count();

            $name = $order?->name ?: ($latestMsg?->sender_name ?: 'Customer');
            if ($name === 'Guru Crackers' && $order?->name) {
                $name = $order->name;
            }

            // Filter by search term if provided
            if ($search) {
                $s = strtolower(trim($search));
                $matchName = str_contains(strtolower($name), $s);
                $matchPhone = str_contains($phone, $s);
                $matchOrder = $order && str_contains(strtolower($order->order_number), $s);
                if (!$matchName && !$matchPhone && !$matchOrder) {
                    continue;
                }
            }

            $lastActivity = $latestMsg?->sent_at ?? ($latestMsg?->created_at ?? ($order?->created_at ?? now()));

            $chats[] = [
                'phone' => $phone,
                'name' => $name,
                'order_id' => $order?->id,
                'order_number' => $order?->order_number,
                'order_total' => $order ? (float) $order->total_amount : null,
                'order_status' => $order?->payment_status,
                'city' => $order?->city,
                'state' => $order?->state,
                'last_message' => $latestMsg ? [
                    'text' => $latestMsg->message_text,
                    'from_me' => (bool) $latestMsg->from_me,
                    'type' => $latestMsg->message_type,
                    'time' => $latestMsg->sent_at ? $latestMsg->sent_at->format('h:i A') : $latestMsg->created_at->format('h:i A'),
                    'date' => $latestMsg->sent_at ? $latestMsg->sent_at->format('d M') : $latestMsg->created_at->format('d M'),
                ] : [
                    'text' => $order ? "Order #{$order->order_number} booked" : "No messages yet",
                    'from_me' => true,
                    'type' => 'text',
                    'time' => $order?->created_at ? $order->created_at->format('h:i A') : '',
                    'date' => $order?->created_at ? $order->created_at->format('d M') : '',
                ],
                'unread_count' => $unreadCount,
                'timestamp' => $lastActivity->timestamp,
                'time_formatted' => $lastActivity->diffForHumans(null, true, true),
            ];
        }

        // Sort chats by timestamp descending (most recent first)
        usort($chats, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $chats;
    }

    /**
     * Get chat conversation thread and customer info for a given phone number.
     */
    public static function getChatMessages(string $phone): array
    {
        $clean = self::normalizePhone($phone);
        $shortPhone = strlen($clean) > 10 ? substr($clean, -10) : $clean;

        // Find matched order
        $order = Order::with('items.product')->where('phone1', 'like', "%{$shortPhone}%")
            ->orWhere('phone2', 'like', "%{$shortPhone}%")
            ->latest()
            ->first();

        // If order exists and no messages exist for this phone, create initial booking record
        $count = \App\Models\WhatsAppMessage::where('phone', $shortPhone)->count();
        if ($count === 0 && $order) {
            $shop = Shop::current();
            $invoiceText = self::formatCustomerInvoice($order, $shop);
            \App\Models\WhatsAppMessage::create([
                'order_id' => $order->id,
                'message_id' => 'initial_inv_' . $order->id,
                'remote_jid' => $clean . '@s.whatsapp.net',
                'phone' => $shortPhone,
                'from_me' => true,
                'sender_name' => 'Guru Crackers',
                'message_type' => 'text',
                'message_text' => $invoiceText,
                'status' => 'sent',
                'is_read' => true,
                'sent_at' => $order->created_at,
                'created_at' => $order->created_at,
            ]);
        }

        // Mark incoming messages as read
        \App\Models\WhatsAppMessage::where('phone', $shortPhone)
            ->where('from_me', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Fetch messages chronologically
        $messages = \App\Models\WhatsAppMessage::where('phone', $shortPhone)
            ->orderBy('sent_at', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'message_id' => $m->message_id,
                    'from_me' => (bool) $m->from_me,
                    'sender_name' => $m->sender_name,
                    'message_type' => $m->message_type,
                    'message_text' => $m->message_text,
                    'media_filename' => $m->media_filename,
                    'media_url' => $m->media_url,
                    'status' => $m->status,
                    'time' => $m->sent_at ? $m->sent_at->format('h:i A') : $m->created_at->format('h:i A'),
                    'date' => $m->sent_at ? $m->sent_at->format('d M Y') : $m->created_at->format('d M Y'),
                    'is_today' => $m->sent_at ? $m->sent_at->isToday() : $m->created_at->isToday(),
                ];
            });

        $customer = [
            'phone' => $shortPhone,
            'formatted_phone' => '+91 ' . $shortPhone,
            'name' => $order?->name ?: 'Customer',
            'order' => $order ? [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => (float) $order->total_amount,
                'total_amount_formatted' => '₹' . number_format($order->total_amount, 2),
                'payment_status' => $order->payment_status,
                'is_dispatched' => $order->isDispatched(),
                'city' => $order->city,
                'state' => $order->state,
                'address' => $order->delivery_address,
                'pincode' => $order->pincode,
                'lr_number' => $order->lr_number,
                'parcel_service_name' => $order->parcel_service_name,
                'created_at' => $order->created_at->format('d M Y, h:i A'),
                'view_url' => route('admin.orders.show', $order->id),
            ] : null,
        ];

        return [
            'customer' => $customer,
            'messages' => $messages,
        ];
    }

    /**
     * Logout WhatsApp session.
     */
    public static function logout(): array
    {
        try {
            $secret = config('services.whatsapp.secret', 'gc-whatsapp-internal-2026');
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->withHeaders(['X-Internal-Secret' => $secret])
                ->post('http://127.0.0.1:3001/logout');
            return $response->json() ?? ['success' => false];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

