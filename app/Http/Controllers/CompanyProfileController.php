<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCompanyProfileRequest;

class CompanyProfileController extends Controller
{
    /**
     * ویرایش اطلاعات شرکت (نام، توضیحات، تاریخ تأسیس و دسته‌بندی‌ها).
     * فقط مالک شرکت (role=4) اجازه‌ی این کار رو داره؛ ادمین‌های شرکت
     * (role=3) فقط می‌تونن به پیام‌ها و سفارش‌ها رسیدگی کنن.
     * این محدودیت هم روی روت (میدلور role:4) و هم توی
     * UpdateCompanyProfileRequest::authorize() اعمال شده.
     */
    public function update(UpdateCompanyProfileRequest $request)
    {
        $company = $request->user()->companyAdmin?->company;

        abort_unless($company, 404);

        $data = $request->safe()->only(['name', 'descriptions', 'founding_date']);

        $company->update($data);

        $company->categories()->sync($request->input('categories', []));

        return back()->with('success', 'اطلاعات شرکت با موفقیت به‌روزرسانی شد.');
    }
}
