<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * حذف (نرم) حساب کاربری. چون مدل User از SoftDeletes استفاده می‌کنه،
     * دیگه لازم نیست سفارش‌ها و پیام‌های کاربر رو دستی پاک کنیم؛ ردیف
     * کاربر فقط deleted_at می‌گیره، حساب دیگه قابل لاگین نیست و از همه‌ی
     * لیست‌های عمومی (متخصص‌ها، شرکت‌ها و ...) خودکار کنار می‌ره، ولی
     * تاریخچه‌ی سفارش‌ها/پیام‌هایی که طرفِ دیگه‌شون (مثل یک شرکت یا
     * متخصص) داره، دست‌نخورده می‌مونه.
     *
     * برای مشتری (role=1) و متخصص (role=2) مجازه؛ این محدودیت روی
     * روت با میدلور role:1,2 اعمال شده.
     *
     * قبل از حذف، رمز عبور فعلی کاربر رو می‌گیریم تا مطمئن بشیم
     * خودِ صاحبِ حساب داره این کار رو انجام می‌ده.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'برای حذف حساب باید رمز عبورت رو وارد کنی.',
        ]);

        if (! Hash::check($request->input('password'), $user->password)) {
            return back()
                ->withErrors(['password' => 'رمز عبور وارد شده صحیح نیست.'])
                ->with('deleteAccountOpen', true);
        }

        // username و contact_number مخدوش می‌شن (چون یونیک‌ان و ردیف
        // فیزیکاً باقی می‌مونه)، بعد ردیف soft-delete می‌شه.
        $user->anonymizeAndDelete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'حساب کاربری‌ات با موفقیت حذف شد.');
    }
}
