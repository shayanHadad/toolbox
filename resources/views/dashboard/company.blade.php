@extends('layouts.app')

@section('title', 'پنل شرکت | جعبه‌ابزار')

@section('content')

<section class="categories">
    <div class="container" style="display:flex; justify-content:center; padding-top:60px; padding-bottom:60px;">

        <div class="t-card" style="max-width:520px; width:100%; text-align:center; padding:48px 32px; display:flex; flex-direction:column; align-items:center; gap:16px;">

            <span style="font-size:42px;">🚧</span>

            <h1 style="font-size:20px; font-weight:800; color:var(--ink);">
                پنل کاربری شرکت‌ها در حال ساخت است
            </h1>

            <p style="font-size:14.5px; line-height:1.9; color:var(--text-light);">
                سلام {{ $user->first_name ?? 'همراه عزیز' }}؛ این بخش از سایت هنوز طراحی و پیاده‌سازی نشده و
                به‌زودی در دسترس قرار می‌گیره. ممنون از صبوری‌ات 🙏
            </p>

            <a href="{{ url('/') }}" class="btn btn-primary btn-sm">بازگشت به صفحه‌ی اصلی</a>

        </div>

    </div>
</section>

@endsection
