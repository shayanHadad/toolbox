<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        Order::autoFinishPastOrders();

        $user = $request->user();

        $orders = $user->customerOrders()
            ->with(['provider', 'company'])
            ->orderByDesc('orderID')
            ->take(6)
            ->get();

        $stats = [
            'active'    => $user->customerOrders()->whereIn('status', [Order::STATUS_WAITING, Order::STATUS_IN_PROGRESS])->count(),
            'completed' => $user->customerOrders()->where('status', Order::STATUS_FINISHED)->count(),
            'bookmarks' => $user->bookmarkedProviders()->count(),
            'unread'    => $user->receivedMessages()->where('status', 0)->count(),
        ];

        $bookmarkedProviders = $user->bookmarkedProviders()
            ->with('expertDetail')
            ->take(4)
            ->get();

        $recentMessages = $user->receivedMessages()
            ->where('status', 0) // فقط پیام‌های واقعاً خوانده‌نشده
            ->with(['sender', 'company'])
            ->orderByDesc('messageID')
            ->get()
            // یه پیام شرکتی ممکنه از طرف چند نماینده‌ی مختلف همون شرکت
            // اومده باشه؛ چون توی لیست کامل پیام‌ها (messages.index) همه‌ی
            // این‌ها زیر یک مکالمه‌ی واحد (بر اساس companyID) نشون داده
            // می‌شن، اینجا هم باید همون‌طوری گروه‌بندی بشه، وگرنه ویجت یه
            // پیام رو جدا نشون می‌ده که توی لیست بخشی از یه مکالمه‌ی
            // دیگه‌ست و انگار گم شده.
            ->unique(fn ($message) => $message->companyID ?? 'user-' . $message->senderID)
            ->take(4)
            ->values();

        return view('dashboard.customer', [
            'user'                => $user,
            'orders'              => $orders,
            'stats'               => $stats,
            'bookmarkedProviders' => $bookmarkedProviders,
            'recentMessages'      => $recentMessages,
        ]);
    }
}
