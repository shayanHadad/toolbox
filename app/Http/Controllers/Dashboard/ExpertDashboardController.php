<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\WorkCategory;
use Illuminate\Http\Request;

class ExpertDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $orders = $user->providerOrders()
            ->with('customer')
            ->orderByDesc('orderID')
            ->take(6)
            ->get();

        $stats = [
            'active'    => $user->providerOrders()->whereIn('status', ['waiting', 'in_progress'])->count(),
            'completed' => $user->providerOrders()->where('status', 'finished')->count(),
            'unread'    => $user->receivedMessages()->where('status', 1)->count(),
        ];

        $recentMessages = $user->receivedMessages()
            ->with('sender')
            ->orderByDesc('messageID')
            ->take(4)
            ->get();

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