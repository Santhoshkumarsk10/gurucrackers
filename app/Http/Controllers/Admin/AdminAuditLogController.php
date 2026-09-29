<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Shop;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\CsvSanitizer;

class AdminAuditLogController extends Controller
{
    /**
     * Display a paginated listing of system-wide audit logs with rich filters & KPIs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::query()->orderByDesc('created_at')->orderByDesc('id');

        // 1. Search filter
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->search($search);
        }

        // 2. Module filter
        $module = $request->input('module', 'all');
        if ($module !== 'all' && !empty($module)) {
            $query->module($module);
        }

        // 3. Event filter
        $event = $request->input('event', 'all');
        if ($event !== 'all' && !empty($event)) {
            $query->event($event);
        }

        // 4. Date filter & Preset
        $datePreset = $request->input('date_preset', 'all_time');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $datePreset = 'custom';
            $query->dateRange($startDate, $endDate);
        } else {
            match ($datePreset) {
                'today' => $query->whereDate('created_at', Carbon::today()),
                'yesterday' => $query->whereDate('created_at', Carbon::yesterday()),
                'last_7_days' => $query->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay()),
                'this_month' => $query->where('created_at', '>=', Carbon::now()->startOfMonth()),
                'last_30_days' => $query->where('created_at', '>=', Carbon::today()->subDays(29)->startOfDay()),
                default => null,
            };
        }

        // Calculate KPI Metrics (based on all-time or today)
        $totalLogsCount = AuditLog::count();
        $todayLogsCount = AuditLog::whereDate('created_at', Carbon::today())->count();
        $highImpactCount = AuditLog::whereIn('event', ['deleted', 'status_change', 'dispatched', 'password_change', 'bulk_upload'])->count();
        $activeOperatorsCount = AuditLog::whereNotNull('user_name')->distinct('user_name')->count('user_name');

        // Module counts
        $moduleStats = AuditLog::selectRaw('module, count(*) as count')
            ->groupBy('module')
            ->pluck('count', 'module')
            ->toArray();

        // Fetch only modules and events that actually have recorded logs to prevent unwanted empty filtering
        $availableModules = AuditLog::selectRaw('module, count(*) as count')
            ->whereNotNull('module')
            ->where('module', '!=', '')
            ->groupBy('module')
            ->orderByDesc('count')
            ->get();

        $availableEvents = AuditLog::selectRaw('event, count(*) as count')
            ->whereNotNull('event')
            ->where('event', '!=', '')
            ->groupBy('event')
            ->orderByDesc('count')
            ->get();

        // Paginate logs
        $logs = $query->paginate(10)->withQueryString();

        return view('admin.audit_logs.index', compact(
            'logs',
            'search',
            'module',
            'event',
            'datePreset',
            'startDate',
            'endDate',
            'totalLogsCount',
            'todayLogsCount',
            'highImpactCount',
            'activeOperatorsCount',
            'moduleStats',
            'availableModules',
            'availableEvents'
        ));
    }

    /**
     * View detailed JSON diff / record details for a single log entry.
     */
    public function show(AuditLog $auditLog)
    {
        return response()->json([
            'success' => true,
            'log' => $auditLog,
            'created_at_human' => $auditLog->created_at->format('d M Y, h:i:s A'),
            'old_values' => $auditLog->old_values,
            'new_values' => $auditLog->new_values,
        ]);
    }

    /**
     * Export Audit Logs to CSV with UTF-8 BOM for Microsoft Excel.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = AuditLog::query()->orderByDesc('created_at')->orderByDesc('id');

        // Apply same filters
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->search($search);
        }

        $module = $request->input('module', 'all');
        if ($module !== 'all' && !empty($module)) {
            $query->module($module);
        }

        $event = $request->input('event', 'all');
        if ($event !== 'all' && !empty($event)) {
            $query->event($event);
        }

        $datePreset = $request->input('date_preset', 'all_time');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->dateRange($startDate, $endDate);
        } else {
            match ($datePreset) {
                'today' => $query->whereDate('created_at', Carbon::today()),
                'yesterday' => $query->whereDate('created_at', Carbon::yesterday()),
                'last_7_days' => $query->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay()),
                'this_month' => $query->where('created_at', '>=', Carbon::now()->startOfMonth()),
                'last_30_days' => $query->where('created_at', '>=', Carbon::today()->subDays(29)->startOfDay()),
                default => null,
            };
        }

        AuditLogger::logExport('audit_logs', 'CSV', 'Exported System-Wide Audit Tracking Log');

        $fileName = 'GuruCrackers_System_Audit_Log_' . Carbon::now()->format('Ymd_His') . '.csv';

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM
            fputs($handle, "\u{FEFF}");

            // Header Row
            fputcsv($handle, [
                'Log ID',
                'Date & Time (IST)',
                'Operator / User',
                'Role',
                'Module',
                'Event / Action',
                'Record Name',
                'Summary Description',
                'Old Values (Before)',
                'New Values (After)',
                'IP Address',
                'URL',
            ]);

            $query->chunk(500, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, CsvSanitizer::sanitizeRow([
                        $log->id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->user_name ?: 'System / Guest',
                        ucfirst($log->user_role ?: 'Admin'),
                        ucfirst($log->module),
                        ucfirst($log->event),
                        $log->record_name ?: '-',
                        $log->summary,
                        $log->old_values ? json_encode($log->old_values, JSON_UNESCAPED_UNICODE) : '-',
                        $log->new_values ? json_encode($log->new_values, JSON_UNESCAPED_UNICODE) : '-',
                        $log->ip_address ?: '-',
                        $log->url ?: '-',
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
     * Export Filtered Audit Logs to Landscape PDF Document.
     */
    public function exportPdf(Request $request)
    {
        $query = AuditLog::query()->orderByDesc('created_at')->orderByDesc('id');

        // Apply filters
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->search($search);
        }

        $module = $request->input('module', 'all');
        if ($module !== 'all' && !empty($module)) {
            $query->module($module);
        }

        $event = $request->input('event', 'all');
        if ($event !== 'all' && !empty($event)) {
            $query->event($event);
        }

        $datePreset = $request->input('date_preset', 'all_time');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->dateRange($startDate, $endDate);
        } else {
            match ($datePreset) {
                'today' => $query->whereDate('created_at', Carbon::today()),
                'yesterday' => $query->whereDate('created_at', Carbon::yesterday()),
                'last_7_days' => $query->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay()),
                'this_month' => $query->where('created_at', '>=', Carbon::now()->startOfMonth()),
                'last_30_days' => $query->where('created_at', '>=', Carbon::today()->subDays(29)->startOfDay()),
                default => null,
            };
        }

        // Limit to 500 records for optimal PDF generation speed & size
        $logs = (clone $query)->limit(500)->get();
        $totalCount = $logs->count();

        AuditLogger::logExport('audit_logs', 'PDF', "Exported System Audit Trail Report PDF ({$totalCount} entries)");

        $shop = Shop::current();

        $pdf = Pdf::loadView('admin.audit_logs.pdf', compact(
            'logs',
            'shop',
            'totalCount',
            'datePreset',
            'startDate',
            'endDate',
            'module',
            'event',
            'search'
        ))->setPaper('a4', 'landscape');

        $fileName = 'GuruCrackers_System_Audit_Report_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}

