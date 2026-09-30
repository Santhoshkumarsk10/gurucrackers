<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\CsvSanitizer;

class ReportController extends Controller
{
    /**
     * Build standard filtered order query according to request inputs.
     */
    protected function buildReportQuery(Request $request)
    {
        $query = Order::query();

        // 1. Date Range Filter
        $range = $request->get('range', 'all_time');
        $startDate = null;
        $endDate = null;

        // Check if custom month and year selected
        if ($request->filled('month') && $request->filled('year')) {
            try {
                $m = (int) $request->month;
                $y = (int) $request->year;
                $startDate = Carbon::create($y, $m, 1)->startOfMonth();
                $endDate = Carbon::create($y, $m, 1)->endOfMonth();
                $range = 'month_year';
            } catch (\Throwable $e) {
                // fallback
            }
        } elseif ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $range = 'custom';
            } catch (\Throwable $e) {
                // fallback
            }
        } elseif ($range === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($range === 'yesterday') {
            $startDate = Carbon::yesterday()->startOfDay();
            $endDate = Carbon::yesterday()->endOfDay();
        } elseif ($range === 'this_week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        } elseif ($range === 'last_7_days') {
            $startDate = Carbon::today()->subDays(6)->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($range === 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($range === 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
        } elseif ($range === 'last_30_days') {
            $startDate = Carbon::today()->subDays(29)->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($range === 'last_90_days') {
            $startDate = Carbon::today()->subDays(89)->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($range === 'this_year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }

        // Apply date boundary if specified and not all_time
        if ($startDate && $endDate && $range !== 'all_time') {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // If all_time, calculate display dates for input fields
        if ($range === 'all_time') {
            $firstOrderDate = Order::min('created_at');
            $startDate = $firstOrderDate ? Carbon::parse($firstOrderDate)->startOfDay() : Carbon::today()->startOfMonth();
            $endDate = Carbon::now()->endOfDay();
        }

        // 2. State Filter (Indian States)
        if ($request->filled('state') && $request->state !== 'all') {
            $query->where('state', $request->state);
        }

        // 3. Payment / Order Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $sf = $request->status;
            if ($sf === 'paid') {
                $query->where('payment_status', 'paid');
            } elseif ($sf === 'unpaid') {
                $query->where('payment_status', 'pending');
            } else {
                $query->where('status', $sf);
            }
        }

        // 4. City Filter
        if ($request->filled('city') && $request->city !== 'all') {
            $query->where('city', $request->city);
        }

        // 5. Search Filter
        if ($request->filled('search')) {
            $s = trim($request->search);
            if (mb_strlen($s) >= 2) {
                $query->where(function ($q) use ($s) {
                    $q->where('order_number', 'like', "%{$s}%")
                      ->orWhere('name', 'like', "%{$s}%")
                      ->orWhere('phone1', 'like', "%{$s}%")
                      ->orWhere('city', 'like', "%{$s}%")
                      ->orWhere('state', 'like', "%{$s}%")
                      ->orWhere('pincode', 'like', "%{$s}%");
                });
            }
        }

        return [$query, $startDate, $endDate, $range];
    }

    /**
     * Display Sales Report interface with customized filters.
     */
    public function index(Request $request)
    {
        [$query, $startDate, $endDate, $activeRange] = $this->buildReportQuery($request);

        // Calculate summary metrics on cloned query
        $totalOrders = (clone $query)->count();
        $totalRevenue = (float) (clone $query)->sum('total_amount');
        
        $paidRevenue = (float) (clone $query)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $pendingRevenue = (float) (clone $query)
            ->where('payment_status', 'pending')
            ->sum('total_amount');

        $avgOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;

        // Total items sold in filtered orders
        $orderIds = (clone $query)->pluck('id');
        $totalItemsSold = (int) OrderItem::whereIn('order_id', $orderIds)->sum('quantity');

        // Status counts for status pill tabs
        $statusCounts = Order::select(
            DB::raw("COALESCE(status, 'pending') as status_key"),
            DB::raw('count(*) as count')
        )
        ->groupBy('status_key')
        ->pluck('count', 'status_key')
        ->toArray();
        $statusCounts['paid'] = Order::where('payment_status', 'paid')->count();

        // Top crackers in filtered orders
        $topProducts = OrderItem::select(
            'product_name',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(line_total) as total_sales'),
            DB::raw('COUNT(DISTINCT order_id) as orders_count')
        )
        ->whereIn('order_id', $orderIds)
        ->groupBy('product_name')
        ->orderByDesc('total_qty')
        ->limit(6)
        ->get();

        // Daily trend data for the period with continuous day-by-day mapping
        $dailyMap = (clone $query)
            ->select(
                DB::raw("DATE(created_at) as order_date"),
                DB::raw("COUNT(*) as orders_count"),
                DB::raw("SUM(total_amount) as daily_revenue")
            )
            ->groupBy('order_date')
            ->orderBy('order_date', 'asc')
            ->get()
            ->keyBy('order_date');

        // Determine range boundaries for continuous chart
        $timelineStart = $startDate ? $startDate->copy()->startOfDay() : null;
        $timelineEnd = $endDate ? $endDate->copy()->endOfDay() : Carbon::today()->endOfDay();

        if (! $timelineStart) {
            $earliestOrder = Order::min('created_at');
            $timelineStart = $earliestOrder ? Carbon::parse($earliestOrder)->startOfDay() : Carbon::today()->subDays(6)->startOfDay();
        }

        // Limit chart to a max of 45 days so bars/points are never cramped
        $daySpan = $timelineStart->diffInDays($timelineEnd) + 1;
        if ($daySpan > 45) {
            $timelineStart = $timelineEnd->copy()->subDays(44)->startOfDay();
        }

        $chartLabels = [];
        $chartFullDates = [];
        $chartRevenue = [];
        $chartOrders = [];

        $pointer = $timelineStart->copy();
        while ($pointer->lte($timelineEnd)) {
            $dateKey = $pointer->toDateString();
            $chartLabels[] = $pointer->format('d M');
            $chartFullDates[] = $pointer->format('D, d M Y');
            $chartRevenue[] = isset($dailyMap[$dateKey]) ? (float) $dailyMap[$dateKey]->daily_revenue : 0;
            $chartOrders[] = isset($dailyMap[$dateKey]) ? (int) $dailyMap[$dateKey]->orders_count : 0;
            $pointer->addDay();
        }

        // Peak stats for chart summary
        $maxRev = !empty($chartRevenue) ? max($chartRevenue) : 0;
        $maxOrders = !empty($chartOrders) ? max($chartOrders) : 0;
        $peakRevIdx = !empty($chartRevenue) ? array_search($maxRev, $chartRevenue) : null;
        $peakRevDay = ($peakRevIdx !== false && isset($chartLabels[$peakRevIdx]) && $maxRev > 0) ? $chartLabels[$peakRevIdx] : null;

        // Distinct states for filter dropdown
        $dbStates = Order::whereNotNull('state')
            ->where('state', '!=', '')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->toArray();
        
        $defaultStates = [
            'Tamil Nadu',
            'Pondicherry',
            'Kerala',
            'Karnataka',
            'Andhra Pradesh',
            'Telangana',
            'Maharashtra',
            'Other',
        ];
        $availableStates = array_values(array_unique(array_merge($defaultStates, $dbStates)));

        // Distinct cities for filter dropdown
        $availableCities = Order::whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        // Paginated list of filtered orders for the table
        $orders = (clone $query)
            ->withCount('items')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $shop = Shop::current();

        return view('admin.reports.index', compact(
            'orders',
            'totalOrders',
            'totalRevenue',
            'paidRevenue',
            'pendingRevenue',
            'avgOrderValue',
            'totalItemsSold',
            'statusCounts',
            'topProducts',
            'chartLabels',
            'chartFullDates',
            'chartRevenue',
            'chartOrders',
            'maxRev',
            'maxOrders',
            'peakRevDay',
            'availableStates',
            'availableCities',
            'activeRange',
            'startDate',
            'endDate',
            'shop'
        ));
    }

    /**
     * Export Orders to CSV (Excel Compatible with UTF-8 BOM).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        [$query, $startDate, $endDate, $range] = $this->buildReportQuery($request);

        $startStr = $startDate ? $startDate->format('Ymd') : 'All';
        $endStr = $endDate ? $endDate->format('Ymd') : Carbon::today()->format('Ymd');
        $fileName = "GuruCrackers_Orders_Report_{$startStr}_to_{$endStr}.csv";

        \App\Services\AuditLogger::logExport('reports', 'CSV', "Exported Orders Report ({$startStr} to {$endStr})");

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Column Headers
            fputcsv($handle, [
                'Order #',
                'Date & Time',
                'Customer Name',
                'Phone 1',
                'Phone 2',
                'Delivery Address',
                'City',
                'State',
                'Pincode',
                'Items Count',
                'Total Amount (INR)',
                'Payment Status',
                'Payment Notes',
                'Transport / Parcel Service',
                'LR Number',
                'Parcel Count',
                'Dispatched Date',
            ]);

            // Stream orders chunk by chunk
            (clone $query)
                ->withCount('items')
                ->orderByDesc('created_at')
                ->chunk(100, function ($orders) use ($handle) {
                    foreach ($orders as $order) {
                        fputcsv($handle, CsvSanitizer::sanitizeRow([
                            $order->order_number,
                            $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '',
                            $order->name,
                            $order->phone1,
                            $order->phone2 ?? '',
                            preg_replace("/\r|\n/", " ", $order->delivery_address ?? ''),
                            $order->city,
                            $order->state ?? 'Tamil Nadu',
                            $order->pincode ?? '',
                            $order->items_count,
                            number_format((float) $order->total_amount, 2, '.', ''),
                            strtoupper($order->payment_status ?: 'PENDING'),
                            preg_replace("/\r|\n/", " ", $order->payment_notes ?? ''),
                            $order->parcel_service_name ?? '',
                            $order->lr_number ?? '',
                            $order->parcel_count ?? '',
                            $order->dispatch_date ? $order->dispatch_date->format('Y-m-d') : '',
                        ]));
                    }
                });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Export Item-by-Item breakdown to CSV (For packing & stock audit).
     */
    public function exportItemsCsv(Request $request): StreamedResponse
    {
        [$query, $startDate, $endDate, $range] = $this->buildReportQuery($request);

        $startStr = $startDate ? $startDate->format('Ymd') : 'All';
        $endStr = $endDate ? $endDate->format('Ymd') : Carbon::today()->format('Ymd');
        $fileName = "GuruCrackers_ItemWise_Report_{$startStr}_to_{$endStr}.csv";

        \App\Services\AuditLogger::logExport('reports', 'CSV', "Exported Item-Wise Sales Breakdown Report ({$startStr} to {$endStr})");

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM
            fputs($handle, "\xEF\xBB\xBF");

            // Header
            fputcsv($handle, [
                'Order #',
                'Order Date',
                'Customer Name',
                'Customer Phone',
                'City',
                'State',
                'Cracker / Item Name',
                'Unit Price (INR)',
                'Quantity',
                'Line Total (INR)',
                'Payment Status',
            ]);

            (clone $query)
                ->with(['items'])
                ->orderByDesc('created_at')
                ->chunk(50, function ($orders) use ($handle) {
                    foreach ($orders as $order) {
                        $orderDate = $order->created_at ? $order->created_at->format('Y-m-d H:i') : '';
                        $status = strtoupper($order->payment_status ?: 'PENDING');

                        foreach ($order->items as $item) {
                            fputcsv($handle, CsvSanitizer::sanitizeRow([
                                $order->order_number,
                                $orderDate,
                                $order->name,
                                $order->phone1,
                                $order->city,
                                $order->state ?? 'Tamil Nadu',
                                $item->product_name,
                                number_format((float) $item->unit_price, 2, '.', ''),
                                (int) $item->quantity,
                                number_format((float) $item->line_total, 2, '.', ''),
                                $status,
                            ]));
                        }
                    }
                });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Export Formatted PDF Sales Report.
     */
    public function exportPdf(Request $request)
    {
        [$query, $startDate, $endDate, $range] = $this->buildReportQuery($request);

        $startStr = $startDate ? $startDate->format('d M Y') : 'All Time';
        $endStr = $endDate ? $endDate->format('d M Y') : Carbon::today()->format('d M Y');
        \App\Services\AuditLogger::logExport('reports', 'PDF', "Exported PDF Sales & Order Report ({$startStr} to {$endStr})");

        $shop = Shop::current();
        $orders = (clone $query)->withCount('items')->orderByDesc('created_at')->get();
        
        $totalOrders = $orders->count();
        $totalRevenue = (float) $orders->sum('total_amount');
        $paidRevenue = (float) $orders->where('payment_status', 'paid')->sum('total_amount');
        $pendingRevenue = (float) $orders->where('payment_status', 'pending')->sum('total_amount');

        $orderIds = $orders->pluck('id');
        $totalItemsSold = (int) OrderItem::whereIn('order_id', $orderIds)->sum('quantity');

        // Top 5 crackers
        $topProducts = OrderItem::select(
            'product_name',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(line_total) as total_sales')
        )
        ->whereIn('order_id', $orderIds)
        ->groupBy('product_name')
        ->orderByDesc('total_qty')
        ->limit(5)
        ->get();

        $startStr = $startDate ? $startDate->format('d M Y') : 'Start of Records';
        $endStr = $endDate ? $endDate->format('d M Y') : Carbon::today()->format('d M Y');

        $pdf = Pdf::loadView('admin.reports.pdf', compact(
            'shop',
            'orders',
            'totalOrders',
            'totalRevenue',
            'paidRevenue',
            'pendingRevenue',
            'totalItemsSold',
            'topProducts',
            'startStr',
            'endStr'
        ))->setPaper('a4', 'landscape');

        $fileDate = Carbon::today()->format('Ymd');
        return $pdf->download("GuruCrackers_Sales_Report_{$fileDate}.pdf");
    }

    /**
     * Sanitize cell value to prevent CSV Formula / DDE injection in spreadsheet readers.
     */
    public static function sanitizeCsvField(mixed $value): mixed
    {
        return CsvSanitizer::sanitize($value);
    }
}
