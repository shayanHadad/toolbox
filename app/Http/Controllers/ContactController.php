<?php
//--//
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    // Show contact page
    public function index()
    {
        return view('contact');
    }

    public function send(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'max:255',
                'email:rfc,dns', // Check the structure and domain
            ],

            'subject' => [
                'nullable',
                'string',
                'max:150'
            ],

            'message' => [
                'required',
                'string',
                'max:5000'
            ],

        ], [

            'email.required' => 'لطفاً ایمیل خود را وارد کنید.',

            'email.email' => 'فرمت ایمیل معتبر نیست.',

            'email.regex' => 'ایمیل وارد شده معتبر نیست.',

            'email.max' => 'ایمیل بیش از حد طولانی است.',

            'message.required' => 'وارد کردن متن پیام الزامیه.',

            'message.max' => 'متن پیام نمی‌تواند بیشتر از ۵۰۰۰ کاراکتر باشد.',

            'subject.max' => 'موضوع نمی‌تواند بیشتر از ۱۵۰ کاراکتر باشد.',
        ]);
        // Tidy up the user inputs
        $payload = [
            'name' => trim($user->first_name . ' ' . $user->last_name),
            'email'   => $validated['email'],
            'subject' => $validated['subject'] ?? 'بدون موضوع',
            'message' => $validated['message'],
        ];

        //
        // Complete sending email later
        //
        return back()->with('error', 'همه‌ی پردازش‌ها انجام شده اما ایمیل ارسال نشد.');
    }
}
