<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WorkCategory;
use Illuminate\Http\Request;

class CompanyDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        Order::autoFinishPastOrders();

        $user = $request->user();
        $company = $user->companyAdmin?->company;

        // اگه کاربر هنوز به هیچ شرکتی وصل نشده (نماینده‌ی هیچ شرکتی نیست)
        if (! $company) {
            return view('dashboard.company', [
                'user'    => $user,
                'company' => null,
            ]);
        }

        $orders = $company->orders()
            ->with('customer')
            ->orderByDesc('orderID')
            ->take(6)
            ->get();

        $stats = [
            'active'    => $company->orders()->whereIn('status', [Order::STATUS_WAITING, Order::STATUS_IN_PROGRESS])->count(),
            'completed' => $company->orders()->where('status', Order::STATUS_FINISHED)->count(),
            'requests'  => $company->orders()->where('status', Order::STATUS_WAITING)->count(),
            'unread'    => $user->receivedMessages()->where('status', 0)->count(),
        ];

        $recentMessages = $user->receivedMessages()
            ->where('status', 0) // فقط پیام‌های واقعاً خوانده‌نشده
            ->with('sender')
            ->orderByDesc('messageID')
            ->get()
            ->unique('senderID')
            ->take(4)
            ->values();

        $categories = WorkCategory::orderBy('category_name')->get();

        return view('dashboard.company', [
            'user'           => $user,
            'company'        => $company,
            'orders'         => $orders,
            'stats'          => $stats,
            'recentMessages' => $recentMessages,
            'categories'     => $categories,
        ]);
    }
}
