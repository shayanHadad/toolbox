<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyAdminRequest;
use App\Http\Requests\UpdateCompanyAdminRequest;
use App\Models\CompanyAdmin;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyAdminController extends Controller
{
    /**
     * مالک شرکت (role=4) یک کاربر جدید با role=3 می‌سازه که به‌عنوان
     * ادمین همون شرکت عمل می‌کنه (می‌تونه به پیام‌ها و سفارش‌های شرکت
     * رسیدگی کنه، ولی نمی‌تونه اطلاعات شرکت رو ویرایش کنه).
     *
     * چون هر کاربر از طریق ستون company_admin_id به یک رکورد
     * company_admins وصل می‌شه، برای هر ادمین جدید یک رکورد
     * company_admins مخصوص به خودش هم ساخته می‌شه.
     */
    public function store(StoreCompanyAdminRequest $request)
    {
        $owner = $request->user();
        $company = $owner->companyAdmin?->company;

        abort_unless($company, 404);

        $data = $request->safe()->only([
            'username',
            'contact_number',
            'first_name',
            'last_name',
            'date_of_birth',
            'password',
        ]);

        DB::transaction(function () use ($company, $data) {
            $companyAdmin = CompanyAdmin::create([
                'companyID' => $company->companyID,
            ]);

            User::create([
                'username'         => $data['username'],
                'password'         => Hash::make($data['password']),
                'contact_number'   => $data['contact_number'],
                'role'             => 3,
                'first_name'       => $data['first_name'],
                'last_name'        => $data['last_name'] ?? null,
                'date_of_birth'    => $data['date_of_birth'] ?? null,
                'company_admin_id' => $companyAdmin->adminID,
            ]);
        });

        return back()->with('success', 'ادمین جدید با موفقیت برای شرکت ثبت شد.');
    }

    /**
     * ویرایش یک ادمین (role=3) که برای همون شرکتِ مالک کار می‌کنه.
     */
    public function update(UpdateCompanyAdminRequest $request, User $admin)
    {
        $this->authorizeOwnershipOf($request->user(), $admin);

        $data = $request->safe()->only([
            'username',
            'contact_number',
            'first_name',
            'last_name',
            'date_of_birth',
            'password',
        ]);

        // اگه فیلد رمز خالی گذاشته شده باشه، رمز فعلی دست‌نخورده می‌مونه.
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data);

        return back()->with('success', 'اطلاعات ادمین با موفقیت به‌روزرسانی شد.');
    }

    /**
     * حذف یک ادمین (role=3) که برای همون شرکتِ مالک کار می‌کنه.
     *
     * طبق تصمیمِ محصولی، پیام‌های این ادمین به‌جای اینکه فقط بمونن به
     * اسم «ادمین حذف‌شده»، به owner ای که داره حذفش می‌کنه (role=4)
     * منتقل می‌شن؛ یعنی هر جا senderID یا receiverID برابر آیدی این
     * ادمین بوده، بعد از حذف برابر آیدی owner می‌شه و مکالمه دست‌نخورده
     * باقی می‌مونه، فقط از این به بعد به owner نسبت داده می‌شه.
     *
     * تنها استثنا، پیام‌های خصوصیِ بین خودِ owner و همین ادمینه؛ چون اگه
     * اون‌ها رو هم منتقل کنیم، هم senderID و هم receiverID برابر آیدی
     * owner می‌شن (یعنی «پیام از owner به owner») که بی‌معنیه، پس این
     * دسته رو مستقیماً پاک می‌کنیم.
     *
     * خودِ کاربر با anonymizeAndDelete() به‌صورت نرم حذف می‌شه (نه با
     * FK دردسر داره، نه اجازه‌ی لاگین دوباره داره)؛ فقط username/
     * contact_number مخدوش می‌شن تا مالک بتونه بعداً یه ادمین جدید با
     * همون نام‌کاربری یا شماره تماس بسازه.
     *
     * ردیف company_admins مربوطه هم اگه بعد از حذف، کاربر دیگه‌ای (غیر
     * از این ادمینِ حذف‌شده) بهش وصل نباشه، پاک می‌شه تا ردیف یتیم توی
     * جدول نمونه.
     */
    public function destroy(Request $request, User $admin)
    {
        $owner = $request->user();

        $this->authorizeOwnershipOf($owner, $admin);

        DB::transaction(function () use ($admin, $owner) {
            $companyAdminId = $admin->company_admin_id;

            // مکالمه‌ی خصوصیِ بین owner و همین ادمین، چون بعد از انتقال
            // دو طرفش یکی می‌شه، مستقیماً پاک می‌شه.
            Message::where(function ($q) use ($admin, $owner) {
                $q->where('senderID', $admin->userID)
                    ->where('receiverID', $owner->userID);
            })->orWhere(function ($q) use ($admin, $owner) {
                $q->where('senderID', $owner->userID)
                    ->where('receiverID', $admin->userID);
            })->delete();

            // بقیه‌ی پیام‌های ارسالی/دریافتیِ ادمین به owner منتقل می‌شه
            // به‌جای اینکه پاک بشه.
            Message::where('senderID', $admin->userID)
                ->update(['senderID' => $owner->userID]);

            Message::where('receiverID', $admin->userID)
                ->update(['receiverID' => $owner->userID]);

            $admin->anonymizeAndDelete();

            if ($companyAdminId) {
                $stillUsed = User::where('company_admin_id', $companyAdminId)->exists();

                if (! $stillUsed) {
                    CompanyAdmin::where('adminID', $companyAdminId)->delete();
                }
            }
        });

        return back()->with('success', 'ادمین مورد نظر حذف شد.');
    }

    /**
     * مطمئن می‌شه $admin واقعاً یک ادمینِ (role=3) متعلق به همون شرکتیه
     * که $owner (role=4) مالکشه؛ وگرنه ۴۰۴ برمی‌گردونه تا اطلاعاتی از
     * وجود/عدم‌وجود کاربر توی شرکت‌های دیگه لو نره.
     */
    private function authorizeOwnershipOf(User $owner, User $admin): void
    {
        $ownerCompanyId = $owner->companyAdmin?->company?->companyID;

        abort_unless($ownerCompanyId, 404);

        $adminCompanyId = $admin->companyAdmin?->company?->companyID;

        abort_unless(
            $admin->isCompanyAdmin() && $adminCompanyId === $ownerCompanyId,
            404
        );
    }
}
