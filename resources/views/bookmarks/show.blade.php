@extends('layouts.app')

@section('title', $company->name . ' | جعبه‌ابزار')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/experts.css') }}">
@endpush

@section('content')

<section class="categories expert-profile">
    <div class="container">

        <a href="{{ route('companies.index') }}" class="expert-back-link">→ بازگشت به لیست شرکت‌ها</a>

        <div class="expert-profile-grid">

            <div class="t-card expert-profile-main">

                <div class="expert-profile-head">
                    <span class="expert-avatar expert-avatar-lg" style="background-image:url('{{ asset('images/company.png') }}')"></span>

                    <div>
                        <h1 class="expert-profile-name">{{ $company->name }}</h1>

                        <div class="company-badges">
                            @foreach($company->categories as $cat)
                                <span class="expert-badge">{{ $cat->category_name }}</span>
                            @endforeach
                        </div>

                        <div class="expert-rating">
                            @for($i = 0; $i < 5; $i++)
                                {{ $i < round($company->rating_avg ?? 0) ? '★' : '☆' }}
                            @endfor
                            <span class="expert-rating-num">
                                {{ $company->rating_avg ? number_format($company->rating_avg, 1) : 'بدون امتیاز' }}
                            </span>
                        </div>

                        @if($company->founding_date)
                            <p class="company-founding">
                                تأسیس: {{ \Illuminate\Support\Carbon::parse($company->founding_date)->format('Y/m/d') }}
                            </p>
                        @endif
                    </div>

                    @auth
                        @if(auth()->user()->role == 1)
                            <div class="expert-profile-actions">
                                <form action="{{ route('bookmarks.company.toggle', $company) }}" method="POST">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="btn btn-outline btn-sm {{ $isBookmarked ? 'btn-icon-active' : '' }}">
                                        <img src="{{ asset('images/bookmark-icon.png') }}" alt="" class="btn-inline-icon">
                                        {{ $isBookmarked ? 'حذف بوکمارک' : 'بوکمارک کردن' }}
                                    </button>
                                </form>

                                @if($contactUser)
                                    <a
                                        href="{{ route('messages.show', $contactUser) }}"
                                        class="btn btn-primary btn-sm">
                                        <img src="{{ asset('images/message-icon.png') }}" alt="" class="btn-inline-icon">
                                        ارسال پیام
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endauth
                </div>

                <h2 class="expert-section-title">درباره‌ی شرکت</h2>
                <p class="expert-desc-full">
                    {{ $company->descriptions ?: 'این شرکت هنوز توضیحی برای پروفایلش ثبت نکرده.' }}
                </p>

            </div>

            <div class="expert-profile-side">

                <div class="t-card">
                    <h2 class="expert-section-title">نظرات مشتری‌ها</h2>

                    @forelse($reviews as $order)
                        <div class="expert-review">
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
