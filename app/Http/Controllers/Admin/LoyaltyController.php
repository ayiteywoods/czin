<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LoyaltyTransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoyaltyAdjustRequest;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyTransaction;
use App\Support\AdminTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = AdminTable::paginate(
            LoyaltyAccount::query()->with('user'),
            $request,
            [
                'points_balance' => 'points_balance',
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
            ],
            'updated_at',
            'desc',
        );

        return view('admin.loyalty.index', compact('accounts'));
    }

    public function show(LoyaltyAccount $loyalty): View
    {
        $loyalty->load('user');
        $transactions = $loyalty->transactions()
            ->with('order')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.loyalty.show', compact('loyalty', 'transactions'));
    }

    public function adjust(LoyaltyAdjustRequest $request, LoyaltyAccount $loyalty): RedirectResponse
    {
        $points = (int) $request->validated('points');

        DB::transaction(function () use ($loyalty, $points, $request) {
            $account = LoyaltyAccount::query()->lockForUpdate()->findOrFail($loyalty->id);
            $account->update([
                'points_balance' => max(0, (int) $account->points_balance + $points),
            ]);

            LoyaltyTransaction::query()->create([
                'loyalty_account_id' => $account->id,
                'points' => $points,
                'type' => LoyaltyTransactionType::Adjust,
                'note' => $request->validated('note'),
            ]);
        });

        return redirect()
            ->route('admin.loyalty.show', $loyalty)
            ->with('success', 'Loyalty points adjusted.');
    }
}
