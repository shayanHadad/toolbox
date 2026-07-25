<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Message;
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
            ->get()
            ->unique('senderID')
            ->reject(function ($message) use ($user) {
                // اگه بعد از این پیام، خودمون یه پیام جدیدتر براش فرستاده باشیم
                // یعنی جوابش رو دادیم؛ دیگه توی این ویجت نشونش نده
                return Message::where('senderID', $user->userID)
                    ->where('receiverID', $message->senderID)
                    ->where('messageID', '>', $message->messageID)
                    ->exists();
            })
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