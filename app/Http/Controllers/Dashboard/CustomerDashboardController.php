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
            'active'    => $user->customerOrders()->whereIn('status', ['waiting', 'in_progress'])->count(),
            'completed' => $user->customerOrders()->where('status', 'finished')->count(),
            'bookmarks' => $user->bookmarkedProviders()->count(),
            'unread'    => $user->receivedMessages()->where('status', 0)->count(),
        ];

        $bookmarkedProviders = $user->bookmarkedProviders()
            ->with('expertDetail')
            ->take(4)
            ->get();

        $recentMessages = $user->receivedMessages()
            ->where('status', 0) // فقط پیام‌های واقعاً خوانده‌نشده
            ->with('sender')
            ->orderByDesc('messageID')
            ->get()
            ->unique('senderID')
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
