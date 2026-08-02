<?php
//--//
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
    // To add a new company admin
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

        // Create a new user with role = 3
        // Create a new company admin
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

    // Update an admin profile
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

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data);

        return back()->with('success', 'اطلاعات ادمین با موفقیت به‌روزرسانی شد.');
    }

    // Delete an admin by company owner
    public function destroy(Request $request, User $admin)
    {
        $owner = $request->user();

        $this->authorizeOwnershipOf($owner, $admin);

        DB::transaction(function () use ($admin, $owner) {
            $companyAdminId = $admin->company_admin_id;

            // Delete all messages between owner and admin
            Message::where(function ($q) use ($admin, $owner) {
                $q->where('senderID', $admin->userID)
                    ->where('receiverID', $owner->userID);
            })->orWhere(function ($q) use ($admin, $owner) {
                $q->where('senderID', $owner->userID)
                    ->where('receiverID', $admin->userID);
            })->delete();

            // Assign all the admin messages to company owner
            Message::where('senderID', $admin->userID)
                ->update(['senderID' => $owner->userID]);

            // Assign all the admin messages to company owner
            Message::where('receiverID', $admin->userID)
                ->update(['receiverID' => $owner->userID]);

            // Delete admin and free up its usrname and phone number
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

    // Check the ownership of the company
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
