<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * نمایش صفحه‌ی تماس با ما.
     */
    public function index()
    {
        return view('contact');
    }

    /**
     * دریافت و ارسال فرم تماس.
     * این متد فقط برای کاربرهای لاگین‌کرده مجازه (middleware auth روی روت).
     */
    public function send(Request $request)
    {
        // نام و ایمیل هیچ‌وقت از ورودی کاربر خونده نمی‌شه، همیشه از کاربر لاگین‌شده میاد
        // (چون در فرم به‌صورت readonly هستن، ولی سمت سرور هم نباید بهشون اعتماد کرد)
        $user = Auth::user();

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'max:255',
                'email:rfc,dns',
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
        $payload = [
            'name'    => $user->name,
            'email'   => $validated['email'],
            'subject' => $validated['subject'] ?? 'بدون موضوع',
            'message' => $validated['message'],
        ];
        try {
            Mail::raw(
                "نام: {$payload['name']}\n" .
                    "ایمیل: {$payload['email']}\n" .
                    "موضوع: {$payload['subject']}\n\n" .
                    "متن پیام:\n{$payload['message']}",
                function ($mail) use ($payload) {
                    $mail->to(config('mail.contact_to', config('mail.from.address')))
                        ->subject('پیام جدید از فرم تماس با ما: ' . $payload['subject'])
                        ->replyTo($payload['email'], $payload['name']);
                }
            );
        } catch (\Throwable $e) {
            Log::error('Contact form mail failed: ' . $e->getMessage());

            return back()->withInput()->with('error', 'ارسال پیام با مشکل مواجه شد، لطفاً دوباره تلاش کن.');
        }

        return back()->with('status', 'پیامت با موفقیت ارسال شد، به‌زودی باهات در تماس خواهیم بود.');
    }
}
