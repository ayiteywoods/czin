<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request, AdminReportService $reports): View
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $to = $request->date('to') ?? now();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $segment = $reports->restaurantSegmentSummary($from, $to);
        $summary = $reports->periodSummary($from, $to);
        $growth = $reports->growthRate($from, $to);

        return view('admin.analytics.index', compact('from', 'to', 'segment', 'summary', 'growth'));
    }
}
