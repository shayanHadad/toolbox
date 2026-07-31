<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $data = $request->safe()->only([
            'username',
            'first_name',
            'last_name',
            'contact_number',
            'date_of_birth',
        ]);

        // Only touch the password if the user actually typed a new one.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // فقط اگه واقعاً یه فایلِ جدید فرستاده شده باشه دست به عکس می‌زنیم؛
        // وگرنه چون فیلد nullable/optional‌ه، عکسِ قبلی نباید پاک بشه.
        // فایل قبلاً توسط UpdateProfileRequest (image/mimes/max/dimensions)
        // اعتبارسنجی شده. store() خودش یه اسمِ رندوم و امن تولید می‌کنه
        // (نه اسمِ اصلیِ فایل که کاربر فرستاده)، پس مسیر/اسم فایل هیچ‌وقت
        // مستقیماً از ورودیِ کاربر ساخته نمی‌شه.
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $data['profile_picture'] = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        $user->update($data);

        return back()->with('success', 'اطلاعات پروفایل با موفقیت به‌روزرسانی شد.');
    }

    // فقط اگه کاربر واقعاً عکسی توی دیتابیس ثبت کرده باشه چیزی حذف می‌شه؛
    // در غیر این صورت هیچ کاری انجام نمی‌شه (نه خطا، نه درخواست به دیسک).
    public function destroyPicture(Request $request)
    {
        $user = $request->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);

            $user->update(['profile_picture' => null]);

            return back()->with('success', 'عکس پروفایل با موفقیت حذف شد.');
        }

        return back();
    }
}