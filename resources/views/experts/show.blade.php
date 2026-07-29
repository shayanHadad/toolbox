@extends('layouts.app')

@section('title', trim($expert->first_name . ' ' . $expert->last_name) . ' | جعبه‌ابزار')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/experts.css') }}">
@endpush

@section('content')

<section class="categories expert-profile">
    <div class="container">

        <a href="{{ route('experts.index') }}" class="expert-back-link">→ بازگشت به لیست متخصص‌ها</a>

        @if (session('success'))
        <div class="experts-flash experts-flash-success">{{ session('success') }}</div>
        @endif

        <div class="expert-profile-grid">

            <div class="t-card expert-profile-main">

                <div class="expert-profile-head">
                    <span class="expert-avatar expert-avatar-lg" style="background-image:url('{{ asset('images/expert.png') }}')"></span>

                    <div>
                        <h1 class="expert-profile-name">{{ trim($expert->first_name . ' ' . $expert->last_name) }}</h1>

                        @if($expert->expertDetail?->category)
                        <span class="expert-badge">{{ $expert->expertDetail->category->category_name }}</span>
                        @endif

                        <div class="expert-rating">
                            @for($i = 0; $i < 5; $i++)
                                {{ $i < round($expert->rating_avg ?? 0) ? '★' : '☆' }}
                                @endfor
                                <span class="expert-rating-num">
                                {{ $expert->rating_avg ? number_format($expert->rating_avg, 1) : 'بدون امتیاز' }}
                                </span>
                        </div>
                    </div>

                    @auth
                    @if(auth()->user()->role == 1)
                    <div class="expert-profile-actions">
                        <form action="{{ route('bookmarks.toggle', $expert) }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="btn btn-outline btn-sm {{ $isBookmarked ? 'btn-icon-active' : '' }}">
                                <img src="{{ asset('images/bookmark-icon.png') }}" alt="" class="btn-inline-icon">
                                {{ $isBookmarked ? 'حذف بوکمارک' : 'بوکمارک کردن' }}
                            </button>
                        </form>

                        <a
                            href="{{ route('messages.show', $expert) }}"
                            class="btn btn-primary btn-sm">
                            <img src="{{ asset('images/message-icon.png') }}" alt="" class="btn-inline-icon">
                            ارسال پیام
                        </a>
                    </div>
                    @endif
                    @endauth
                </div>

                @auth
                @if(auth()->user()->role == 1)
                <div class="order-box">
                    @if (session('success'))
                    <div class="experts-flash experts-flash-success">{{ session('success') }}</div>
                    @endif

                    <details class="order-details" @if($errors->has('details') || $errors->has('preferred_date')) open @endif>
                        <summary class="order-summary">📝 ثبت سفارش برای این متخصص</summary>

                        <form action="{{ route('orders.expert.store', $expert) }}" method="POST" class="order-form">
                            @csrf

                            <label class="order-label" for="order_details">توضیح بده چه خدماتی نیاز داری</label>
                            <textarea
                                id="order_details"
                                name="details"
                                class="order-input"
                                rows="4"
                                maxlength="2000"
                                placeholder="مثلاً: نوع خدمات، زمان مورد نظر، آدرس تقریبی و..."
                                required>{{ old('details') }}</textarea>
                            @error('details')
                            <p class="order-error">{{ $message }}</p>
                            @enderror

                            <label class="order-label" for="order_preferred_date">تاریخ مدنظرت برای انجام کار</label>
                            <input
                                type="date"
                                id="order_preferred_date"
                                name="preferred_date"
                                class="order-input"
                                min="{{ now()->format('Y-m-d') }}"
                                value="{{ old('preferred_date') }}"
                                required>
                            @error('preferred_date')
                            <p class="order-error">{{ $message }}</p>
                            @enderror

                            <button type="submit" class="btn btn-primary btn-sm" style="align-self:flex-start;">ثبت سفارش</button>
                        </form>
                    </details>
                </div>
                @endif
                @endauth

                <h2 class="expert-section-title">درباره‌ی من</h2>
                <p class="expert-desc-full">
                    {{ $expert->expertDetail?->description ?: 'این متخصص هنوز توضیحی برای پروفایلش ثبت نکرده.' }}
                </p>

                @if($expert->expertDetail?->resume)
                <h2 class="expert-section-title">رزومه</h2>
                <p class="expert-desc-full">{{ $expert->expertDetail->resume }}</p>
                @endif

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
                        <p class="t-text">هنوز نظری برای این متخصص ثبت نشده.</p>
                        @endforelse
                    </div>

                </div>

            </div>

        </div>
</section>

@endsection