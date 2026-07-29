@extends('layouts.app')

@section('title', 'تماس با ما | جعبه‌ابزار')

<link rel="stylesheet" href="{{ asset('css/contact.css') }}">


@section('content')

<section class="contact-hero">
    <div class="container hero-content">
        <div class="hero-eyebrow">
            <span class="dot"></span>
            تماس با ما
        </div>

        <h1>سوالی داری؟ <em>خوشحال می‌شیم</em> کمکت کنیم</h1>

        <p>
            برای هر سوال، پیشنهاد یا مشکلی که داری، فرم زیر رو پر کن یا از راه‌های ارتباطی دیگه با ما در تماس باش.
        </p>
    </div>
</section>

<section class="categories">
    <div class="container contact-container">

        <div class="contact-grid">

            <div class="t-card contact-form-card">

                <h2 class="contact-title">فرم تماس</h2>

                @if(session('status'))
                <p class="contact-success">{{ session('status') }}</p>
                @endif

                @if(session('error'))
                <p class="contact-error">{{ session('error') }}</p>
                @endif

                <form method="POST"
                    action="{{ route('contact.send') }}"
                    class="contact-form">

                    @csrf

                    <div>

                        <label class="contact-label">
                            نام و نام خانوادگی
                        </label>

                        <input
                            type="text"
                            value="{{ auth()->check() ? trim(auth()->user()->first_name . ' ' . auth()->user()->last_name) : old('name') }}" @auth readonly @endauth
                            required
                            class="contact-input {{ auth()->check() ? 'readonly-input' : '' }}">

                        @error('name')
                        <small class="field-error">{{ $message }}</small>
                        @enderror

                    </div>

                    <div>

                        <label class="contact-label">
                            ایمیل
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="contact-input"
                            placeholder="example@gmail.com">


                        @error('email')
                        <small class="field-error">{{ $message }}</small>
                        @enderror

                    </div>

                    <div>

                        <label class="contact-label">
                            موضوع
                        </label>

                        <input
                            type="text"
                            name="subject"
                            value="{{ old('subject') }}"
                            class="contact-input">

                    </div>

                    <div>

                        <label class="contact-label">
                            پیام
                        </label>

                        <textarea
                            name="message"
                            rows="5"
                            required
                            class="contact-textarea">{{ old('message') }}</textarea>

                        @error('message')
                        <small class="field-error">{{ $message }}</small>
                        @enderror

                    </div>

                    @guest

                    <div class="login-required">

                        <p class="t-text">
                            برای ارسال فرم تماس باید ابتدا وارد حساب کاربری‌ت بشی.
                        </p>

                        <a href="{{ route('login') }}"
                            class="btn btn-primary btn-sm">
                            ورود به حساب کاربری
                        </a>

                    </div>

                    @else

                    <button
                        type="submit"
                        class="btn btn-primary contact-submit">
                        ارسال پیام
                    </button>

                    @endguest

                </form>

            </div>

            <div class="contact-info">

                <div class="t-card">
                    <div class="t-name info-title">ایمیل</div>
                    <p class="t-text">shayan.hadad2004@gmail.com</p>
                </div>

                <div class="t-card">
                    <div class="t-name info-title">تلفن</div>
                    <p class="t-text">۰۲۱-۱۲۳۴۵۶۷۸</p>
                </div>

                <div class="t-card">
                    <div class="t-name info-title">آدرس</div>
                    <p class="t-text">دزفول، خیابان جندی شاپور، برج تولباکس</p>
                </div>

            </div>

        </div>

    </div>
</section>

@endsection