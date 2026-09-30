<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\StoreVisit;
use App\Services\WhatsAppOrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $shop = Shop::current();
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Status counts and amounts
        $statusBreakdown = Order::select(
            DB::raw("COALESCE(status, 'pending') as status_key"),
            DB::raw('count(*) as count'),
            DB::raw('SUM(total_amount) as total_amount')
        )
        ->groupBy('status_key')
        ->get()
        ->keyBy('status_key');

        $totalOrdersCount = Order::count();
        $totalRevenueAll = (float) Order::sum('total_amount');
        
        // Revenue strictly from paid orders
        $totalPaidRevenue = (float) Order::where('payment_status', 'paid')->sum('total_amount');
        
        // Pending revenue awaiting confirmation
        $totalPendingRevenue = (float) Order::where('payment_status', 'pending')->sum('total_amount');

        // Today's stats
        $todayOrdersCount = Order::whereDate('created_at', $today)->count();
        $todayRevenue = (float) Order::whereDate('created_at', $today)->sum('total_amount');

        // This Month stats
        $monthOrdersCount = Order::where('created_at', '>=', $startOfMonth)->count();
        $monthRevenue = (float) Order::where('created_at', '>=', $startOfMonth)->sum('total_amount');

        // Total products & categories
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalCategories = Category::count();

        // Total items / cracker boxes sold
        $totalItemsSold = (int) OrderItem::sum('quantity');

        // Average Order Value (AOV)
        $aov = $totalOrdersCount > 0 ? ($totalRevenueAll / $totalOrdersCount) : 0;

        // Store Visitors & Traffic Analytics (Customer Portal)
        $totalUniqueVisitors = StoreVisit::count();
        $totalPageViews = (int) StoreVisit::sum('page_views');
        $todayUniqueVisitors = StoreVisit::whereDate('visited_date', $today)->count();
        $todayPageViews = (int) StoreVisit::whereDate('visited_date', $today)->sum('page_views');
        $monthUniqueVisitors = StoreVisit::where('visited_date', '>=', $startOfMonth)->count();
        $conversionRate = $totalUniqueVisitors > 0 ? min(100, round(($totalOrdersCount / $totalUniqueVisitors) * 100, 1)) : 0;

        $deviceCounts = StoreVisit::select('device', DB::raw('count(*) as count'))
            ->groupBy('device')
            ->pluck('count', 'device')
            ->toArray();

        $mobileCount = (int) ($deviceCounts['mobile'] ?? 0) + (int) ($deviceCounts['tablet'] ?? 0);
        $desktopCount = (int) ($deviceCounts['desktop'] ?? 0);
        $totalDeviceVisits = $mobileCount + $desktopCount;
        $mobilePercentage = $totalDeviceVisits > 0 ? round(($mobileCount / $totalDeviceVisits) * 100) : 0;
        $desktopPercentage = $totalDeviceVisits > 0 ? (100 - $mobilePercentage) : 0;

        $topTrafficSources = StoreVisit::select('referrer_source', DB::raw('count(*) as count'))
            ->groupBy('referrer_source')
            ->orderByDesc('count')
            ->limit(4)
            ->get();

        // Last 14 Days Sales & Visitors Trend for Chart.js
        $trendDays = 14;
        $trendStartDate = Carbon::today()->subDays($trendDays - 1)->startOfDay();
        
        $dailyData = Order::select(
            DB::raw("DATE(created_at) as order_date"),
            DB::raw("COUNT(*) as orders_count"),
            DB::raw("SUM(total_amount) as daily_revenue")
        )
        ->where('created_at', '>=', $trendStartDate)
        ->groupBy('order_date')
        ->orderBy('order_date', 'asc')
        ->get()
        ->keyBy('order_date');

        $dailyVisitorsData = StoreVisit::select(
            DB::raw("DATE(visited_date) as visit_date"),
            DB::raw("COUNT(*) as visitors_count"),
            DB::raw("SUM(page_views) as page_views_count")
        )
        ->where('visited_date', '>=', $trendStartDate->toDateString())
        ->groupBy('visit_date')
        ->get()
        ->keyBy('visit_date');

        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];
        $chartVisitors = [];

        for ($i = 0; $i < $trendDays; $i++) {
            $dateObj = Carbon::today()->subDays($trendDays - 1 - $i);
            $dateStr = $dateObj->toDateString();
            $label = $dateObj->format('d M');

            $chartLabels[] = $label;
            $chartRevenue[] = isset($dailyData[$dateStr]) ? (float) $dailyData[$dateStr]->daily_revenue : 0;
            $chartOrders[] = isset($dailyData[$dateStr]) ? (int) $dailyData[$dateStr]->orders_count : 0;
            $chartVisitors[] = isset($dailyVisitorsData[$dateStr]) ? (int) $dailyVisitorsData[$dateStr]->visitors_count : 0;
        }

        // Status Distribution Data for Doughnut Chart
        $statusCounts = [
            'pending' => (int) ($statusBreakdown->get('pending')?->count ?? 0),
            'paid' => (int) ($statusBreakdown->get('paid')?->count ?? 0),
            'confirmed' => (int) ($statusBreakdown->get('confirmed')?->count ?? 0),
            'dispatched' => (int) ($statusBreakdown->get('dispatched')?->count ?? 0),
        ];

        // Top 5 Best-Selling Crackers
        $topProducts = OrderItem::select(
            'product_name',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(line_total) as total_sales'),
            DB::raw('COUNT(DISTINCT order_id) as orders_count')
        )
        ->groupBy('product_name')
        ->orderByDesc('total_qty')
        ->limit(5)
        ->get();

        // Top 5 Cities by orders
        $topCities = Order::select(
            'city',
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_revenue')
        )
        ->whereNotNull('city')
        ->where('city', '!=', '')
        ->groupBy('city')
        ->orderByDesc('total_orders')
        ->limit(5)
        ->get();

        // Recent 8 Orders
        $recentOrders = Order::withCount('items')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // WhatsApp Gateway Status
        $gatewayStatus = ['online' => false, 'connected' => false];
        try {
            $gatewayStatus = WhatsAppOrderService::checkServerStatus();
        } catch (\Throwable $e) {
            // keep default offline
        }

        return view('admin.dashboard.index', compact(
            'shop',
            'totalOrdersCount',
            'totalRevenueAll',
            'totalPaidRevenue',
            'totalPendingRevenue',
            'todayOrdersCount',
            'todayRevenue',
            'monthOrdersCount',
            'monthRevenue',
            'totalProducts',
            'activeProducts',
            'totalCategories',
            'totalItemsSold',
            'aov',
            'statusCounts',
            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'topProducts',
            'topCities',
            'recentOrders',
            'gatewayStatus',
            'totalUniqueVisitors',
            'totalPageViews',
            'todayUniqueVisitors',
            'todayPageViews',
            'monthUniqueVisitors',
            'conversionRate',
            'mobilePercentage',
            'desktopPercentage',
            'topTrafficSources',
            'chartVisitors'
        ));
    }
}
