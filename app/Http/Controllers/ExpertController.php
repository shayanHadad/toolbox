<?php
//--//
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\WorkCategory;
use Illuminate\Http\Request;

class ExpertController extends Controller
{
    // List of  experts with search and filtering options
    public function index(Request $request)
    {
        $categories = WorkCategory::orderBy('category_name')->get();

        $query = User::query()
            ->where('role', 2) // Only experts
            ->whereHas('expertDetail') // Only experts who completed their profile
            ->with(['expertDetail.category'])
            ->withAvg(['providerOrders as rating_avg' => function ($q) { // Calculate the rating avg
                $q->whereNotNull('rating');
            }], 'rating')
            ->withCount(['providerOrders as orders_count' => function ($q) { // Count the finished orders
                $q->where('status', Order::STATUS_FINISHED);
            }]);

        // Search bar with name - username - profile detalis
        if ($request->filled('q')) {
            $search = trim($request->string('q'));

            $query->where(function ($qq) use ($search) {
                $qq->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhereHas('expertDetail', function ($eq) use ($search) {
                        $eq->where('description', 'like', "%{$search}%");
                    });
            });
        }

        // Category filtering
        if ($request->filled('category')) {
            $categorySlug = $request->string('category');

            $query->whereHas('expertDetail.category', function ($cq) use ($categorySlug) {
                $cq->where('url', $categorySlug);
            });
        }

        // Sorting
        match ($request->input('sort')) {
            'rating' => $query->orderByDesc('rating_avg'),
            'orders' => $query->orderByDesc('orders_count'),
            default  => $query->orderByDesc('userID'), // Newest
        };


        // Show 9 results in each page
        $experts = $query->paginate(9)->withQueryString();

        // Experts that logged in user has bookmarked
        $bookmarkedIds = [];
        if (auth()->check() && auth()->user()->role == 1) {
            $bookmarkedIds = auth()->user()
                ->bookmarkedProviders()
                ->pluck('users.userID')
                ->all();
        }

        // Show the experts view with fetched data
        return view('experts.index', [
            'experts'       => $experts,
            'categories'    => $categories,
            'bookmarkedIds' => $bookmarkedIds,
        ]);
    }

    // Expert public profile
    public function show(User $expert)
    {
        abort_unless($expert->role == 2 && $expert->expertDetail, 404);

        $expert->load('expertDetail.category');
        $expert->loadAvg(['providerOrders as rating_avg' => function ($q) {
            $q->whereNotNull('rating');
        }], 'rating');

        // Expert reviews
        $reviews = $expert->providerOrders()
            ->whereNotNull('comment')
            ->whereNotNull('rating')
            ->with('customer')
            ->latest('order_date')
            ->take(6)
            ->get();

        $isBookmarked = false;
        if (auth()->check() && auth()->user()->role == 1) {
            $isBookmarked = auth()->user()
                ->bookmarkedProviders()
                ->where('users.userID', $expert->userID)
                ->exists();
        }

        return view('experts.show', [
            'expert'       => $expert,
            'reviews'      => $reviews,
            'isBookmarked' => $isBookmarked,
        ]);
    }
}
