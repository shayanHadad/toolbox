<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyDashboardController extends Controller
{
    /**
     * پنل کاربری شرکت‌ها (role = 3) هنوز طراحی نشده؛
     * فعلاً فقط یه پیام «در حال ساخت» نشون داده می‌شه.
     */
    public function __invoke(Request $request)
    {
        return view('dashboard.company', [
            'user' => $request->user(),
        ]);
    }
}
