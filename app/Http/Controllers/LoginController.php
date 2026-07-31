<?php
//--//
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show the login view
    public function show()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $field = $request->loginField(); // username or contact_number

        $credentials = [
            $field     => $request->input('login'),
            'password' => $request->input('password'),
        ];

        // Failed login
        // remember comes from the checkbox in form
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['login' => 'نام کاربری/شماره تماس یا رمز عبور اشتباه است.'])
                ->onlyInput('login'); // keeps the login input
        }

        // If login was successful regenrate the session-ID
        $request->session()->regenerate();

        /** @var User $user */
        // Fetch the user data
        $user = Auth::user();

        // Redirect to dashboard based on role
        return redirect()
            ->route($user->dashboardRoute())
            ->with('success', 'خوش آمدید ' . $user->first_name);
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate(); // Delete the session
        $request->session()->regenerateToken(); // Generate a new csrf token

        return redirect()->route('login');
    }
}
