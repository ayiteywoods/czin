<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminOperationsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosReportController extends Controller
{
    public function index(Request $request, AdminOperationsService $operations): View
    {
        $date = $request->date('date') ?? now();
        $summary = $operations->posEndOfDaySummary(Carbon::parse($date)->startOfDay());

        return view('admin.pos-report.index', compact('date', 'summary'));
    }
}
