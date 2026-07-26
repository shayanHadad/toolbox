<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\WorkCategory;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * لیست عمومی شرکت‌ها با قابلیت سرچ، فیلتر بر اساس دسته‌بندی و مرتب‌سازی.
     */
    public function index(Request $request)
    {
        $categories = WorkCategory::orderBy('category_name')->get();

        $query = Company::query()
            ->with(['categories', 'admins.users'])
            // میانگین امتیاز از روی سفارش‌های تمام‌شده‌ای که rating دارن
            ->withAvg(['orders as rating_avg' => function ($q) {
                $q->whereNotNull('rating');
            }], 'rating')
            ->withCount(['orders as orders_count' => function ($q) {
                $q->where('status', 'finished');
            }]);

        // --- سرچ بار: توی نام و توضیحات شرکت ---
        if ($request->filled('q')) {
            $search = trim($request->string('q'));

            $query->where(function ($qq) use ($search) {
                $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('descriptions', 'like', "%{$search}%");
            });
        }

        // --- فیلتر دسته‌بندی (بر اساس slug ستون url، هماهنگ با بقیه‌ی سایت) ---
        if ($request->filled('category')) {
            $categorySlug = $request->string('category');

            $query->whereHas('categories', function ($cq) use ($categorySlug) {
                $cq->where('url', $categorySlug);
            });
        }

        // --- مرتب‌سازی ---
        match ($request->input('sort')) {
            'rating' => $query->orderByDesc('rating_avg'),
            'orders' => $query->orderByDesc('orders_count'),
            default  => $query->orderByDesc('companyID'), // جدیدترین‌ها
        };

        $companies = $query->paginate(9)->withQueryString();

        // آی‌دی شرکت‌هایی که کاربر لاگین‌کرده (در صورتی که مشتری باشه) بوکمارک کرده
        $bookmarkedCompanyIds = [];
        if (auth()->check() && auth()->user()->role == 1) {
            $bookmarkedCompanyIds = auth()->user()
                ->bookmarkedCompanies()
                ->pluck('companies.companyID')
                ->all();
        }

        return view('companies.index', [
            'companies'            => $companies,
            'categories'           => $categories,
            'bookmarkedCompanyIds' => $bookmarkedCompanyIds,
        ]);
    }

    /**
     * پروفایل عمومی یک شرکت.
     */
    public function show(Company $company)
    {
        $company->load(['categories', 'admins.users']);
        $company->loadAvg(['orders as rating_avg' => function ($q) {
            $q->whereNotNull('rating');
        }], 'rating');

        $reviews = $company->orders()
            ->whereNotNull('comment')
            ->whereNotNull('rating')
            ->with('customer')
            ->latest('order_date')
            ->take(6)
            ->get();

        $isBookmarked = false;
        if (auth()->check() && auth()->user()->role == 1) {
            $isBookmarked = auth()->user()
                ->bookmarkedCompanies()
                ->where('companies.companyID', $company->companyID)
                ->exists();
        }

        return view('companies.show', [
            'company'      => $company,
            'reviews'      => $reviews,
            'isBookmarked' => $isBookmarked,
            'contactUser'  => $company->contactUser(),
        ]);
    }
}
