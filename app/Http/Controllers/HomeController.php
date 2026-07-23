<?php

namespace App\Http\Controllers;

use App\Models\WorkCategory;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function fetchWorkCategories()
    {
        $categories = WorkCategory::orderBy('category_name')->take(7)->get();

        return view('home', compact('categories'));
    }
}
