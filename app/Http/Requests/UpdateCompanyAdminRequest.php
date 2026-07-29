<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        // مجوز اصلی (مالک بودن + تعلق ادمین به همون شرکت) توی کنترلر
        // چک می‌شه، چون به رکورد company_admins نیاز داره که فقط توی
        // کنترلر در دسترسه. اینجا فقط نقش کاربر رو بررسی می‌کنیم.
        return (int) $this->user()?->role === 4;
    }

    public function rules(): array
    {
        // یوزرآیدی خود ادمین در حال ویرایش از روی روت گرفته می‌شه تا
        // بشه توی unique اونو از قلم انداخت (خودش نباید با خودش تداخل کنه).
        $admin = $this->route('admin');

        return [
            'username' => [
                'required',
                'alpha_dash',
                'max:50',
                'unique:users,username,' . $admin->userID . ',userID',
            ],

            'contact_number' => [
                'required',
                'regex:/^09\d{9}$/',
                'unique:users,contact_number,' . $admin->userID . ',userID',
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

            'date_of_birth' => [
                'nullable',
                'date',
                'after:1900-01-01',
            ],

            // موقع ویرایش، رمز اختیاریه؛ اگه خالی بمونه رمز قبلی دست‌نخورده می‌مونه.
            'password' => [
                'nullable',
                'min:8',
                'regex:/^[A-Za-z0-9!@#$%^&*()_+\-=]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'نام کاربری الزامی است.',
            'username.alpha_dash' => 'نام کاربری فقط می‌تواند شامل حروف انگلیسی، اعداد و _ باشد.',
            'username.unique' => 'این نام کاربری قبلاً ثبت شده است.',

            'contact_number.required' => 'شماره موبایل الزامی است.',
            'contact_number.regex' => 'شماره موبایل معتبر نیست.',
            'contact_number.unique' => 'این شماره موبایل قبلاً ثبت شده است.',

            'first_name.required' => 'نام الزامی است.',

            'date_of_birth.date' => 'تاریخ تولد معتبر نیست.',
            'date_of_birth.after' => 'سال تولد باید بعد از ۱۹۰۰ باشد.',

            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.regex' => 'رمز عبور فقط باید شامل کاراکترهای انگلیسی باشد.',
        ];
    }
}
