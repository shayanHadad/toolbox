<?php
//--//
namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Order;
use App\Models\WorkCategory;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Public list of companies with search and filtering abilities
    public function index(Request $request)
    {
        // Fetch the work categories
        $categories = WorkCategory::orderBy('category_name')->get();

        $query = Company::query()
            ->with(['categories', 'admins.users']) // Load the relations
            ->withAvg(['orders as rating_avg' => function ($q) { // Calculate rating avg
                $q->whereNotNull('rating');
            }], 'rating')
            ->withCount(['orders as orders_count' => function ($q) { // Count the finished orders
                $q->where('status', Order::STATUS_FINISHED);
            }]);

        // Search bar based on name and descriptions
        if ($request->filled('q')) {
            $search = trim($request->string('q'));

            // Search query
            $query->where(function ($qq) use ($search) {
                $qq->where('name', 'like', "%{$search}%")
                    ->orWhere('descriptions', 'like', "%{$search}%");
            });
        }

        // Category filtering
        if ($request->filled('category')) {
            $categorySlug = $request->string('category');

            // Fetch the companies based on the wanted category
            $query->whereHas('categories', function ($cq) use ($categorySlug) {
                $cq->where('url', $categorySlug);
            });
        }

        // Sorting
        match ($request->input('sort')) {
            'rating' => $query->orderByDesc('rating_avg'),
            'orders' => $query->orderByDesc('orders_count'),
            default  => $query->orderByDesc('companyID'), // Newest
        };

        // Show 9 results in eaach page
        $companies = $query->paginate(9)->withQueryString();

        // Fetch the companies that user has bookmarked
        $bookmarkedCompanyIds = [];
        if (auth()->check() && auth()->user()->role == 1) {
            $bookmarkedCompanyIds = auth()->user()
                ->bookmarkedCompanies()
                ->pluck('companies.companyID')
                ->all();
        }

        // Return the view with datas
        return view('companies.index', [
            'companies'            => $companies,
            'categories'           => $categories,
            'bookmarkedCompanyIds' => $bookmarkedCompanyIds,
        ]);
    }

    // Company public profile
    public function show(Company $company)
    {
        $company->load(['categories', 'admins.users']); // Load the relationships
        $company->loadAvg(['orders as rating_avg' => function ($q) { // Calculate the order rating
            $q->whereNotNull('rating');
        }], 'rating');

        // Fetch 6 company orders to show comments and ratings
        $reviews = $company->orders()
            ->whereNotNull('comment')
            ->whereNotNull('rating')
            ->with('customer')
            ->latest('order_date')
            ->take(6)
            ->get();

        // Check if company is bookmarked by the user
        $isBookmarked = false;
        if (auth()->check() && auth()->user()->role == 1) {
            $isBookmarked = auth()->user()
                ->bookmarkedCompanies()
                ->where('companies.companyID', $company->companyID)
                ->exists();
        }

        // Return the view with datas
        return view('companies.show', [
            'company'      => $company,
            'reviews'      => $reviews,
            'isBookmarked' => $isBookmarked,
            'contactUser'  => $company->contactUser(),
        ]);
    }
}
