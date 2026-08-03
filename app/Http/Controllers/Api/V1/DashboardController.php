<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AdminOperationsService;
use App\Services\AdminReportService;
use App\Services\Api\ReceiptPayloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use RespondsWithJson;

    public function summary(Request $request, AdminReportService $reports, AdminOperationsService $operations): JsonResponse
    {
        $period = in_array($request->string('period')->toString(), ['today', '7d', '30d', 'month'], true)
            ? $request->string('period')->toString()
            : 'today';

        [$from, $to] = $reports->dashboardPeriodBounds($period);

        return $this->success([
            'period' => $period,
            'period_label' => $reports->dashboardPeriodLabel($period),
            'stats' => $reports->dashboardStatsForPeriod($from, $to),
            'attention' => $reports->attentionMetrics(),
            'restaurant' => $operations->restaurantMetrics($from, $to),
            'pos_eod' => $operations->posEndOfDaySummary(now()),
        ]);
    }
}
