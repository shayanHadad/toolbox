<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * پنل کاربری ادمین کل (role = 0) هنوز طراحی نشده؛
     * فعلاً فقط یه پیام «در حال ساخت» نشون داده می‌شه، تا حداقل
     * لاگین و مسیریابی برای کاربر ادمین با خطا مواجه نشه.
     */
    public function __invoke(Request $request)
    {
        return view('dashboard.admin', [
            'user' => $request->user(),
        ]);
    }
}
