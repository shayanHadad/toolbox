<?php
//--//
namespace App\Http\Controllers;

use App\Http\Requests\UpdateCompanyProfileRequest;

class CompanyProfileController extends Controller
{
    // Only company owner can update the company profile
    public function update(UpdateCompanyProfileRequest $request)
    {
        $company = $request->user()->companyAdmin?->company;

        abort_unless($company, 404);

        $data = $request->safe()->only(['name', 'descriptions', 'founding_date']);

        $company->update($data);

        // To sync many to many relationships
        $company->categories()->sync($request->input('categories', []));

        return back()->with('success', 'اطلاعات شرکت با موفقیت به‌روزرسانی شد.');
    }
}
