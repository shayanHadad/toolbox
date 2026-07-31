<?php
//--//
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    // All users are authorized
    public function authorize(): bool
    {
        return true;
    }

    // Form rules
    public function rules(): array
    {
        return [
            'login'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    // Error messages
    public function messages(): array
    {
        return [
            'login.required'    => 'وارد کردن نام کاربری یا شماره تماس الزامی است.',
            'password.required' => 'وارد کردن رمز عبور الزامی است.',
        ];
    }

    // Determine if the login input is username OR‌ contact_number
    public function loginField(): string
    {
        $login = $this->input('login');

        if (preg_match('/^[0-9]+$/', $login)) {
            return 'contact_number';
        }

        return 'username';
    }
}
