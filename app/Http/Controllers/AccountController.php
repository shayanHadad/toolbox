<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * حذف (نرم) حساب کاربری. چون مدل User از SoftDeletes استفاده می‌کنه،
     * لازم نیست سفارش‌ها و پیام‌های کاربر رو دستی پاک کنیم؛ ردیف کاربر
     * فقط deleted_at می‌گیره، حساب دیگه قابل لاگین نیست و از همه‌ی
     * لیست‌های عمومی (متخصص‌ها، شرکت‌ها و ...) خودکار کنار می‌ره.
     *
     * برای مشتری (role=1): سفارش‌های در انتظارش لغو می‌شن (پایین‌تر)،
     * و طبق منطقِ MessageController، دیگه توی لیستِ پیام‌های متخصص/شرکت
     * دیده نمی‌شه. سفارش‌های finished‌شده و نظر/امتیازِ ثبت‌شده روش
     * دست‌نخورده می‌مونه.
     * برای متخصص (role=2): سفارش‌های در انتظاری که provider‌شونه رد
     * می‌شن (پایین‌تر)، وگرنه مشتری برای همیشه با یه سفارشِ «در انتظار
     * تأیید» می‌موند که هیچ‌وقت کسی تأییدش نمی‌کرد. تاریخچه‌ی سفارش‌ها/
     * پیام‌هایی که مشتری‌های طرفِ حسابش دارن، دست‌نخورده می‌مونه.
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

        if ((int) $user->role === 1) {
            // سفارش‌های در انتظارِ این مشتری لغو می‌شن (status=cancelled)،
            // نه حذف؛ چون تاریخچه برای طرفِ مقابل (متخصص/شرکت) باید بمونه.
            // این کار خودش باعث می‌شه از لیستِ درخواست‌ها هم کنار برن،
            // چون اون لیست فقط سفارش‌های waiting رو نشون می‌ده. سفارش‌های
            // finished و نظر/امتیازی که مشتری قبلاً ثبت کرده دست‌نخورده
            // می‌مونه.
            $user->customerOrders()
                ->where('status', Order::STATUS_WAITING)
                ->update(['status' => Order::STATUS_CANCELLED]);
        }

        if ((int) $user->role === 2) {
            // سفارش‌های در انتظاری که این متخصص provider‌شونه رد می‌شن
            // (status=rejected)، همون وضعیتی که خودِ متخصص با دکمه‌ی «رد
            // کردن» می‌ذاشت؛ وگرنه بعد از حذف حساب، دیگه هیچ‌وقت کسی
            // نیست تأییدش کنه و مشتری برای همیشه با یه سفارشِ «در انتظار
            // تأیید» به یک متخصصِ دیگه‌وجودنداشته می‌موند.
            $user->providerOrders()
                ->where('status', Order::STATUS_WAITING)
                ->update(['status' => Order::STATUS_REJECTED]);
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
