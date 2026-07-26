<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Order;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * حذف کامل و همیشگی حساب کاربری، به‌همراه تمام رکوردهای وابسته‌ای
     * که در دیتابیس cascade نمی‌شن (سفارش‌ها و پیام‌ها).
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

        try {
            DB::transaction(function () use ($user) {
                // سفارش‌هایی که کاربر توشون مشتری یا ارائه‌دهنده‌ی خدمات بوده.
                Order::where('customerID', $user->userID)
                    ->orWhere('providerID', $user->userID)
                    ->delete();

                // تمام پیام‌های ارسالی/دریافتیِ کاربر.
                Message::where('senderID', $user->userID)
                    ->orWhere('receiverID', $user->userID)
                    ->delete();

                // بوکمارک‌ها و پروفایل تخصصی (در صورت وجود) از طریق
                // cascadeOnDelete توی خود دیتابیس پاک می‌شن.
                $user->delete();
            });
        } catch (QueryException $e) {
            return back()
                ->with('error', 'حذف حساب با خطای غیرمنتظره‌ای مواجه شد. لطفاً دوباره تلاش کن یا با پشتیبانی تماس بگیر.')
                ->with('deleteAccountOpen', true);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'حساب کاربری‌ات و تمام اطلاعات مربوط بهش برای همیشه حذف شد.');
    }
}
