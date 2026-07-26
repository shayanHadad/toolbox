@extends('layouts.app')

@section('title', 'بوکمارک‌های من | جعبه‌ابزار')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/experts.css') }}">
@endpush

@section('content')

<section class="categories experts-section">
    <div class="container">

        <h1 class="expert-section-title" style="margin-bottom:20px;">بوکمارک‌های من</h1>

        @if (session('success'))
            <div class="experts-flash experts-flash-success">{{ session('success') }}</div>
        @endif

        {{-- ================= متخصص‌های بوکمارک‌شده ================= --}}
        <h2 class="expert-section-title">متخصص‌ها</h2>

        <div class="expert-grid bookmarks-grid" style="margin-bottom:50px;">

            @forelse ($providers as $provider)
                <div class="expert-card">

                    <div class="expert-card-top">
                        <span class="expert-avatar" style="background-image:url('{{ asset('images/default-pfp.png') }}')"></span>

                        @if($provider->expertDetail?->category)
                            <span class="expert-badge">{{ $provider->expertDetail->category->category_name }}</span>
                        @endif
                    </div>

                    <h3 class="expert-name">{{ trim($provider->first_name . ' ' . $provider->last_name) }}</h3>

                    <p class="expert-desc">
                        {{ \Illuminate\Support\Str::limit($provider->expertDetail?->description ?: 'این متخصص هنوز توضیحی برای پروفایلش ثبت نکرده.', 110) }}
                    </p>

                    <div class="expert-card-footer">
                        <div class="expert-card-actions">
                            <form action="{{ route('bookmarks.toggle', $provider) }}" method="POST" class="btn-icon-form">
                                @csrf
                                <button type="submit" class="btn-icon btn-icon-active" title="حذف از بوکمارک‌ها">
                                    <img src="{{ asset('images/bookmark-icon.png') }}" alt="بوکمارک">
                                </button>
                            </form>
                            <a href="{{ route('experts.show', $provider) }}" class="btn btn-outline btn-sm">مشاهده پروفایل</a>
                        </div>
                    </div>

                </div>
            @empty
                <div class="experts-empty">
                    <p>هنوز هیچ متخصصی رو بوکمارک نکردی.</p>
                    <a href="{{ route('experts.index') }}" class="btn btn-outline btn-sm">رفتن به لیست متخصص‌ها</a>
                </div>
            @endforelse

        </div>

        {{-- ================= شرکت‌های بوکمارک‌شده ================= --}}
        <h2 class="expert-section-title">شرکت‌ها</h2>

        <div class="expert-grid bookmarks-grid">

            @forelse ($companies as $company)
                <div class="expert-card">

                    <div class="expert-card-top">
                        <span class="expert-avatar" style="background-image:url('{{ asset('images/company.png') }}')"></span>

                        <div class="company-badges">
                            @foreach($company->categories->take(2) as $cat)
                                <span class="expert-badge">{{ $cat->category_name }}</span>
                            @endforeach
                        </div>
                    </div>

                    <h3 class="expert-name">{{ $company->name }}</h3>

                    <p class="expert-desc">
                        {{ \Illuminate\Support\Str::limit($company->descriptions ?: 'این شرکت هنوز توضیحی برای پروفایلش ثبت نکرده.', 110) }}
                    </p>

                    <div class="expert-card-footer">
                        <div class="expert-card-actions">
                            <form action="{{ route('bookmarks.company.toggle', $company) }}" method="POST" class="btn-icon-form">
                                @csrf
                                <button type="submit" class="btn-icon btn-icon-active" title="حذف از بوکمارک‌ها">
                                    <img src="{{ asset('images/bookmark-icon.png') }}" alt="بوکمارک">
                                </button>
                            </form>
                            <a href="{{ route('companies.show', $company) }}" class="btn btn-outline btn-sm">مشاهده پروفایل</a>
                        </div>
                    </div>

                </div>
            @empty
                <div class="experts-empty">
                    <p>هنوز هیچ شرکتی رو بوکمارک نکردی.</p>
                    <a href="{{ route('companies.index') }}" class="btn btn-outline btn-sm">رفتن به لیست شرکت‌ها</a>
                </div>
            @endforelse

        </div>

    </div>
</section>

@endsection
