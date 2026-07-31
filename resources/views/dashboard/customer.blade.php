@extends('layouts.app')

@section('title', 'پنل کاربری | جعبه‌ابزار')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard/customer.css') }}">

<div class="cd-wrap">

    {{-- Header --}}
    <div class="cd-header">
        <div class="cd-header-left">
            <img src="{{ $user->profilePictureUrl() }}" alt="{{ $user->first_name }}" class="cd-avatar">
            <div>
                <p class="cd-eyebrow">{{ $user->username }}</p>
                <p class="cd-name">{{ $user->first_name }} {{ $user->last_name }}</p>
            </div>
        </div>
        <a href="#profile-form" class="cd-btn"> ویرایش اطلاعات پروفایل </a>
    </div>

    {{-- Stats --}}
    <div class="cd-stats">
        <div class="cd-stat">
            <p class="cd-stat-label">سفارش‌های ناتمام</p>
            <p class="cd-stat-value" style="color:#3E8E7E">{{ $stats['active'] }}</p>
        </div>
        <div class="cd-stat">
            <p class="cd-stat-label">سفارش‌های به اتمام رسیده</p>
            <p class="cd-stat-value" style="color:#C9A24B">{{ $stats['completed'] }}</p>
        </div>
        <div class="cd-stat">
            <p class="cd-stat-label">پیام‌های خوانده نشده</p>
            <p class="cd-stat-value" style="color:#C4573B">{{ $stats['unread'] }}</p>
        </div>
    </div>

    <div class="cd-grid">

        {{-- Recent orders --}}
        <div class="cd-card">
            <div class="cd-card-head">
                <p class="cd-card-title">سفارش‌های اخیر</p>
                <a href="{{ route('orders.index') }}" class="cd-link">مشاهده همه</a>
            </div>

            @php
            $stageKeys = [\App\Models\Order::STATUS_WAITING, \App\Models\Order::STATUS_IN_PROGRESS, \App\Models\Order::STATUS_FINISHED];
            @endphp

            @forelse ($orders as $order)
            @php
            $currentIndex = array_search($order->status, $stageKeys);
            $partnerName = $order->provider
            ? trim(($order->provider->first_name ?? '') . ' ' . ($order->provider->last_name ?? ''))
            : ($order->company->name ?? 'ارائه‌دهنده');
            @endphp
            <div class="cd-order">
                <div class="cd-order-top">
                    <div>
                        <p class="cd-order-name">{{ $partnerName }}</p>
                        <p class="cd-order-meta">
                            سفارش #{{ $order->orderID }}
                            @if ($order->order_date)
                            · {{ \Carbon\Carbon::parse($order->order_date)->format('Y/m/d') }}
                            @endif
                        </p>
                    </div>
                    <span class="cd-badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
                </div>
                @if (!in_array($order->status, [\App\Models\Order::STATUS_REJECTED, \App\Models\Order::STATUS_CANCELLED]))
                <div class="cd-stepper">
                    @foreach ($stageKeys as $i => $key)
                    <div class="cd-node {{ $i < $currentIndex ? 'done' : ($i === $currentIndex ? 'current' : '') }}"></div>
                    @if (!$loop->last)
                    <div class="cd-track {{ $i < $currentIndex ? 'done' : '' }}"></div>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="cd-empty">
                <p>هنوز سفارشی ندارید</p>
            </div>
            @endforelse
        </div>

        {{-- Sidebar --}}
        <div class="cd-side">

            <div class="cd-card">
                <div class="cd-card-head">
                    <p class="cd-card-title">بوکمارک‌ها</p>
                    <a href="{{ route('bookmarks.index') }}" class="cd-link">مشاهده همه</a>
                </div>
                @forelse ($bookmarkedProviders as $provider)
                <div class="cd-row">
                    <img src="{{ $provider->profilePictureUrl() }}" class="cd-row-avatar" alt="{{ $provider->first_name }}">
                    <div style="min-width:0">
                        <p class="cd-row-name">{{ $provider->first_name }} {{ $provider->last_name }}</p>
                        <p class="cd-row-sub">{{ $provider->expertDetail->specialty ?? 'Provider' }}</p>
                    </div>
                </div>
                @empty
                <p style="font-size:14px;color:#6B7280;padding:8px 0;">هنوز بوکمارکی ندارید.</p>
                @endforelse
            </div>

            <div class="cd-card">
                <div class="cd-card-head">
                    <p class="cd-card-title">پیام‌های خوانده نشده</p>
                    <a href="{{ route('messages.index') }}" class="cd-link">مشاهده همه</a>
                </div>
                @forelse ($recentMessages as $message)
                <a href="{{ route('messages.show', $message->sender) }}" class="cd-msg" style="display:block;text-decoration:none;">
                    <div class="cd-msg-top">
                        <p class="cd-msg-name">
                            {{ $message->companyID ? ($message->company->name ?? 'شرکت') : ($message->sender->first_name ?? 'کاربر') }}
                            @if ($message->status == 0)
                            <span class="cd-msg-unread"></span>
                            @endif
                        </p>
                    </div>
                    <p class="cd-msg-text">{{ $message->message }}</p>
                </a>
                @empty
                <p style="font-size:14px;color:#6B7280;padding:8px 0;">هنوز پیامی ندارید.</p>
                @endforelse
            </div>

        </div>
    </div>

    {{-- Profile edit form --}}
    <div class="cd-card" id="profile-form">
        <div class="cd-card-head">
            <p class="cd-card-title">ویرایش اطلاعات پروفایل</p>
        </div>

        @if (session('success'))
        <div class="cd-alert cd-alert-success">{{ session('success') }}</div>
        @endif

        {{-- Profile picture: kept as its own form, outside the update form below, so the
             DELETE request always goes to its own route instead of being captured by the
             PATCH update form (nested <form> elements are invalid HTML and browsers will
             submit the outer form's action/method instead of the inner one). --}}
        <div class="cd-avatar-block">
            <img src="{{ $user->profilePictureUrl() }}" alt="{{ $user->first_name }}" class="cd-avatar cd-avatar-lg">
            <div class="cd-avatar-info">
                <p class="cd-avatar-name">{{ $user->first_name }} {{ $user->last_name }}</p>
                <p class="cd-avatar-hint">jpg، png یا webp، حداکثر ۲ مگابایت. برای تغییر عکس، فایل جدید را در فرم پایین انتخاب کنید.</p>
            </div>
            @if ($user->profile_picture)
            <form action="{{ route('customer.profile.picture.destroy') }}" method="POST" class="cd-avatar-delete-form"
                onsubmit="return confirm('مطمئنی می‌خوای عکس پروفایلت رو حذف کنی؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="cd-btn cd-btn-danger cd-btn-sm" style="border:none;cursor:pointer;">
                    حذف عکس
                </button>
            </form>
            @endif
        </div>

        <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="cd-form-grid">
                <div class="cd-field">
                    <label class="cd-label" for="username">نام کاربری</label>
                    <input type="text" id="username" name="username" class="cd-input"
                        value="{{ old('username', $user->username) }}">
                    @error('username')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="contact_number">شماره تماس</label>
                    <input type="text" id="contact_number" name="contact_number" class="cd-input"
                        value="{{ old('contact_number', $user->contact_number) }}">
                    @error('contact_number')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="first_name">نام</label>
                    <input type="text" id="first_name" name="first_name" class="cd-input"
                        value="{{ old('first_name', $user->first_name) }}">
                    @error('first_name')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="last_name">نام خانوادگی</label>
                    <input type="text" id="last_name" name="last_name" class="cd-input"
                        value="{{ old('last_name', $user->last_name) }}">
                    @error('last_name')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="password">رمز عبور جدید</label>
                    <input type="password" id="password" name="password" class="cd-input" placeholder="خالی بگذارید تا تغییر نکند">
                    @error('password')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="date_of_birth">تاریخ تولد</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="cd-input"
                        value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}">
                    @error('date_of_birth')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="password_confirmation">تکرار رمز عبور جدید</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="cd-input">
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="profile_picture">عکس پروفایل جدید</label>
                    <input type="file" id="profile_picture" name="profile_picture" class="cd-input"
                        accept="image/jpeg,image/png,image/webp">
                    @error('profile_picture')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="cd-btn" style="margin-top:20px;border:none;cursor:pointer;">ذخیره تغییرات</button>
        </form>

    </div>

    {{-- Danger zone: delete account --}}
    <div class="cd-card cd-card-danger" style="margin-top:24px;">
        <div class="cd-card-head">
            <p class="cd-card-title" style="color:var(--danger);">حذف حساب کاربری</p>
        </div>

        @if (session('error'))
        <div class="cd-alert cd-alert-danger">{{ session('error') }}</div>
        @endif

        <p class="cd-danger-text">
            با حذف حساب، اطلاعات پروفایلت برای همیشه پاک می‌شه و این کار قابل بازگشت نیست.
        </p>

        <details class="cd-danger-details" @if(session('deleteAccountOpen')) open @endif>
            <summary class="cd-danger-summary">حذف حساب کاربری</summary>

            <form action="{{ route('account.destroy') }}" method="POST" class="cd-danger-form"
                onsubmit="return confirm('مطمئنی می‌خوای حساب کاربری‌ات رو برای همیشه حذف کنی؟ این کار قابل بازگشت نیست.');">
                @csrf
                @method('DELETE')

                <div class="cd-field">
                    <label class="cd-label" for="delete_password">برای تأیید، رمز عبورت رو وارد کن</label>
                    <input type="password" id="delete_password" name="password" class="cd-input" required>
                    @error('password')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>
     
                <button type="submit" class="cd-btn cd-btn-danger" style="margin-top:12px;border:none;cursor:pointer;">
                    حذف همیشگی حساب
                </button>
            </form>
        </details>
    </div>

</div>

@endsection
