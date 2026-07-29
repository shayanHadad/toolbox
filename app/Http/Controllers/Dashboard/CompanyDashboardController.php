<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Message;
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

        $orders = $company->ordersVisibleTo($user)
            ->with('customer')
            ->orderByDesc('orderID')
            ->take(6)
            ->get();

        // پیام‌ها بین اعضای شرکت مشترکه (یک اینباکس واحد)، پس نباید بر اساس
        // receiverID این کاربر خاص فیلتر بشه؛ باید بر اساس companyID شرکت و
        // اینکه فرستنده مشتری باشه (نه یکی دیگه از اعضای شرکت) حساب بشه،
        // وگرنه پیام‌هایی که برای admin/owner دیگه‌ای فرستاده شدن دیده نمی‌شن.
        $companyID = $company->companyID;

        $unreadCompanyMessages = Message::forCompany($companyID)
            ->where('status', 0)
            ->whereHas('sender', fn ($q) => $q->where('role', 1));

        $stats = [
            'active'    => $company->ordersVisibleTo($user)->whereIn('status', [Order::STATUS_WAITING, Order::STATUS_IN_PROGRESS])->count(),
            'completed' => $company->ordersVisibleTo($user)->where('status', Order::STATUS_FINISHED)->count(),
            'requests'  => $company->ordersVisibleTo($user)->where('status', Order::STATUS_WAITING)->count(),
            'unread'    => (clone $unreadCompanyMessages)->count(),
        ];

        $recentMessages = (clone $unreadCompanyMessages)
            ->with('sender')
            ->orderByDesc('messageID')
            ->get()
            ->unique('senderID')
            ->take(4)
            ->values();

        $categories = WorkCategory::orderBy('category_name')->get();

        // ادمین‌های فعلی شرکت (role=3)؛ فقط برای نمایش به مالک شرکت لازمه.
        $companyAdmins = $company->admins()
            ->with('users')
            ->get()
            ->flatMap->users
            ->where('role', 3)
            ->values();

        return view('dashboard.company', [
            'user'           => $user,
            'company'        => $company,
            'orders'         => $orders,
            'stats'          => $stats,
            'recentMessages' => $recentMessages,
            'categories'     => $categories,
            'companyAdmins'  => $companyAdmins,
        ]);
    }
}
