<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpertProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
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
                'confirmed',
            ],

            // expert_details fields
            'category_id' => [
                'required',
                'integer',
                'exists:work_categories,categoryID',
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'resume' => [
                'nullable',
                'string',
                'max:5000',
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

            'category_id.required' => 'انتخاب دسته‌بندی الزامی است.',
            'category_id.exists' => 'دسته‌بندی انتخاب شده معتبر نیست.',

            'description.max' => 'توضیحات نباید بیشتر از ۲۰۰۰ کاراکتر باشد.',
            'resume.max' => 'رزومه نباید بیشتر از ۵۰۰۰ کاراکتر باشد.',
        ];
    }
}