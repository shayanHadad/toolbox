<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\WorkCategory;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function fetchData()
    {
        $categories = WorkCategory::orderBy('category_name')->take(7)->get();

        $comments = Order::query()
            ->whereNotNull('comment')
            ->whereNotNull('rating')
            ->with(['customer', 'provider.expertDetail.category'])
            ->latest('order_date')
            ->take(6)
            ->get();

        return view('home', compact('categories', 'comments'));
    }
}