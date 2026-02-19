<?php

namespace App\Http\Controllers\LastMile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Attendance;
use App\Models\SuccessfulDeliveries;
use App\Models\FailedDeliveries;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['hub','hub.client',  'user', 'attendance', 'trip', 'successfulDeliveries', 'failedDeliveries'])->orderBy('id')->get();
        return response()->json($reports);
    }

    public function show($id)
    {
        $report = Report::with(['hub', 'user', 'attendance', 'trip', 'successfulDeliveries', 'failedDeliveries'])->findOrFail($id);
        return response()->json($report);
    }

    public function getByHub($hubId)
    {
        $reports = Report::with(['user', 'attendance', 'trip', 'successfulDeliveries', 'failedDeliveries'])
            ->where('hub_id', $hubId)
            ->orderBy('date', 'desc')
            ->get();
        return response()->json($reports);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hub_id' => 'required|exists:hub,id',
            'user_id' => 'required|exists:users,id',
            'inbound' => 'required|integer|min:0',
            'outbound' => 'required|integer|min:0',
            'delivered' => 'required|integer|min:0',
            'backlogs' => 'required|integer|min:0',
            'failed' => 'required|integer|min:0',
            'misroutes' => 'required|integer|min:0',
            'date' => 'required|date',
            'sdod' => 'nullable|string',
            'failed_rate' => 'nullable|numeric|min:0|max:100',
            'success_rate' => 'nullable|numeric|min:0|max:100',
            
            // Trip data
            'trips.two_w' => 'required|integer|min:0',
            'trips.three_w' => 'required|integer|min:0',
            'trips.four_w' => 'required|integer|min:0',
            
            // Successful deliveries data
            'successful_deliveries.two_w' => 'required|integer|min:0',
            'successful_deliveries.three_w' => 'required|integer|min:0',
            'successful_deliveries.four_w' => 'required|integer|min:0',
            
            // Failed deliveries data
            'failed_deliveries.two_w' => 'required|integer|min:0',
            'failed_deliveries.three_w' => 'required|integer|min:0',
            'failed_deliveries.four_w' => 'required|integer|min:0',
            'failed_deliveries.canceled_bef_delivery' => 'required|integer|min:0',
            'failed_deliveries.postponed' => 'required|integer|min:0',
            'failed_deliveries.invalid_address' => 'required|integer|min:0',
            'failed_deliveries.unreachable' => 'required|integer|min:0',
            'failed_deliveries.no_cash_available' => 'required|integer|min:0',
            'failed_deliveries.not_at_home' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Create the report
            $report = Report::create([
                'hub_id' => $validated['hub_id'],
                'user_id' => $validated['user_id'],
                'inbound' => $validated['inbound'],
                'outbound' => $validated['outbound'],
                'delivered' => $validated['delivered'],
                'backlogs' => $validated['backlogs'],
                'failed' => $validated['failed'],
                'misroutes' => $validated['misroutes'],
                'date' => $validated['date'],
                'sdod' => $validated['sdod'] ?? null,
                'failed_rate' => $validated['failed_rate'] ?? null,
                'success_rate' => $validated['success_rate'] ?? null,
            ]);

             // Trips
            Trip::create(array_merge(
                ['report_id' => $report->id],
                $validated['trips']
            ));

            // Successful deliveries
            SuccessfulDeliveries::create(array_merge(
                ['report_id' => $report->id],
                $validated['successful_deliveries']
            ));

            // Failed deliveries
            FailedDeliveries::create(array_merge(
                ['report_id' => $report->id],
                $validated['failed_deliveries']
            ));

            DB::commit();

            // Load relationships and return
            $report->load(['hub', 'user', 'trip', 'successfulDeliveries', 'failedDeliveries']);
            return response()->json($report->id, 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return response()->json(['message' => 'Report deleted successfully'], 200);
    }
}
