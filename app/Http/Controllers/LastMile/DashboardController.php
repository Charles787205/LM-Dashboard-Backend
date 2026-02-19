<?php

namespace App\Http\Controllers\LastMile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Hub;
use App\Models\FailedDeliveries;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{

    private function getPeriod(string $period, ?string $customStartDate, ?string $customEndDate): array
    {
        $endDate = Carbon::now();
        
        if ($period === 'custom' && $customStartDate && $customEndDate) {
            $startDate = Carbon::parse($customStartDate)->startOfDay();
            $endDate = Carbon::parse($customEndDate)->endOfDay();
        } else {
            switch ($period) {
                case 'yesterday':
                    $startDate = Carbon::yesterday()->startOfDay();
                    $endDate = Carbon::yesterday()->endOfDay();
                    break;
                case 'daily':
                    $startDate = Carbon::today()->startOfDay();
                    break;
                case 'weekly':
                    $startDate = Carbon::now()->subDays(7);
                    break;
                case '15days':
                    $startDate = Carbon::now()->subDays(15);
                    break;
                case 'monthly':
                    $startDate = Carbon::now()->subMonth();
                    break;
                default:
                    $startDate = Carbon::now()->subDays(7);
            }
        }

        return [$startDate, $endDate];
    }
    private function getDashboardFilters(Request $request)
    {
        $period = $request->get('period', 'weekly');
        $customStartDate = $request->get('startDate');
        $customEndDate = $request->get('endDate');
        $hubId = $request->get('hubId');
        $hubIds = $request->get('hubIds') ? explode(',', $request->get('hubIds')) : [];
        $excludeSundays = $request->get('excludeSundays') === 'true';

        return [
            $period,
            $customStartDate,
            $customEndDate,
            $hubIds,
            $excludeSundays,
        ];
    }

    private function getBaseQuery($startDate, $endDate, $hubIds = [], $excludeSundays = false)
    {
        $query = Report::query()
            ->whereBetween('date', [$startDate, $endDate]);

        
        if ($hubIds && is_array($hubIds) && count($hubIds) > 0) {
            $query->whereIn('hub_id', $hubIds);
        }

        if ($excludeSundays) {
            $query->whereRaw('DAYOFWEEK(date) != 1'); // Sunday is 1 in MySQL
        }
        
        return $query;
    }

    public function getDashboardStats(Request $request)
    {
        list($period, $customStartDate, $customEndDate, $hubId, $excludeSundays) = $this->getDashboardFilters($request);
        list($startDate, $endDate) = $this->getPeriod($period, $customStartDate, $customEndDate);
        $enddate = Carbon::now();
        $baseQuery = $this->getBaseQuery($startDate, $endDate, $hubId, $excludeSundays);
        $totalStats = (clone $baseQuery)
                ->selectRaw('
                SUM(inbound) as inbound,
                SUM(outbound) as outbound,
                SUM(backlogs) as backlogs,
                SUM(delivered) as delivered,
                SUM(failed) as failed,
                COUNT(*) as reports
            ')
            ->first();
    $dashboardStats = [
        'inbound' => $totalStats->inbound ?? 0,
        'outbound' => $totalStats->outbound ?? 0,
        'backlogs' => $totalStats->backlogs ?? 0,
        'delivered' => $totalStats->delivered ?? 0,
        'failed' => $totalStats->failed ?? 0,
        'reports' => $totalStats->reports ?? 0,
    ];
    return response()->json($dashboardStats);
        
    }

    

    public function getHubPerformance(Request $request){
        list($period, $customStartDate, $customEndDate, $hubId, $excludeSundays) = $this->getDashboardFilters($request);
        list($startDate, $endDate) = $this->getPeriod($period, $customStartDate, $customEndDate);
        $baseQuery = $this->getBaseQuery($startDate, $endDate, $hubId, $excludeSundays);

        $hubPerformance = (clone $baseQuery)
            ->join('hub', 'hub_reports.hub_id', '=', 'hub.id')
            ->selectRaw("
                hub.name as name,
                SUM(hub_reports.delivered) as totalDelivered,
                SUM(hub_reports.failed) as totalFailed,
                SUM(hub_reports.delivered + hub_reports.failed) as totalProcessed,
                CASE 
                    WHEN SUM(hub_reports.delivered + hub_reports.failed) = 0 THEN 0
                    ELSE (SUM(hub_reports.delivered) / SUM(hub_reports.delivered + hub_reports.failed)) * 100
                END as successRate
            ")
            ->groupBy('hub.id', 'hub.name')
            ->orderByDesc('successRate')
            ->get();

        return response()->json($hubPerformance);
    }

    public function getDailyTrends(Request $request){
        list($period, $customStartDate, $customEndDate, $hubId, $excludeSundays) = $this->getDashboardFilters($request);
        list($startDate, $endDate) = $this->getPeriod($period, $customStartDate, $customEndDate);
        $baseQuery = $this->getBaseQuery($startDate, $endDate, $hubId, $excludeSundays);

        $dailyTrends = (clone $baseQuery)
            ->selectRaw("
                DATE(date) as date,
                SUM(inbound) as inbound,
                SUM(outbound) as outbound,
                SUM(backlogs) as backlogs,
                SUM(delivered) as delivered,
                SUM(failed) as failed
            ")
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($dailyTrends);
    }

    public function getKeyMetrics(Request $request){
        list($period, $customStartDate, $customEndDate, $hubId, $excludeSundays) = $this->getDashboardFilters($request);
        list($startDate, $endDate) = $this->getPeriod($period, $customStartDate, $customEndDate);
        $baseQuery = $this->getBaseQuery($startDate, $endDate, $hubId, $excludeSundays);
        
        // Get aggregated metrics
        $keyMetrics = (clone $baseQuery)->join('trips', 'hub_reports.id', '=', 'trips.report_id')
                ->selectRaw('
                    SUM(outbound) as totalOutbound,
                    SUM(inbound) as totalInbound,
                    SUM(backlogs) as totalBacklogs,
                    SUM(delivered) as totalDelivered,
                    SUM(trips.two_w + trips.three_w + trips.four_w) as totalTrips,
                    SUM(failed) as totalFailed,
                    COUNT(DISTINCT DATE(hub_reports.date)) as totalDays
                ')
                ->first();

        $numberOfHubs = Hub::count();
        
        // Calculate metrics with proper division checks
        $totalIncoming = ($keyMetrics->totalInbound ?? 0) + ($keyMetrics->totalBacklogs ?? 0);
        $sdod = $totalIncoming > 0 ? ($keyMetrics->totalOutbound ?? 0) / $totalIncoming : 0;
        $successRate = ($keyMetrics->totalOutbound ?? 0) > 0 ? ($keyMetrics->totalDelivered ?? 0) / ($keyMetrics->totalOutbound ?? 0) : 0;
        $productivity = ($keyMetrics->totalTrips ?? 0) > 0 ? ($keyMetrics->totalOutbound ?? 0) / ($keyMetrics->totalTrips ?? 0) : 0;
        $averageVolume = ($keyMetrics->totalDays ?? 1) > 0 ? $totalIncoming / ($keyMetrics->totalDays ?? 1) : 0;
        
        $keyMetricsData = [
            'sdodRate' => $sdod * 100,
            'averageVolume' => round($averageVolume),
            'averageSuccessRate' => round($successRate * 100, 1),
            'productivity' => round($productivity, 2),
            'activeHubs' => $numberOfHubs,
            'totalIncoming' => $totalIncoming,
        ];
        
        return response()->json($keyMetricsData);
    }
    
   
}
