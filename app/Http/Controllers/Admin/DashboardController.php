<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Reports\SalesReportService;
use App\Support\Commerce\Money;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function __construct(private readonly SalesReportService $reports) {}

    public function index(): View
    {
        $user = auth()->user();
        $summary = $this->reports->summary();

        return view('admin.dashboard', [
            'stats' => [
                'customers' => User::query()->whereDoesntHave('roles')->count(),
                'staff' => User::query()->whereHas('roles')->count(),
                'roles' => Role::query()->count(),
                'permissions' => Permission::query()->count(),
                'products' => Product::query()->count(),
                'orders' => Order::query()->count(),
                'revenue' => Money::format($summary['revenue_minor'], config('store.currency', 'NGN')),
            ],
            'permissions' => $user->getPermissionsViaRoles()->pluck('name')->sort()->values()->all(),
            'activity' => Gate::allows('viewAny', ActivityLog::class)
                ? ActivityLog::with('user')->latest()->limit(10)->get()
                : collect(),
        ]);
    }
}
