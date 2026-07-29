@extends('layouts.app')

@section('title', 'پنل متخصص | جعبه‌ابزار')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard/expert.css') }}">

<div class="cd-wrap">

    @if (!$expertDetail || !$expertDetail->categoryID)
    <div class="cd-warning">
        ⚠ هنوز دسته‌بندی تخصص خود را انتخاب نکرده‌اید. لطفاً هرچه سریع‌تر از
        <a href="#profile-form">بخش ویرایش پروفایل</a> یک دسته‌بندی انتخاب کنید.
    </div>
    @endif

    {{-- Header --}}
    <div class="cd-header">
        <div class="cd-header-left">
            <img src="{{ $user->profilePictureUrl() }}" alt="{{ $user->first_name }}" class="cd-avatar">
            <div>
                <p class="cd-eyebrow">{{ $user->username }}</p>
                <p class="cd-name">{{ $user->first_name }} {{ $user->last_name }}</p>
            </div>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="#profile-form" class="cd-btn" style="background:var(--surface); color:var(--indigo); border:1px solid var(--border);"> ویرایش اطلاعات پروفایل </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="cd-stats">
        <div class="cd-stat">
            <p class="cd-stat-label">سفارش‌های ناتمام</p>
            <p class="cd-stat-value" style="color:var(--success)">{{ $stats['active'] }}</p>
        </div>
        <div class="cd-stat">
            <p class="cd-stat-label">سفارش‌های به اتمام رسیده</p>
            <p class="cd-stat-value" style="color:var(--amber)">{{ $stats['completed'] }}</p>
        </div>
        <a href="{{ route('orders.index', ['status' => \App\Models\Order::STATUS_WAITING]) }}" class="cd-stat" style="text-decoration:none;">
            <p class="cd-stat-label">درخواست‌های در انتظار</p>
            <p class="cd-stat-value" style="color:var(--indigo)">{{ $stats['requests'] }}</p>
        </a>
        <div class="cd-stat">
            <p class="cd-stat-label">پیام‌های خوانده نشده</p>
            <p class="cd-stat-value" style="color:var(--danger)">{{ $stats['unread'] }}</p>
        </div>
    </div>

    <div class="cd-grid">

        {{-- Recent orders (this user is the provider) --}}
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
            @endphp
            <div class="cd-order">
                <div class="cd-order-top">
                    <div>
                        <p class="cd-order-name">{{ $order->customer->first_name ?? 'مشتری' }} {{ $order->customer->last_name ?? '' }}</p>
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
                <p>هنوز سفارشی دریافت نکرده‌اید</p>
            </div>
            @endforelse
        </div>

        {{-- Sidebar --}}
        <div class="cd-side">
            <div class="cd-card">
                <div class="cd-card-head">
                    <p class="cd-card-title">پیام‌های خوانده نشده</p>
                    <a href="{{ route('messages.index') }}" class="cd-link">مشاهده همه</a>
                </div>
                @forelse ($recentMessages as $message)
                <a href="{{ route('messages.show', $message->sender) }}" class="cd-msg" style="display:block;text-decoration:none;">
                    <div class="cd-msg-top">
                        <p class="cd-msg-name">
                            {{ $message->sender->first_name ?? 'User' }}
                            @if ($message->status == 0)
                            <span class="cd-msg-unread"></span>
                            @endif
                        </p>
                    </div>
                    <p class="cd-msg-text">{{ $message->message }}</p>
                </a>
                @empty
                <p style="font-size:14px;color:var(--text-light);padding:8px 0;">هنوز مسیجی ندارید.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Profile edit form --}}
    <div class="cd-card" id="profile-form" style="margin-top:290px;">
        <div class="cd-card-head">
            <p class="cd-card-title">ویرایش اطلاعات پروفایل</p>
        </div>

        @if (session('success'))
        <div class="cd-alert cd-alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('expert.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="cd-form-grid">

                <div class="cd-field cd-field-full" style="display:flex; align-items:center; gap:16px;">
                    <img src="{{ $user->profilePictureUrl() }}" alt="{{ $user->first_name }}"
                        class="cd-avatar" style="width:64px;height:64px;">
                    <div>
                        <label class="cd-label" for="profile_picture">عکس پروفایل</label>
                        <input type="file" id="profile_picture" name="profile_picture" class="cd-input"
                            accept="image/jpeg,image/png,image/webp">
                        <p style="font-size:12px;color:var(--text-light);margin-top:4px;">
                            jpg، png یا webp، حداکثر ۲ مگابایت.
                        </p>
                        @error('profile_picture')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

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
                    <label class="cd-label" for="date_of_birth">تاریخ تولد</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="cd-input"
                        value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}">
                    @error('date_of_birth')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="category_id">دسته‌بندی تخصص</label>
                    <select id="category_id" name="category_id" class="cd-input">
                        <option value="">— انتخاب کنید —</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->categoryID }}"
                            @selected(old('category_id', $expertDetail->categoryID ?? null) == $category->categoryID)>
                            {{ $category->category_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field cd-field-full">
                    <label class="cd-label" for="description">توضیحات</label>
                    <textarea id="description" name="description" class="cd-input" rows="4">{{ old('description', $expertDetail->description ?? '') }}</textarea>
                    @error('description')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field cd-field-full">
                    <label class="cd-label" for="resume">رزومه</label>
                    <textarea id="resume" name="resume" class="cd-input" rows="6">{{ old('resume', $expertDetail->resume ?? '') }}</textarea>
                    @error('resume')
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
                    <label class="cd-label" for="password_confirmation">تکرار رمز عبور جدید</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="cd-input">
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
            با حذف حساب، اطلاعات پروفایل و پروفایل تخصصی‌ات برای همیشه پاک می‌شه و این کار قابل بازگشت نیست.
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