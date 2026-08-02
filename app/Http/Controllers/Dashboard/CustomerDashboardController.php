<?php
//--//
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

        // Fetch the 6 newest orders
        $orders = $user->customerOrders()
            ->with(['provider', 'company'])
            ->orderByDesc('orderID')
            ->take(6)
            ->get();

        // Change the status codes to strings
        $stats = [
            'active'    => $user->customerOrders()->whereIn('status', [Order::STATUS_WAITING, Order::STATUS_IN_PROGRESS])->count(),
            'completed' => $user->customerOrders()->where('status', Order::STATUS_FINISHED)->count(),
            'bookmarks' => $user->bookmarkedProviders()->count(),
            'unread'    => $user->receivedMessages()->where('status', 0)->count(),
        ];

        // Fetch the bookmarked providers
        $bookmarkedProviders = $user->bookmarkedProviders()
            ->with('expertDetail')
            ->take(4)
            ->get();


        // Recent messages (4)
        $recentMessages = $user->receivedMessages()
            ->where('status', 0) // Unread messages
            ->with(['sender', 'company'])
            ->orderByDesc('messageID')
            ->get()
            ->unique(fn($message) => $message->companyID ?? 'user-' . $message->senderID)
            ->take(4)
            ->values();

        // Return the view
        return view('dashboard.customer', [
            'user'                => $user,
            'orders'              => $orders,
            'stats'               => $stats,
            'bookmarkedProviders' => $bookmarkedProviders,
            'recentMessages'      => $recentMessages,
        ]);
    }
}
