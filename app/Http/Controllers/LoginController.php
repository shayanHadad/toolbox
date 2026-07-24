<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $field = $request->loginField(); // 'username' یا 'contact_number'

        $credentials = [
            $field     => $request->input('login'),
            'password' => $request->input('password'),
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['login' => 'نام کاربری/شماره تماس یا رمز عبور اشتباه است.'])
                ->onlyInput('login');
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        return redirect()
            ->route($user->dashboardRoute())
            ->with('success', 'خوش آمدید ' . $user->first_name);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}