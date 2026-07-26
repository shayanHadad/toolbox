<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Company;
use App\Models\CompanyBookmark;
use App\Models\User;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * لیست متخصص‌ها و شرکت‌هایی که کاربر (مشتری) بوکمارک کرده.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $providers = $user->bookmarkedProviders()
            ->with('expertDetail.category')
            ->get();

        $companies = $user->bookmarkedCompanies()
            ->with('categories')
            ->get();

        return view('bookmarks.index', [
            'providers' => $providers,
            'companies' => $companies,
        ]);
    }

    /**
     * بوکمارک کردن / حذف بوکمارک یک متخصص (toggle).
     * فقط برای کاربرهای لاگین‌کرده با role=1 (مشتری) مجاز است؛
     * این محدودیت روی روت با میدلور role:1 اعمال شده.
     */
    public function toggle(Request $request, User $expert)
    {
        abort_unless($expert->role == 2 && $expert->expertDetail, 404);

        /** @var User $user */
        $user = $request->user();

        $bookmark = Bookmark::where('customerID', $user->userID)
            ->where('providerID', $expert->userID)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $message = 'متخصص از لیست بوکمارک‌هات حذف شد.';
        } else {
            Bookmark::create([
                'customerID' => $user->userID,
                'providerID' => $expert->userID,
            ]);
            $message = 'متخصص به لیست بوکمارک‌هات اضافه شد.';
        }

        return back()->with('success', $message);
    }

    /**
     * بوکمارک کردن / حذف بوکمارک یک شرکت (toggle).
     * فقط برای کاربرهای لاگین‌کرده با role=1 (مشتری) مجاز است؛
     * این محدودیت روی روت با میدلور role:1 اعمال شده.
     */
    public function toggleCompany(Request $request, Company $company)
    {
        /** @var User $user */
        $user = $request->user();

        $bookmark = CompanyBookmark::where('customerID', $user->userID)
            ->where('companyID', $company->companyID)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $message = 'شرکت از لیست بوکمارک‌هات حذف شد.';
        } else {
            CompanyBookmark::create([
                'customerID' => $user->userID,
                'companyID'  => $company->companyID,
            ]);
            $message = 'شرکت به لیست بوکمارک‌هات اضافه شد.';
        }

        return back()->with('success', $message);
    }
}
