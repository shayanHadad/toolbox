<?php
//--//
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\WorkCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Showing all experts and companies from the same category
    public function show(Request $request, WorkCategory $category)
    {
        // Fetch experts
        $experts = User::query()
            ->where('role', 2) // Only experts
            ->whereHas('expertDetail', function ($query) use ($category) {
                $query->where('categoryID', $category->categoryID); // With same category ID
            })
            ->with('expertDetail.category') // Get the expert details and category details
            ->withAvg(['providerOrders as rating_avg' => function ($query) {
                $query->whereNotNull('rating');
            }], 'rating') // Calculate the rating avg of the expert
            ->withCount(['providerOrders as orders_count' => function ($query) {
                $query->where('status', Order::STATUS_FINISHED);
            }]) // Count the number of finished orders
            ->get();

        // Fetch companies
        $companies = $category->companies()
            ->with(['categories', 'admins.users']) // Load the relation
            ->withAvg(['orders as rating_avg' => function ($q) { // Calculate the rating avg
                $q->whereNotNull('rating');
            }], 'rating')
            ->withCount(['orders as orders_count' => function ($q) { // Count the number of finished orders
                $q->where('status', Order::STATUS_FINISHED);
            }])
            ->get();

        // If user-role = 1 then:
        $bookmarkedExpertIds  = [];
        $bookmarkedCompanyIds = [];

        // Make sure the user is logged in with role = 1
        if (auth()->check() && auth()->user()->role == 1) {
            $bookmarkedExpertIds = auth()->user()
                ->bookmarkedProviders()
                ->pluck('users.userID') // Only returns userID column
                ->all();

            $bookmarkedCompanyIds = auth()->user()
                ->bookmarkedCompanies()
                ->pluck('companies.companyID')
                ->all();
        }

        // Send the fetched data to the view
        return view('categories.show', [
            'category'             => $category,
            'experts'              => $experts,
            'companies'            => $companies,
            'bookmarkedExpertIds'  => $bookmarkedExpertIds,
            'bookmarkedCompanyIds' => $bookmarkedCompanyIds,
        ]);
    }
}
