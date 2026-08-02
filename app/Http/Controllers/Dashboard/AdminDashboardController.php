<?php
//--//
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $stats = [
            'customers'   => User::where('role', 1)->count(),
            'experts'     => User::where('role', 2)->count(),
            'companies'   => Company::count(),
            'todayOrders' => Order::whereDate('created_at', now()->toDateString())->count(),
        ];

        // Search string
        $search      = trim((string) $request->query('search', ''));

        // Applied filter
        $ownerFilter = $request->query('owner') ?: null; // 'with' | 'without' | null (all)

        // Get companies
        $companiesQuery = Company::with('admins.users')->orderByDesc('companyID');

        // Search the database
        // Looking for company name || firtst_name ||‌ last_name || username
        if ($search !== '') {
            $companiesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('admins.users', function ($uq) use ($search) {
                        $uq->where('role', 4)
                            ->where(function ($uq2) use ($search) {
                                $uq2->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('username', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // With company owner
        if ($ownerFilter === 'with') {
            $companiesQuery->whereHas('admins.users', fn($q) => $q->where('role', 4));
        } elseif ($ownerFilter === 'without') { // Without company owner
            $companiesQuery->whereDoesntHave('admins.users', fn($q) => $q->where('role', 4));
        }

        // Get the companies based on filters
        $companies = $companiesQuery->get();

        // Return the view
        return view('dashboard.admin', [
            'user'        => $user,
            'stats'       => $stats,
            'companies'   => $companies,
            'search'      => $search,
            'ownerFilter' => $ownerFilter,
        ]);
    }
}
