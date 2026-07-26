@extends('layouts.app')

@section('title', 'سفارش‌ها | جعبه‌ابزار')

@section('content')

<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

<div class="ord-wrap">

    <div class="ord-header">
        <h1 class="ord-title">
            @if ($user->role == 1)
            تاریخچه‌ی سفارش‌های من
            @else
            تاریخچه‌ی سفارش‌های دریافتی
            @endif
        </h1>
        <a href="{{ route($user->dashboardRoute()) }}" class="ord-back">← بازگشت به پنل</a>
    </div>

    @if (session('success'))
    <div class="ord-alert ord-alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="ord-alert ord-alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" class="ord-filter-bar">
        <div class="ord-filter-field">
            <label for="ord-status">دسته‌بندی وضعیت</label>
            <select name="status" id="ord-status" onchange="this.form.submit()">
                <option value="">همه‌ی سفارش‌ها</option>
                <option value="waiting" @selected($status==='waiting' )>در انتظار تأیید</option>
                <option value="in_progress" @selected($status==='in_progress' )>در حال انجام</option>
                <option value="finished" @selected($status==='finished' )>تمام شده</option>
                <option value="rejected" @selected($status==='rejected' )>رد شده</option>
                <option value="cancelled" @selected($status==='cancelled' )>لغو شده</option>
            </select>
        </div>

        <div class="ord-filter-field">
            <label for="ord-sort">مرتب‌سازی</label>
            <select name="sort" id="ord-sort" onchange="this.form.submit()">
                <option value="newest" @selected($sort==='newest' )>جدیدترین</option>
                <option value="oldest" @selected($sort==='oldest' )>قدیمی‌ترین</option>
                <option value="status" @selected($sort==='status' )>بر اساس وضعیت</option>
            </select>
        </div>

        @if($status || $sort !== 'newest')
        <a href="{{ route('orders.index') }}" class="ord-filter-clear">حذف فیلتر</a>
        @endif
    </form>

    @forelse ($orders as $order)
    @php
    if ($user->role == 1) {
    $partnerLabel = $order->provider ? 'متخصص' : 'شرکت';
    $partnerName = $order->provider
    ? trim(($order->provider->first_name ?? '') . ' ' . ($order->provider->last_name ?? ''))
    : ($order->company->name ?? '—');
    } else {
    $partnerLabel = 'مشتری';
    $partnerName = trim(($order->customer->first_name ?? '') . ' ' . ($order->customer->last_name ?? ''));
    }
    @endphp
    <div class="ord-card">
        <div class="ord-card-top">
            <div>
                <p class="ord-order-id">سفارش #{{ $order->orderID }}</p>
                <p class="ord-partner">{{ $partnerLabel }}: {{ $partnerName ?: '—' }}</p>
            </div>
            <span class="cd-badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
        </div>

        <dl class="ord-meta-grid">
            <div>
                <dt>تاریخ مدنظر برای انجام کار</dt>
                <dd>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y/m/d') : '—' }}</dd>
            </div>
            <div>
                <dt>تاریخ ثبت سفارش</dt>
                <dd>{{ $order->created_at?->format('Y/m/d H:i') ?? '—' }}</dd>
            </div>

            @if ($user->role == 1 && $order->provider)
            <div>
                <dt>شماره تماس متخصص</dt>
                <dd>{{ $order->provider->contact_number ?? '—' }}</dd>
            </div>
            @endif

            @if ($user->role != 1)
            <div>
                <dt>شماره تماس مشتری</dt>
                <dd>{{ $order->customer->contact_number ?? '—' }}</dd>
            </div>
            @endif
        </dl>

        @if ($order->details)
        <div class="ord-details">
            <p class="ord-details-label">توضیحات مشتری هنگام ثبت سفارش:</p>
            <p class="ord-details-text">{{ $order->details }}</p>
        </div>
        @endif

        @if ($order->status === 'finished' && $order->comment)
        <div class="ord-review">
            <p class="ord-details-label">
                نظرِ ثبت‌شده
                @if ($order->rating)
                · {{ str_repeat('★', (int) $order->rating) }}{{ str_repeat('☆', 5 - (int) $order->rating) }}
                @endif
            </p>
            <p class="ord-details-text">{{ $order->comment }}</p>
        </div>
        @endif

        @if ($order->status === 'rejected')
        <div class="ord-rejected-note">
            @if ($user->role == 1)
            این سفارش رد شده و انجام نمی‌شه.
            @else
            شما این سفارش رو رد کردید.
            @endif
        </div>
        @endif

        @if ($order->status === 'cancelled')
        <div class="ord-rejected-note">
            @if ($user->role == 1)
            این سفارش رو خودت لغو کردی.
            @else
            این سفارش توسط مشتری لغو شده.
            @endif
        </div>
        @endif

        @if ($user->role == 1 && $order->status === 'waiting')
        <div class="ord-actions">
            <form action="{{ route('orders.cancel', $order) }}" method="POST"
                onsubmit="return confirm('مطمئنی می‌خوای این سفارش رو لغو کنی؟');">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--danger); border-color:var(--danger);">لغو سفارش</button>
            </form>
        </div>
        @endif

        @if (in_array($user->role, [2, 3, 4]) && $order->status === 'waiting')
        <div class="ord-actions" style="justify-content: space-between;">
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <form action="{{ route('orders.approve', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">تأیید سفارش</button>
                </form>
                <form action="{{ route('orders.reject', $order) }}" method="POST"
                    onsubmit="return confirm('مطمئنی می‌خوای این سفارش رو رد کنی؟ به مشتری اطلاع داده می‌شه.');">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm" style="color:var(--danger); border-color:var(--danger);">رد کردن</button>
                </form>
            </div>

            <a href="{{ route('messages.show', $order->customer) }}" class="btn btn-outline btn-sm">
                شروع گفتگو با مشتری
            </a>
        </div>
        @endif

        @if ($user->role == 1 && $order->needsReview())
        <details class="ord-review-details" @if($errors->has('rating') || $errors->has('comment')) open @endif>
            <summary class="order-summary">⭐ ثبت نظر و امتیاز</summary>

            <form action="{{ route('orders.review', $order) }}" method="POST" class="order-form">
                @csrf

                <label class="order-label">امتیازت رو انتخاب کن</label>
                <div class="ord-rating-picker">
                    @for ($i = 5; $i >= 1; $i--)
                    <label class="ord-rating-star">
                        <input type="radio" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }} required>
                        {{ $i }} ★
                    </label>
                    @endfor
                </div>
                @error('rating')
                <p class="order-error">{{ $message }}</p>
                @enderror

                <label class="order-label" for="comment_{{ $order->orderID }}">نظرت رو بنویس</label>
                <textarea id="comment_{{ $order->orderID }}" name="comment" class="order-input" rows="3" maxlength="2000" required>{{ old('comment') }}</textarea>
                @error('comment')
                <p class="order-error">{{ $message }}</p>
                @enderror

                <button type="submit" class="btn btn-primary btn-sm" style="align-self:flex-start; margin-top:8px;">ثبت نظر</button>
            </form>
        </details>
        @endif
    </div>
    @empty
    <div class="ord-empty">
        <p>هنوز سفارشی ثبت نشده</p>
    </div>
    @endforelse

</div>

@endsection