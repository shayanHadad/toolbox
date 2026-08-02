<?php
//--//
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (int) $this->user()?->role === 0;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'descriptions' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'founding_date' => [
                'nullable',
                'date',
                'after:1900-01-01',
                'before_or_equal:today',
            ],

            'username' => [
                'required',
                'alpha_dash',
                'max:50',
                'unique:users,username',
            ],

            'contact_number' => [
                'required',
                'regex:/^09\d{9}$/',
                'unique:users,contact_number',
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

            'password' => [
                'required',
                'min:8',
                'regex:/^[A-Za-z0-9!@#$%^&*()_+\-=]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'نام شرکت الزامی است.',
            'name.max'      => 'نام شرکت نمی‌تونه بیشتر از ۱۵۰ کاراکتر باشه.',

            'descriptions.max' => 'توضیحات نمی‌تونه بیشتر از ۲۰۰۰ کاراکتر باشه.',

            'founding_date.date'            => 'تاریخ تأسیس معتبر نیست.',
            'founding_date.after'           => 'سال تأسیس باید بعد از ۱۹۰۰ باشه.',
            'founding_date.before_or_equal' => 'تاریخ تأسیس نمی‌تونه در آینده باشه.',

            'username.required'   => 'نام کاربری مالک الزامی است.',
            'username.alpha_dash' => 'نام کاربری فقط می‌تواند شامل حروف انگلیسی، اعداد و _ باشد.',
            'username.unique'     => 'این نام کاربری قبلاً ثبت شده است.',

            'contact_number.required' => 'شماره موبایل مالک الزامی است.',
            'contact_number.regex'    => 'شماره موبایل معتبر نیست.',
            'contact_number.unique'   => 'این شماره موبایل قبلاً ثبت شده است.',

            'first_name.required' => 'نام مالک الزامی است.',

            'password.required' => 'رمز عبور مالک الزامی است.',
            'password.min'      => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.regex'    => 'رمز عبور فقط باید شامل کاراکترهای انگلیسی باشد.',
        ];
    }
}
