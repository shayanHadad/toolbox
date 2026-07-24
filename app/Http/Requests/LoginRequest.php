<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // یک فیلد واحد که هم می‌تونه نام کاربری باشه هم شماره تماس
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required'    => 'وارد کردن نام کاربری یا شماره تماس الزامی است.',
            'password.required' => 'وارد کردن رمز عبور الزامی است.',
        ];
    }

    /**
     * تشخیص اینکه مقدار وارد شده شماره تماس هست یا نام کاربری،
     * و برگردوندن ستون مناسب برای Auth::attempt.
     */
    public function loginField(): string
    {
        return preg_match('/^[0-9]+$/', $this->input('login')) ? 'contact_number' : 'username';
    }
}