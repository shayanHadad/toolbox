<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkCategory;
use Illuminate\Http\Request;

class ExpertController extends Controller
{
    /**
     * لیست عمومی متخصص‌ها با قابلیت سرچ، فیلتر بر اساس دسته‌بندی و مرتب‌سازی.
     */
    public function index(Request $request)
    {
        $categories = WorkCategory::orderBy('category_name')->get();

        $query = User::query()
            ->where('role', 2) // فقط اکسپرت‌ها
            // فقط کسانی که واقعاً پروفایل تخصصی‌شون رو تکمیل کردن نشون داده بشن
            ->whereHas('expertDetail')
            ->with(['expertDetail.category'])
            // میانگین امتیاز از روی سفارش‌های تمام‌شده‌ای که rating دارن
            ->withAvg(['providerOrders as rating_avg' => function ($q) {
                $q->whereNotNull('rating');
            }], 'rating')
            ->withCount(['providerOrders as orders_count' => function ($q) {
                $q->where('status', 'finished');
            }]);

        // --- سرچ بار: توی نام، نام کاربری و توضیحات پروفایل ---
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

        // --- فیلتر دسته‌بندی (بر اساس slug ستون url، هماهنگ با بقیه‌ی سایت) ---
        if ($request->filled('category')) {
            $categorySlug = $request->string('category');

            $query->whereHas('expertDetail.category', function ($cq) use ($categorySlug) {
                $cq->where('url', $categorySlug);
            });
        }

        // --- مرتب‌سازی ---
        match ($request->input('sort')) {
            'rating' => $query->orderByDesc('rating_avg'),
            'orders' => $query->orderByDesc('orders_count'),
            default  => $query->orderByDesc('userID'), // جدیدترین‌ها
        };

        $experts = $query->paginate(9)->withQueryString();

        // آی‌دی متخصص‌هایی که کاربر لاگین‌کرده (در صورتی که مشتری باشه) بوکمارک کرده
        $bookmarkedIds = [];
        if (auth()->check() && auth()->user()->role == 1) {
            $bookmarkedIds = auth()->user()
                ->bookmarkedProviders()
                ->pluck('users.userID')
                ->all();
        }

        return view('experts.index', [
            'experts'       => $experts,
            'categories'    => $categories,
            'bookmarkedIds' => $bookmarkedIds,
        ]);
    }

    /**
     * پروفایل عمومی یک متخصص.
     */
    public function show(User $expert)
    {
        abort_unless($expert->role == 2 && $expert->expertDetail, 404);

        $expert->load('expertDetail.category');
        $expert->loadAvg(['providerOrders as rating_avg' => function ($q) {
            $q->whereNotNull('rating');
        }], 'rating');

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
