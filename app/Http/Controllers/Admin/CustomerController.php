<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerNotesRequest;
use App\Http\Requests\Admin\CustomerTagRequest;
use App\Models\CustomerTag;
use App\Models\User;
use App\Support\AdminTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = AdminTable::paginate(
            User::query()
                ->where('role', UserRole::Customer)
                ->withCount('orders'),
            $request,
            [
                'name' => 'name',
                'email' => 'email',
                'phone' => 'phone',
                'orders_count' => 'orders_count',
                'is_active' => 'is_active',
                'created_at' => 'created_at',
            ],
        );

        return view('admin.customers.index', compact('customers'));
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        abort_unless($user->role === UserRole::Customer, 404);

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Customer status updated.');
    }

    public function updateNotes(CustomerNotesRequest $request, User $user): RedirectResponse
    {
        abort_unless($user->role === UserRole::Customer, 404);

        $user->update([
            'admin_notes' => $request->validated('admin_notes'),
        ]);

        return back()->with('success', 'Customer notes saved.');
    }

    public function storeTag(CustomerTagRequest $request, User $user): RedirectResponse
    {
        abort_unless($user->role === UserRole::Customer, 404);

        $user->customerTags()->firstOrCreate([
            'tag' => $request->validated('tag'),
        ]);

        return back()->with('success', 'Tag added.');
    }

    public function destroyTag(User $user, CustomerTag $tag): RedirectResponse
    {
        abort_unless($user->role === UserRole::Customer, 404);
        abort_unless($tag->user_id === $user->id, 404);

        $tag->delete();

        return back()->with('success', 'Tag removed.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->role === UserRole::Customer, 404);

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('success', $name.' was deleted successfully.');
    }
}
