<?php
//--//
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WorkCategory;
use Illuminate\Http\Request;

class ExpertDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        Order::autoFinishPastOrders();

        $user = $request->user();

        $orders = $user->providerOrders()
            ->with('customer')
            ->orderByDesc('orderID')
            ->take(6)
            ->get();

        $stats = [
            'active'    => $user->providerOrders()->whereIn('status', [Order::STATUS_WAITING, Order::STATUS_IN_PROGRESS])->count(),
            'completed' => $user->providerOrders()->where('status', Order::STATUS_FINISHED)->count(),
            'requests'  => $user->providerOrders()->where('status', Order::STATUS_WAITING)->count(),
            'unread'    => $user->receivedMessages()->where('status', 0)->count(),
        ];

        $recentMessages = $user->receivedMessages()
            ->where('status', 0)
            ->with('sender')
            ->orderByDesc('messageID')
            ->get()
            ->unique('senderID')
            ->take(4)
            ->values();

        $expertDetail = $user->expertDetail;
        $categories = WorkCategory::orderBy('category_name')->get();

        return view('dashboard.expert', [
            'user'           => $user,
            'orders'         => $orders,
            'stats'          => $stats,
            'recentMessages' => $recentMessages,
            'expertDetail'   => $expertDetail,
            'categories'     => $categories,
        ]);
    }
}
