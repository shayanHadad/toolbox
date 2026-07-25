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

        <div class="expert-grid">

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
                        <form action="{{ route('bookmarks.toggle', $provider) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-icon btn-icon-active" title="حذف از بوکمارک‌ها">
                                <img src="{{ asset('images/bookmark-icon.png') }}" alt="بوکمارک">
                            </button>
                        </form>
                        <a href="{{ route('experts.show', $provider) }}" class="btn btn-outline btn-sm">مشاهده پروفایل</a>
                    </div>

                </div>
            @empty
                <div class="experts-empty">
                    <p>هنوز هیچ متخصصی رو بوکمارک نکردی.</p>
                    <a href="{{ route('experts.index') }}" class="btn btn-outline btn-sm">رفتن به لیست متخصص‌ها</a>
                </div>
            @endforelse

        </div>

        @if($providers->hasPages())
            <div class="experts-pagination">
                {{ $providers->onEachSide(1)->links('components.pagination') }}
            </div>
        @endif

    </div>
</section>

@endsection
