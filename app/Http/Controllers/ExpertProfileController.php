<?php
//--//
namespace App\Http\Controllers;

use App\Http\Requests\UpdateExpertProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ExpertProfileController extends Controller
{
    public function update(UpdateExpertProfileRequest $request)
    {
        $user = $request->user();

        $userData = $request->safe()->only([
            'username',
            'first_name',
            'last_name',
            'contact_number',
            'date_of_birth',
        ]);

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $userData['profile_picture'] = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        $user->update($userData);

        $user->expertDetail()->updateOrCreate(
            ['userID' => $user->userID],
            [
                'categoryID'  => $request->category_id,
                'description' => $request->description,
                'resume'      => $request->resume,
            ]
        );

        return back()->with('success', 'اطلاعات پروفایل با موفقیت به‌روزرسانی شد.');
    }

    public function destroyPicture(Request $request)
    {
        $user = $request->user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);

            $user->update(['profile_picture' => null]);

            return back()->with('success', 'عکس پروفایل با موفقیت حذف شد.');
        }

        return back();
    }
}
