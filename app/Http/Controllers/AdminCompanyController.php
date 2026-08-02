<?php
//--//
namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminCompanyRequest;
use App\Http\Requests\UpdateAdminCompanyRequest;
use App\Models\Company;
use App\Models\CompanyAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminCompanyController extends Controller
{
    // Adding a new company
    public function store(StoreAdminCompanyRequest $request)
    {
        $data = $request->safe();

        DB::transaction(function () use ($data) {
            $company = Company::create($data->only(['name', 'descriptions', 'founding_date']));

            $companyAdmin = CompanyAdmin::create([
                'companyID' => $company->companyID,
            ]);

            User::create([
                'username'         => $data['username'],
                'password'         => Hash::make($data['password']),
                'contact_number'   => $data['contact_number'],
                'role'             => 4,
                'first_name'       => $data['first_name'],
                'last_name'        => $data['last_name'] ?? null,
                'date_of_birth'    => $data['date_of_birth'] ?? null,
                'company_admin_id' => $companyAdmin->adminID,
            ]);
        });

        return back()->with('success', 'شرکت جدید به‌همراه مالکش با موفقیت ثبت شد.');
    }

    // Update a company
    public function update(UpdateAdminCompanyRequest $request, Company $company)
    {
        $owner = $company->owner();

        abort_unless($owner, 404, 'این شرکت هنوز مالکی نداره.');

        $data = $request->safe();

        $company->update($data->only(['name', 'descriptions', 'founding_date']));

        $ownerData = $data->only(['username', 'contact_number', 'first_name', 'last_name', 'date_of_birth']);

        if (! empty($data['password'])) {
            $ownerData['password'] = Hash::make($data['password']);
        }

        $owner->update($ownerData);

        return back()->with('success', 'اطلاعات شرکت و مالکش با موفقیت به‌روزرسانی شد.');
    }

    // Delete a company
    public function destroy(Request $request, Company $company)
    {
        DB::transaction(function () use ($company) {
            $repUsers = $company->admins->flatMap->users;

            $company->delete();

            // Soft-delete all admins
            $repUsers->each(fn(User $repUser) => $repUser->anonymizeAndDelete());
        });

        return back()->with('success', 'شرکت مورد نظر به‌همراه اطلاعات وابسته‌اش حذف شد.');
    }
}
