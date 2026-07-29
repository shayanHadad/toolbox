<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // userID of the currently logged in user, so unique checks
        // don't reject the user's own current username/number.
        $userID = $this->user()->userID;

        return [
            'username' => [
                'required',
                'alpha_dash',
                'max:50',
                'unique:users,username,' . $userID . ',userID',
            ],
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'contact_number' => [
                'required',
                'regex:/^09\d{9}$/',
                'unique:users,contact_number,' . $userID . ',userID',
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'after:1900-01-01',
            ],
            'password' => [
                'nullable',
                'min:8',
                'regex:/^[A-Za-z0-9!@#$%^&*()_+\-=]+$/',
                'confirmed', // requires a matching password_confirmation field
            ],
            // آپلود عکس پروفایل اختیاریه. چند لایه محدودیت داریم:
            // - image: فقط چیزی که واقعاً محتوای یک عکسه قبول می‌شه
            //   (بر پایه‌ی محتوای فایل، نه اسم/پسوندش؛ یه فایل php که
            //   اسمش رو gif گذاشته باشن رد می‌شه).
            // - mimes: نوع فایل باید بر اساس محتوای واقعیش یکی از این‌ها
            //   باشه (نه بر اساس Content-Type ای که خودِ کاربر می‌فرسته).
            // - max: سقفِ حجم ۲ مگابایت، جلوی سوءاستفاده از فضای دیسک رو می‌گیره.
            // - dimensions: یه لایه‌ی اضافه‌ی اطمینان که واقعاً ابعاد یک
            //   عکسِ معقول رو داره.
            'profile_picture' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'نام کاربری الزامی است.',
            'username.alpha_dash' => 'نام کاربری فقط می‌تواند شامل حروف انگلیسی، اعداد و _ باشد.',
            'username.unique' => 'این نام کاربری قبلاً ثبت شده است.',

            'first_name.required' => 'نام الزامی است.',

            'contact_number.required' => 'شماره موبایل الزامی است.',
            'contact_number.regex' => 'شماره موبایل معتبر نیست.',
            'contact_number.unique' => 'این شماره موبایل قبلاً ثبت شده است.',

            'date_of_birth.date' => 'تاریخ تولد معتبر نیست.',
            'date_of_birth.after' => 'سال تولد باید بعد از ۱۹۰۰ باشد.',

            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.regex' => 'رمز عبور فقط باید شامل کاراکترهای انگلیسی باشد.',
            'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد.',

            'profile_picture.image' => 'فایل انتخاب‌شده باید یک تصویر معتبر باشد.',
            'profile_picture.mimes' => 'فرمت عکس باید jpeg، jpg، png یا webp باشد.',
            'profile_picture.max' => 'حجم عکس نباید بیشتر از ۲ مگابایت باشد.',
            'profile_picture.dimensions' => 'ابعاد عکس معتبر نیست.',
        ];
    }
}