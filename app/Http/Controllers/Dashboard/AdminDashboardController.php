<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * پنل ادمین کل (role=0): آمار کلی سایت + مدیریت شرکت‌ها (افزودن،
     * ویرایش، حذف، سرچ بر اساس نام شرکت/مالک و فیلتر بر اساس وجود مالک).
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $stats = [
            'customers'   => User::where('role', 1)->count(),
            'experts'     => User::where('role', 2)->count(),
            'companies'   => Company::count(),
            // سفارش‌هایی که امروز ثبت شدن (بر اساس created_at، نه
            // order_date که تاریخ موردنظر مشتری برای انجام کاره).
            'todayOrders' => Order::whereDate('created_at', now()->toDateString())->count(),
        ];

        $search      = trim((string) $request->query('search', ''));
        $ownerFilter = $request->query('owner') ?: null; // 'with' | 'without' | null (همه)

        $companiesQuery = Company::with('admins.users')->orderByDesc('companyID');

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

        if ($ownerFilter === 'with') {
            $companiesQuery->whereHas('admins.users', fn ($q) => $q->where('role', 4));
        } elseif ($ownerFilter === 'without') {
            $companiesQuery->whereDoesntHave('admins.users', fn ($q) => $q->where('role', 4));
        }

        $companies = $companiesQuery->get();

        return view('dashboard.admin', [
            'user'        => $user,
            'stats'       => $stats,
            'companies'   => $companies,
            'search'      => $search,
            'ownerFilter' => $ownerFilter,
        ]);
    }
}
