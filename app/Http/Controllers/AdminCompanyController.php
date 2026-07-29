<?php

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
    /**
     * ادمین کل (role=0) یک شرکت جدید به‌همراه مالکش (role=4) می‌سازه.
     * چون هر کاربرِ نماینده‌ی شرکت از طریق company_admin_id به یک ردیف
     * company_admins وصل می‌شه، برای مالک جدید هم یک ردیف مخصوص به
     * خودش لازمه.
     */
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

    /**
     * ویرایش اطلاعات یک شرکت و مالکش (role=4) با هم، در یک فرم واحد.
     */
    public function update(UpdateAdminCompanyRequest $request, Company $company)
    {
        $owner = $company->owner();

        abort_unless($owner, 404, 'این شرکت هنوز مالکی نداره.');

        $data = $request->safe();

        $company->update($data->only(['name', 'descriptions', 'founding_date']));

        $ownerData = $data->only(['username', 'contact_number', 'first_name', 'last_name', 'date_of_birth']);

        // اگه فیلد رمز خالی گذاشته شده باشه، رمز فعلی مالک دست‌نخورده می‌مونه.
        if (! empty($data['password'])) {
            $ownerData['password'] = Hash::make($data['password']);
        }

        $owner->update($ownerData);

        return back()->with('success', 'اطلاعات شرکت و مالکش با موفقیت به‌روزرسانی شد.');
    }

    /**
     * حذف یک شرکت، به‌همراه مالک و ادمین‌هاش (role=4 و role=3).
     *
     * خودِ شرکت واقعاً حذف می‌شه (cascadeOnDelete رکوردهای company_admins
     * و company_bookmarks/company_categories رو پاک می‌کنه، و
     * nullOnDelete هم companyID سفارش‌ها/پیام‌های مربوطه رو null می‌کنه؛
     * یعنی تاریخچه‌شون می‌مونه، فقط دیگه به شرکتی وصل نیستن).
     *
     * اما کاربرهای نماینده‌ی شرکت (owner/admins) به‌جای حذف واقعی، soft
     * delete می‌شن؛ چون مدل User از SoftDeletes استفاده می‌کنه، دیگه
     * لازم نیست سفارش‌ها/پیام‌هایی که این کاربرها توشون طرف حساب بودن
     * رو دستی پاک کنیم — تاریخچه‌شون (مثلاً برای مشتری‌هایی که باهاشون
     * چت کرده بودن) دست‌نخورده می‌مونه.
     */
    public function destroy(Request $request, Company $company)
    {
        DB::transaction(function () use ($company) {
            $repUsers = $company->admins->flatMap->users;

            $company->delete();

            $repUsers->each(fn(User $repUser) => $repUser->anonymizeAndDelete());
        });

        return back()->with('success', 'شرکت مورد نظر به‌همراه اطلاعات وابسته‌اش حذف شد.');
    }
}
