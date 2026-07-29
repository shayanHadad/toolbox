@extends('layouts.app')

@section('title', 'پنل شرکت | جعبه‌ابزار')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard/expert.css') }}">

@if (! $company)

<section class="categories">
    <div class="container" style="display:flex; justify-content:center; padding-top:60px; padding-bottom:60px;">
        <div class="t-card" style="max-width:520px; width:100%; text-align:center; padding:48px 32px; display:flex; flex-direction:column; align-items:center; gap:16px;">
            <span style="font-size:42px;">🏢</span>
            <h1 style="font-size:20px; font-weight:800; color:var(--ink);">
                حساب کاربری‌ات به هیچ شرکتی وصل نیست
            </h1>
            <p style="font-size:14.5px; line-height:1.9; color:var(--text-light);">
                سلام {{ $user->first_name ?? 'همراه عزیز' }}؛ به نظر می‌رسه اکانتت هنوز به‌عنوان نماینده‌ی هیچ شرکتی ثبت نشده.
                برای وصل‌شدن به یک شرکت با پشتیبانی تماس بگیر.
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary btn-sm">بازگشت به صفحه‌ی اصلی</a>
        </div>
    </div>
</section>

@else

<div class="cd-wrap">

    {{-- Header --}}
    <div class="cd-header">
        <div class="cd-header-left">
            <img src="{{ asset('images/expert.png') }}" alt="{{ $user->first_name }}" class="cd-avatar">
            <div>
                <p class="cd-eyebrow">{{ $user->username }} · {{ $user->role == 4 ? 'مالک' : 'ادمین' }} {{ $company->name }}</p>
                <p class="cd-name">{{ $user->first_name }} {{ $user->last_name }}</p>
            </div>
        </div>
        @if ($user->role == 4)
        <div class="cd-header-right" style="display:flex; gap:12px; margin-inline-start:auto;">
            <a href="#profile-form" class="cd-btn" style="text-decoration:none;">
                ویرایش اطلاعات شرکت
            </a>

            <a href="#admins" class="cd-btn" style="text-decoration:none;">
                لیست ادمین‌ها
            </a>
        </div>
        @endif
    </div>

    @if (session('success'))
    <div class="cd-alert cd-alert-success" style="margin-top:16px;">{{ session('success') }}</div>
    @endif

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
                <p>هنوز سفارشی برای این شرکت ثبت نشده</p>
            </div>
            @endforelse
        </div>

        {{-- Sidebar --}}
        <div class="cd-side">

            <div class="cd-card">
                <div class="cd-card-head">
                    <p class="cd-card-title">پیام‌های اخیر</p>
                    <a href="{{ route('messages.index') }}" class="cd-link">مشاهده همه</a>
                </div>

                @forelse ($recentMessages as $message)
                <a href="{{ route('messages.show', $message->sender) }}" class="cd-msg" style="display:block;text-decoration:none;">
                    <div class="cd-msg-top">
                        <p class="cd-msg-name">
                            {{ $message->sender->first_name ?? 'کاربر' }} {{ $message->sender->last_name ?? '' }}
                            @if ($message->status == 0)
                            <span class="cd-msg-unread"></span>
                            @endif
                        </p>
                    </div>
                    <p class="cd-msg-text">{{ \Illuminate\Support\Str::limit($message->message, 60) }}</p>
                </a>
                @empty
                <div class="cd-empty">
                    <p>پیامی موجود نیست</p>
                </div>
                @endforelse
            </div>

        </div>

    </div>

    @if ($user->role == 4)
    {{-- Company profile edit form: only the company owner (role=4) can edit these --}}
    <div class="cd-card" id="profile-form" style="margin-top:24px;">
        <div class="cd-card-head">
            <p class="cd-card-title">ویرایش اطلاعات شرکت</p>
        </div>

        <form action="{{ route('company.profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="cd-form-grid">

                <div class="cd-field">
                    <label class="cd-label" for="name">نام شرکت</label>
                    <input type="text" id="name" name="name" class="cd-input"
                        value="{{ old('name', $company->name) }}">
                    @error('name')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="founding_date">تاریخ تأسیس</label>
                    <input type="date" id="founding_date" name="founding_date" class="cd-input"
                        value="{{ old('founding_date', $company->founding_date?->format('Y-m-d')) }}">
                    @error('founding_date')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field cd-field-full">
                    <label class="cd-label" for="descriptions">توضیحات</label>
                    <textarea id="descriptions" name="descriptions" class="cd-input" rows="4">{{ old('descriptions', $company->descriptions) }}</textarea>
                    @error('descriptions')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field cd-field-full">
                    <label class="cd-label">دسته‌بندی‌های فعالیت</label>
                    <div class="cd-checkbox-group">
                        @php $selectedCategoryIds = old('categories', $company->categories->pluck('categoryID')->all()); @endphp
                        @foreach ($categories as $category)
                        <label class="cd-checkbox">
                            <input type="checkbox" name="categories[]" value="{{ $category->categoryID }}"
                                @checked(in_array($category->categoryID, $selectedCategoryIds))>
                            {{ $category->category_name }}
                        </label>
                        @endforeach
                    </div>
                    @error('categories')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <button type="submit" class="cd-btn" style="margin-top:20px;border:none;cursor:pointer;">ذخیره تغییرات</button>
        </form>
    </div>

    {{-- Company admins management: only the company owner (role=4) can add/see admins --}}
    <div class="cd-card" id="admins" style="margin-top:24px;">
        <div class="cd-card-head">
            <p class="cd-card-title">ادمین‌های شرکت</p>
        </div>

        @forelse ($companyAdmins as $admin)
        <details class="cd-order cd-admin-item">
            <summary class="cd-admin-summary">
                <div>
                    <p class="cd-order-name">{{ $admin->first_name }} {{ $admin->last_name }}</p>
                    <p class="cd-order-meta">{{ $admin->username }} · {{ $admin->contact_number }}</p>
                </div>
            </summary>

            <form action="{{ route('company.admins.update', $admin) }}" method="POST" style="margin-top:16px;">
                @csrf
                @method('PUT')

                <div class="cd-form-grid">

                    <div class="cd-field">
                        <label class="cd-label" for="admin_username_{{ $admin->userID }}">نام کاربری</label>
                        <input type="text" id="admin_username_{{ $admin->userID }}" name="username" class="cd-input"
                            value="{{ old('username', $admin->username) }}">
                        @error('username')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="admin_contact_number_{{ $admin->userID }}">شماره موبایل</label>
                        <input type="text" id="admin_contact_number_{{ $admin->userID }}" name="contact_number" class="cd-input"
                            value="{{ old('contact_number', $admin->contact_number) }}">
                        @error('contact_number')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="admin_first_name_{{ $admin->userID }}">نام</label>
                        <input type="text" id="admin_first_name_{{ $admin->userID }}" name="first_name" class="cd-input"
                            value="{{ old('first_name', $admin->first_name) }}">
                        @error('first_name')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="admin_last_name_{{ $admin->userID }}">نام خانوادگی</label>
                        <input type="text" id="admin_last_name_{{ $admin->userID }}" name="last_name" class="cd-input"
                            value="{{ old('last_name', $admin->last_name) }}">
                        @error('last_name')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="admin_date_of_birth_{{ $admin->userID }}">تاریخ تولد</label>
                        <input type="date" id="admin_date_of_birth_{{ $admin->userID }}" name="date_of_birth" class="cd-input"
                            value="{{ old('date_of_birth', optional($admin->date_of_birth)->format('Y-m-d')) }}">
                        @error('date_of_birth')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="admin_password_{{ $admin->userID }}">رمز عبور جدید (اختیاری)</label>
                        <input type="password" id="admin_password_{{ $admin->userID }}" name="password" class="cd-input"
                            placeholder="خالی بذارید یعنی بدون تغییر">
                        @error('password')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div style="display:flex;gap:12px;margin-top:16px;">
                    <button type="submit" class="cd-btn" style="border:none;cursor:pointer;">ذخیره تغییرات</button>
                </div>
            </form>

            <form action="{{ route('company.admins.destroy', $admin) }}" method="POST"
                style="margin-top:12px;"
                onsubmit="return confirm('مطمئنی می‌خوای این ادمین رو حذف کنی؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="cd-btn" style="border:none;cursor:pointer;background:#dc2626;">حذف این ادمین</button>
            </form>
        </details>
        @empty
        <div class="cd-empty">
            <p>هنوز ادمینی برای این شرکت ثبت نشده</p>
        </div>
        @endforelse

        <form action="{{ route('company.admins.store') }}" method="POST" style="margin-top:20px;">
            @csrf

            <div class="cd-form-grid">

                <div class="cd-field">
                    <label class="cd-label" for="admin_username">نام کاربری</label>
                    <input type="text" id="admin_username" name="username" class="cd-input"
                        value="{{ old('username') }}">
                    @error('username')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="admin_contact_number">شماره موبایل</label>
                    <input type="text" id="admin_contact_number" name="contact_number" class="cd-input"
                        value="{{ old('contact_number') }}">
                    @error('contact_number')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="admin_first_name">نام</label>
                    <input type="text" id="admin_first_name" name="first_name" class="cd-input"
                        value="{{ old('first_name') }}">
                    @error('first_name')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="admin_last_name">نام خانوادگی</label>
                    <input type="text" id="admin_last_name" name="last_name" class="cd-input"
                        value="{{ old('last_name') }}">
                    @error('last_name')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="admin_date_of_birth">تاریخ تولد</label>
                    <input type="date" id="admin_date_of_birth" name="date_of_birth" class="cd-input"
                        value="{{ old('date_of_birth') }}">
                    @error('date_of_birth')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="admin_password">رمز عبور</label>
                    <input type="password" id="admin_password" name="password" class="cd-input">
                    @error('password')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <button type="submit" class="cd-btn" style="margin-top:20px;border:none;cursor:pointer;">افزودن ادمین جدید</button>
        </form>
    </div>
    @endif

</div>

@endif

@endsection