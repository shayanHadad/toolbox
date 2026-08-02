<?php
//--//
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    // Delete the user account
    public function destroy(Request $request)
    {
        $user = $request->user();

        // Validate the form data
        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'برای حذف حساب باید رمز عبورت رو وارد کنی.',
        ]);

        // Check the user password
        if (! Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withErrors(['password' => 'رمز عبور وارد شده صحیح نیست.'])
                ->with('deleteAccountOpen', true);
        }

        // If customer
        if ((int) $user->role === 1) {
            // Update all waiting orders to cancelled
            $user->customerOrders()
                ->where('status', Order::STATUS_WAITING)
                ->update(['status' => Order::STATUS_CANCELLED]);
        }

        // If expert
        if ((int) $user->role === 2) {
            // Update all waiting orders to rejected
            $user->providerOrders()
                ->where('status', Order::STATUS_WAITING)
                ->update(['status' => Order::STATUS_REJECTED]);
        }

        // Alter the username and phone number so they get freed for new users
        $user->anonymizeAndDelete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to home page after logging out the user
        return redirect()->route('home')->with('success', 'حساب کاربری‌ات با موفقیت حذف شد.');
    }
}
