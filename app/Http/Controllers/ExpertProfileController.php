<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateExpertProfileRequest;
use Illuminate\Support\Facades\Hash;

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
}