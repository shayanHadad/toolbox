@extends('layouts.app')

@section('title', 'متخصص‌ها | جعبه‌ابزار')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/experts.css') }}">
@endpush

@section('content')

{{-- 1. HERO + SEARCH BAR --}}
<section class="hero experts-hero">
    <div class="container hero-content">

        <span class="hero-eyebrow"><span class="dot"></span> متخصص‌ها</span>

        <h1>یه <em>متخصص مطمئن</em> برای هر کاری پیدا کن</h1>

        <p>
            توی جعبه‌ابزار می‌تونی بین متخصص‌های حوزه‌های مختلف بگردی، پروفایل و امتیازشون رو ببینی
            و مناسب‌ترین گزینه رو برای کارت انتخاب کنی.
        </p>

        <form method="GET" action="{{ route('experts.index') }}" class="hero-search">
            @if(request()->filled('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request()->filled('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="اسم متخصص، تخصص یا یه کلمه‌ی کلیدی رو بنویس...">

            <button type="submit">جستجو</button>
        </form>

    </div>
</section>

{{-- 2. FILTERS + RESULTS --}}
<section class="categories experts-section">
    <div class="container">

        <form method="GET" action="{{ route('experts.index') }}" class="experts-filter-bar">

            @if(request()->filled('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif

            <div class="filter-field">
                <label for="category">دسته‌بندی</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="">همه‌ی دسته‌ها</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->url }}" {{ request('category') === $cat->url ? 'selected' : '' }}>
                            {{ $cat->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-field">
                <label for="sort">مرتب‌سازی</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>جدیدترین</option>
                    <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>بالاترین امتیاز</option>
                    <option value="orders" {{ request('sort') === 'orders' ? 'selected' : '' }}>پرکارترین</option>
                </select>
            </div>

            <noscript><button type="submit" class="btn btn-outline btn-sm">اعمال فیلتر</button></noscript>

            @if(request()->anyFilled(['q', 'category', 'sort']))
                <a href="{{ route('experts.index') }}" class="filter-clear">✕ پاک کردن فیلترها</a>
            @endif

        </form>

        @if (session('success'))
            <div class="experts-flash experts-flash-success">{{ session('success') }}</div>
        @endif

        <p class="experts-count">
            {{ $experts->total() }} متخصص پیدا شد
            @if(request()->filled('q'))
                برای «{{ request('q') }}»
            @endif
        </p>

        <div class="expert-grid">

            @forelse($experts as $expert)
                <div class="expert-card">

                    <div class="expert-card-top">
                        <span class="expert-avatar" style="background-image:url('{{ asset('images/expert.png') }}')"></span>

                        @if($expert->expertDetail?->category)
                            <span class="expert-badge">{{ $expert->expertDetail->category->category_name }}</span>
                        @endif

                        @auth
                            @if(auth()->user()->role == 1)
                                <form action="{{ route('bookmarks.toggle', $expert) }}" method="POST" class="btn-icon-form">
                                    @csrf
                                    @php $isBookmarked = in_array($expert->userID, $bookmarkedIds); @endphp
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
                    <p>متأسفانه با این فیلترها متخصصی پیدا نشد.</p>
                    <a href="{{ route('experts.index') }}" class="btn btn-outline btn-sm">پاک کردن فیلترها</a>
                </div>
            @endforelse

        </div>

        @if($experts->hasPages())
            <div class="experts-pagination">
                {{ $experts->onEachSide(1)->links('components.pagination') }}
            </div>
        @endif

    </div>
</section>

@endsection
