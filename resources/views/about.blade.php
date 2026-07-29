@extends('layouts.app')

@section('title', ' درباره ما | جعبه‌ابزار')

@section('content')

{{-- HERO --}}
<section class="hero" style="min-height:auto;padding:70px 0;">
    <div class="container hero-content" style="padding:20px 0;">
        <div class="hero-eyebrow">
            <span class="dot"></span>
            درباره ما
        </div>
        <h1>ما به کارفرماها و متخصص‌ها کمک می‌کنیم <em>سریع‌تر</em> به هم برسند</h1>
        <p>
            تولباکس یه پلتفرم برای پیدا کردن متخصص‌های حرفه‌ای در حوزه‌های مختلفه؛
            هدف ما ساده کردن مسیر همکاری بین کارفرما و متخصص‌ها یا شرکت‌های حرفه‌ای هست، از جستجو تا پرداخت.
        </p>
    </div>
</section>

{{-- STATS / VALUES --}}
<section class="categories">
    <div class="container">
        <div class="section-head">
            <div class="hero-eyebrow">
                <span class="dot"></span>
                چرا تولباکس
            </div>
            <h2>چیزی که ما رو متفاوت می‌کنه</h2>
            <p>یک تیم کوچیک، یه هدف بزرگ: ساختن بهترین تجربه‌ی همکاری آنلاین.</p>
        </div>

        <div class="t-grid">
            <div class="t-card">
                <div class="t-quote-mark">”</div>
                <p class="t-text">بیش از ۱۰ هزار پروژه با موفقیت روی پلتفرم ما تکمیل شده و کارفرماها و متخصص‌ها هر روز بهم متصل می‌شن.</p>
                <div class="t-person">
                    <div>
                        <div class="t-name">+۱۰,۰۰۰</div>
                        <div class="t-role">پروژه‌ی موفق</div>
                    </div>
                </div>
            </div>

            <div class="t-card">
                <div class="t-quote-mark">”</div>
                <p class="t-text">شبکه‌ای رو به رشد از متخصص‌ها و شرکت‌های ارائه خدمات داریم.</p>
                <div class="t-person">
                    <div>
                        <div class="t-name">+۲,۵۰۰</div>
                        <div class="t-role">متخصص فعال</div>
                    </div>
                </div>
            </div>

            <div class="t-card">
                <div class="t-quote-mark">”</div>
                <p class="t-text">پشتیبانی و بررسی پروژه‌ها به‌صورت مستمر، برای اطمینان از کیفیت کار و رضایت طرفین.</p>
                <div class="t-person">
                    <div>
                        <div class="t-name">۲۴/۷</div>
                        <div class="t-role">پشتیبانی</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CLOSING CTA --}}
<section class="cta-bar">
    <div class="container">
        <div class="cta-box">
            <h2>اگر سوالی داری میتونی با ما در ارتباط باشی.</h2>
            <div class="cta-links">
                @guest
                <a href="{{ route('register') }}" class="btn btn-primary">ثبت‌نام رایگان</a>
                @endguest
                <a href="{{ route('contact') }}" class="btn btn-outline">تماس با ما</a>
            </div>
        </div>
    </div>
</section>

@endsection