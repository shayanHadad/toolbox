<?php
//--//
namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Company;
use App\Models\CompanyBookmark;
use App\Models\User;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    // List of bookmarked experts and companies for user (role = 1)
    public function index(Request $request)
    {
        $user = $request->user();

        // Fetch bookmarked experts
        $providers = $user->bookmarkedProviders()
            ->with('expertDetail.category') // Load related information
            ->get();

        // Fetch bookmarked companies
        $companies = $user->bookmarkedCompanies()
            ->with('categories')
            ->get();

        // return the view with fetched data
        return view('bookmarks.index', [
            'providers' => $providers,
            'companies' => $companies,
        ]);
    }

    // Add/Delete an expert to bookmarks for users with (role = 1)
    public function toggle(Request $request, User $expert)
    {
        abort_unless($expert->role == 2 && $expert->expertDetail, 404);

        /** @var User $user */
        $user = $request->user();

        // Get the bookmarked expert from database
        $bookmark = Bookmark::where('customerID', $user->userID)
            ->where('providerID', $expert->userID)
            ->first();

        // If it existed delete it from bookmarks
        if ($bookmark) {
            $bookmark->delete();
            $message = 'متخصص از لیست بوکمارک‌هات حذف شد.';
        } else { // If it didn't exist add it to bookmarks
            Bookmark::create([
                'customerID' => $user->userID,
                'providerID' => $expert->userID,
            ]);
            $message = 'متخصص به لیست بوکمارک‌هات اضافه شد.';
        }

        // Return to previous page with proper message
        return back()->with('success', $message);
    }

    // Add/Delete a company to bookmarks by user (role = 1)
    public function toggleCompany(Request $request, Company $company)
    {
        /** @var User $user */
        $user = $request->user();

        // Look for the bookmarked company in database
        $bookmark = CompanyBookmark::where('customerID', $user->userID)
            ->where('companyID', $company->companyID)
            ->first();

        // If it existed then delete it
        if ($bookmark) {
            $bookmark->delete();
            $message = 'شرکت از لیست بوکمارک‌هات حذف شد.';
        } else { // Add it to bookmarks
            CompanyBookmark::create([
                'customerID' => $user->userID,
                'companyID'  => $company->companyID,
            ]);
            $message = 'شرکت به لیست بوکمارک‌هات اضافه شد.';
        }

        return back()->with('success', $message);
    }
}
