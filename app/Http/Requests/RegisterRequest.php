<?php
//--//
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    // All users can send a register request
    public function authorize(): bool
    {
        return true;
    }

    // Validating the form inputs from user
    public function rules(): array
    {
        return [

            'username' => [
                'required',
                'alpha_dash',
                'unique:users,username',
                'max:50'
            ],

            'contact_number' => [
                'required',
                'regex:/^09\d{9}$/',
                'unique:users,contact_number'
            ],

            'role' => [
                'required',
                'in:1,2'
            ],

            'first_name' => [
                'required',
                'string',
                'max:100'
            ],

            'last_name' => [
                'nullable',
                'string',
                'max:100'
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'after:1900-01-01'
            ],

            'password' => [
                'required',
                'min:8',
                'regex:/^[A-Za-z0-9!@#$%^&*()_+\-=]+$/' // Upper and lower case letters + numbrs + symbols
            ],

        ];
    }

    // Setting an error message for each situation
    public function messages(): array
    {
        return [

            'username.required' => 'نام کاربری الزامی است.',
            'username.alpha_dash' => 'نام کاربری فقط می‌تواند شامل حروف انگلیسی، اعداد و _ باشد.',
            'username.unique' => 'این نام کاربری قبلاً ثبت شده است.',

            'contact_number.required' => 'شماره موبایل الزامی است.',
            'contact_number.regex' => 'شماره موبایل معتبر نیست.',
            'contact_number.unique' => 'این شماره موبایل قبلاً ثبت شده است.',

            'role.required' => 'انتخاب نقش الزامی است.',
            'role.in' => 'نقش انتخاب شده معتبر نیست.',

            'first_name.required' => 'نام الزامی است.',

            'date_of_birth.date' => 'تاریخ تولد معتبر نیست.',
            'date_of_birth.after' => 'سال تولد باید بعد از ۱۹۰۰ باشد.',

            'password.required' => 'رمز عبور الزامی است.',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.regex' => 'رمز عبور فقط باید شامل کاراکترهای انگلیسی باشد.',
        ];
    }
}
