@extends('layouts.app')

@section('title', $category->category_name . ' | جعبه‌ابزار')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/experts.css') }}">
@endpush

@section('content')

<section class="hero experts-hero">
    <div class="container hero-content">
        <span class="hero-eyebrow"><span class="dot"></span> دسته‌بندی خدمات</span>
        <h1>متخصص‌ها و شرکت‌های <em>{{ $category->category_name }}</em></h1>
        <p>تمام متخصص‌ها و شرکت‌های فعال توی این حوزه رو اینجا کنار هم می‌بینی.</p>
    </div>
</section>

<section class="categories experts-section">
    <div class="container">

        @if (session('success'))
            <div class="experts-flash experts-flash-success">{{ session('success') }}</div>
        @endif

        {{-- ================= متخصص‌ها ================= --}}
        <h2 class="expert-section-title">متخصص‌ها</h2>

        <div class="expert-grid" style="margin-bottom:50px;">

            @forelse ($experts as $expert)
                <div class="expert-card">

                    <div class="expert-card-top">
                        <span class="expert-avatar" style="background-image:url('{{ asset('images/default-pfp.png') }}')"></span>

                        @if($expert->expertDetail?->category)
                            <span class="expert-badge">{{ $expert->expertDetail->category->category_name }}</span>
                        @endif

                        @auth
                            @if(auth()->user()->role == 1)
                                <form action="{{ route('bookmarks.toggle', $expert) }}" method="POST" class="btn-icon-form">
                                    @csrf
                                    @php $isBookmarked = in_array($expert->userID, $bookmarkedExpertIds); @endphp
                                    <button
                                        type="submit"
                                        class="btn-icon {{ $isBookmarked ? 'btn-icon-active' : '' }}"
                                        title="{{ $isBookmarked ? 'حذف از بوکمارک‌ها' : 'بوکمارک کردن این متخصص' }}">
                                        <img src="{{ asset('images/bookmark-icon.png') }}" alt="بوکمارک">
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>

                    <h3 class="expert-name">{{ trim($expert->first_name . ' ' . $expert->last_name) }}</h3>

                    <div class="expert-rating">
                        @for($i = 0; $i < 5; $i++)
                            {{ $i < round($expert->rating_avg ?? 0) ? '★' : '☆' }}
                        @endfor
                        <span class="expert-rating-num">
                            {{ $expert->rating_avg ? number_format($expert->rating_avg, 1) : 'بدون امتیاز' }}
                        </span>
                    </div>

                    <p class="expert-desc">
                        {{ \Illuminate\Support\Str::limit($expert->expertDetail?->description ?: 'این متخصص هنوز توضیحی برای پروفایلش ثبت نکرده.', 110) }}
                    </p>

                    <div class="expert-card-footer">
                        <span class="expert-orders">{{ $expert->orders_count }} سفارش تمام‌شده</span>

                        <div class="expert-card-actions">
                            @auth
                                @if(auth()->user()->role == 1)
                                    <a href="{{ route('messages.show', $expert) }}" class="btn btn-outline btn-sm">
                                        ارسال پیام
                                    </a>
                                @endif
                            @endauth
                            <a href="{{ route('experts.show', $expert) }}" class="btn btn-outline btn-sm">مشاهده پروفایل</a>
                        </div>
                    </div>

                </div>
            @empty
                <div class="experts-empty">
                    <p>هنوز متخصصی توی این دسته ثبت نشده.</p>
                </div>
            @endforelse

        </div>

        {{-- ================= شرکت‌ها ================= --}}
        <h2 class="expert-section-title">شرکت‌ها</h2>

        <div class="expert-grid">

            @forelse ($companies as $company)
                @php $contactUser = $company->contactUser(); @endphp
                <div class="expert-card">

                    <div class="expert-card-top">
                        <span class="expert-avatar" style="background-image:url('{{ asset('images/company.png') }}')"></span>

                        <div class="company-badges">
                            @foreach($company->categories->take(2) as $cat)
                                <span class="expert-badge">{{ $cat->category_name }}</span>
                            @endforeach
                        </div>

                        @auth
                            @if(auth()->user()->role == 1)
                                <form action="{{ route('bookmarks.company.toggle', $company) }}" method="POST" class="btn-icon-form">
                                    @csrf
                                    @php $isCompanyBookmarked = in_array($company->companyID, $bookmarkedCompanyIds); @endphp
                                    <button
                                        type="submit"
                                        class="btn-icon {{ $isCompanyBookmarked ? 'btn-icon-active' : '' }}"
                                        title="{{ $isCompanyBookmarked ? 'حذف از بوکمارک‌ها' : 'بوکمارک کردن این شرکت' }}">
                                        <img src="{{ asset('images/bookmark-icon.png') }}" alt="بوکمارک">
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>

                    <h3 class="expert-name">{{ $company->name }}</h3>

                    <div class="expert-rating">
                        @for($i = 0; $i < 5; $i++)
                            {{ $i < round($company->rating_avg ?? 0) ? '★' : '☆' }}
                        @endfor
                        <span class="expert-rating-num">
                            {{ $company->rating_avg ? number_format($company->rating_avg, 1) : 'بدون امتیاز' }}
                        </span>
                    </div>

                    <p class="expert-desc">
                        {{ \Illuminate\Support\Str::limit($company->descriptions ?: 'این شرکت هنوز توضیحی برای پروفایلش ثبت نکرده.', 110) }}
                    </p>

                    <div class="expert-card-footer">
                        <span class="expert-orders">{{ $company->orders_count }} سفارش تمام‌شده</span>

                        <div class="expert-card-actions">
                            @auth
                                @if(auth()->user()->role == 1 && $contactUser)
                                    <a href="{{ route('messages.show', $contactUser) }}" class="btn btn-outline btn-sm">
                                        ارسال پیام
                                    </a>
                                @endif
                            @endauth
                            <a href="{{ route('companies.show', $company) }}" class="btn btn-outline btn-sm">مشاهده پروفایل</a>
                        </div>
                    </div>

                </div>
            @empty
                <div class="experts-empty">
                    <p>هنوز شرکتی توی این دسته ثبت نشده.</p>
                </div>
            @endforelse

        </div>

    </div>
</section>

@endsection
