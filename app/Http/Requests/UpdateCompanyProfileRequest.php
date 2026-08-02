<?php
//--//
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (int) $this->user()?->role === 4;
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

            'categories' => [
                'nullable',
                'array',
            ],

            'categories.*' => [
                'integer',
                'exists:work_categories,categoryID',
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

            'categories.*.exists' => 'یکی از دسته‌بندی‌های انتخاب‌شده معتبر نیست.',
        ];
    }
}
