<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\WorkCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * صفحه‌ی یک دسته‌بندی خدمات: تمام متخصص‌ها و شرکت‌های همون دسته، کنار هم.
     */
    public function show(Request $request, WorkCategory $category)
    {
        // --- متخصص‌های این دسته ---
        $experts = User::query()
            ->where('role', 2)
            ->whereHas('expertDetail', function ($q) use ($category) {
                $q->where('categoryID', $category->categoryID);
            })
            ->with('expertDetail.category')
            ->withAvg(['providerOrders as rating_avg' => function ($q) {
                $q->whereNotNull('rating');
            }], 'rating')
            ->withCount(['providerOrders as orders_count' => function ($q) {
                $q->where('status', Order::STATUS_FINISHED);
            }])
            ->get();

        // --- شرکت‌های این دسته ---
        $companies = $category->companies()
            ->with(['categories', 'admins.users'])
            ->withAvg(['orders as rating_avg' => function ($q) {
                $q->whereNotNull('rating');
            }], 'rating')
            ->withCount(['orders as orders_count' => function ($q) {
                $q->where('status', Order::STATUS_FINISHED);
            }])
            ->get();

        // --- برای کاربر مشتری لاگین‌کرده: چه چیزهایی رو از قبل بوکمارک کرده ---
        $bookmarkedExpertIds  = [];
        $bookmarkedCompanyIds = [];

        if (auth()->check() && auth()->user()->role == 1) {
            $bookmarkedExpertIds = auth()->user()
                ->bookmarkedProviders()
                ->pluck('users.userID')
                ->all();

            $bookmarkedCompanyIds = auth()->user()
                ->bookmarkedCompanies()
                ->pluck('companies.companyID')
                ->all();
        }

        return view('categories.show', [
            'category'             => $category,
            'experts'              => $experts,
            'companies'            => $companies,
            'bookmarkedExpertIds'  => $bookmarkedExpertIds,
            'bookmarkedCompanyIds' => $bookmarkedCompanyIds,
        ]);
    }
}
