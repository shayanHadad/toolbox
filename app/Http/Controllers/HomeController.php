<?php
//--//
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\WorkCategory;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function fetchData()
    {
        // Fetch all work categories
        $categories = WorkCategory::orderBy('category_name')->get();

        // Fetch 8 newest comments and ratings for orders with expert detail + category
        $comments = Order::query()
            ->whereNotNull('comment')
            ->whereNotNull('rating')
            ->with(['customer', 'provider.expertDetail.category'])
            ->latest('order_date')
            ->take(8)
            ->get();

        return view('home', compact('categories', 'comments'));
    }
}
