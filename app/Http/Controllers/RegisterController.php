<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {

        $user = User::create([

            'username' => $request->username,

            'password' => Hash::make($request->password),

            'contact_number' => $request->contact_number,

            'role' => $request->role,

            'first_name' => $request->first_name,

            'last_name' => $request->last_name,

            'date_of_birth' => $request->date_of_birth,

        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $dashboardRoute = $user->dashboardRoute();
        return redirect()->route($dashboardRoute)
            ->with('success', 'ثبت نام با موفقیت انجام شد.');
    }
}
