<?php
//--//
namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;


class RegisterController extends Controller
{
    // Showing the register page
    public function show()
    {
        return view('auth.register');
    }

    // Receive a request and serve it 
    public function store(RegisterRequest $request)
    {

        // Creating a new user row in database
        $user = User::create([

            'username' => $request->username,

            'password' => Hash::make($request->password),

            'contact_number' => $request->contact_number,

            'role' => $request->role,

            'first_name' => $request->first_name,

            'last_name' => $request->last_name,

            'date_of_birth' => $request->date_of_birth,

        ]);

        // Login the user after registration automatically
        Auth::login($user);

        // Regenarating the session-ID to prevent << session fixation attack >>‌
        $request->session()->regenerate();

        // Getting the users dashboard url based on its role and redirect the user there
        $dashboardRoute = $user->dashboardRoute();
        return redirect()->route($dashboardRoute)
            ->with('success', 'ثبت نام با موفقیت انجام شد.'); // For blade file to know if it was successful
    }
}
