@extends('layouts.app')

@section('title', $company->name . ' | جعبه‌ابزار')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/companies.css') }}">
@endpush

@section('content')

<section class="categories company-profile">
    <div class="container">

        <a href="{{ route('companies.index') }}" class="company-back-link">→ بازگشت به لیست شرکت‌ها</a>

        @if (session('success'))
            <div class="companies-flash companies-flash-success">{{ session('success') }}</div>
        @endif

        <div class="company-profile-grid">

            <div class="t-card company-profile-main">

                <div class="company-profile-head">
                    <span class="company-avatar company-avatar-lg" style="background-image:url('{{ asset('images/company.png') }}')"></span>

                    <div>
                        <h1 class="company-profile-name">{{ $company->name }}</h1>

                        @if($company->categories->isNotEmpty())
                            <div class="company-badges">
                                @foreach($company->categories as $cat)
                                    <span class="company-badge">{{ $cat->category_name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="company-rating">
                            @for($i = 0; $i < 5; $i++)
                                {{ $i < round($company->rating_avg ?? 0) ? '★' : '☆' }}
                            @endfor
                            <span class="company-rating-num">
                                {{ $company->rating_avg ? number_format($company->rating_avg, 1) : 'بدون امتیاز' }}
                            </span>
                        </div>
                    </div>
                </div>

                <h2 class="company-section-title">درباره‌ی شرکت</h2>
                <p class="company-desc-full">
                    {{ $company->descriptions ?: 'این شرکت هنوز توضیحی برای پروفایلش ثبت نکرده.' }}
                </p>

                @if($company->founding_date)
                    <h2 class="company-section-title">تاریخ تأسیس</h2>
                    <p class="company-desc-full">{{ $company->founding_date->format('Y/m/d') }}</p>
                @endif

            </div>

            <div class="company-profile-side">

                <div class="t-card">
                    <h2 class="company-section-title">نظرات مشتری‌ها</h2>

                    @forelse($reviews as $order)
                        <div class="company-review">
                            <div class="t-stars">
                                @for($i = 0; $i < 5; $i++)
                                    {{ $i < $order->rating ? '★' : '☆' }}
                                @endfor
                            </div>
                            <p class="t-text">{{ $order->comment }}</p>
                            <span class="t-name">{{ $order->customer->first_name ?? 'کاربر' }} {{ $order->customer->last_name ?? '' }}</span>
                        </div>
                    @empty
                        <p class="t-text">هنوز نظری برای این شرکت ثبت نشده.</p>
                    @endforelse
                </div>

            </div>

        </div>

    </div>
</section>

@endsection
