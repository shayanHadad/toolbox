@extends('layouts.app')

@section('title', 'پنل ادمین | جعبه‌ابزار')

@section('content')

<link rel="stylesheet" href="{{ asset('css/dashboard/expert.css') }}">

<div class="cd-wrap">

    {{-- Header --}}
    <div class="cd-header">
        <div class="cd-header-left">
            <img src="{{ asset('images/expert.png') }}" alt="{{ $user->first_name }}" class="cd-avatar">
            <div>
                <p class="cd-eyebrow">{{ $user->username }} · ادمین کل</p>
                <p class="cd-name">{{ $user->first_name }} {{ $user->last_name }}</p>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="cd-alert cd-alert-success" style="margin-top:16px;">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="cd-stats">
        <div class="cd-stat">
            <p class="cd-stat-label">تعداد مشتری‌ها</p>
            <p class="cd-stat-value" style="color:var(--indigo)">{{ $stats['customers'] }}</p>
        </div>
        <div class="cd-stat">
            <p class="cd-stat-label">تعداد متخصص‌ها</p>
            <p class="cd-stat-value" style="color:var(--success)">{{ $stats['experts'] }}</p>
        </div>
        <div class="cd-stat">
            <p class="cd-stat-label">تعداد شرکت‌ها</p>
            <p class="cd-stat-value" style="color:var(--amber)">{{ $stats['companies'] }}</p>
        </div>
        <div class="cd-stat">
            <p class="cd-stat-label">سفارش‌های امروز</p>
            <p class="cd-stat-value" style="color:var(--danger)">{{ $stats['todayOrders'] }}</p>
        </div>
    </div>
    <div class="cd-card" id="add-company" style="margin-top:24px;">
        <div class="cd-card-head">
            <p class="cd-card-title">افزودن شرکت جدید</p>
        </div>

        {{-- Add new company + owner --}}
        <p class="cd-eyebrow" style="margin-top:24px;">افزودن شرکت جدید</p>

        <form action="{{ route('admin.companies.store') }}" method="POST" style="margin-top:12px;">
            @csrf

            <p class="cd-eyebrow" style="margin-top:0;">اطلاعات شرکت</p>

            <div class="cd-form-grid">

                <div class="cd-field">
                    <label class="cd-label" for="new_company_name">نام شرکت</label>
                    <input type="text" id="new_company_name" name="name" class="cd-input"
                        value="{{ old('name') }}">
                    @error('name')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="new_company_founding_date">تاریخ تأسیس</label>
                    <input type="date" id="new_company_founding_date" name="founding_date" class="cd-input"
                        value="{{ old('founding_date') }}">
                    @error('founding_date')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field cd-field-full">
                    <label class="cd-label" for="new_company_descriptions">توضیحات</label>
                    <textarea id="new_company_descriptions" name="descriptions" class="cd-input" rows="3">{{ old('descriptions') }}</textarea>
                    @error('descriptions')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <p class="cd-eyebrow" style="margin-top:20px;">اطلاعات مالک شرکت</p>

            <div class="cd-form-grid">

                <div class="cd-field">
                    <label class="cd-label" for="new_owner_username">نام کاربری</label>
                    <input type="text" id="new_owner_username" name="username" class="cd-input"
                        value="{{ old('username') }}">
                    @error('username')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="new_owner_contact_number">شماره موبایل</label>
                    <input type="text" id="new_owner_contact_number" name="contact_number" class="cd-input"
                        value="{{ old('contact_number') }}">
                    @error('contact_number')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="new_owner_first_name">نام</label>
                    <input type="text" id="new_owner_first_name" name="first_name" class="cd-input"
                        value="{{ old('first_name') }}">
                    @error('first_name')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="new_owner_last_name">نام خانوادگی</label>
                    <input type="text" id="new_owner_last_name" name="last_name" class="cd-input"
                        value="{{ old('last_name') }}">
                    @error('last_name')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="new_owner_date_of_birth">تاریخ تولد</label>
                    <input type="date" id="new_owner_date_of_birth" name="date_of_birth" class="cd-input"
                        value="{{ old('date_of_birth') }}">
                    @error('date_of_birth')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="cd-field">
                    <label class="cd-label" for="new_owner_password">رمز عبور</label>
                    <input type="password" id="new_owner_password" name="password" class="cd-input">
                    @error('password')
                    <p class="cd-error">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <button type="submit" class="cd-btn" style="margin-top:20px;border:none;cursor:pointer;">افزودن شرکت جدید</button>
        </form>
    </div>

    {{-- Companies management --}}
    <div class="cd-card" id="companies" style="margin-top:24px;">
        <div class="cd-card-head">
            <p class="cd-card-title">مدیریت شرکت‌ها</p>
        </div>

        <form method="GET" action="{{ route('dashboard.admin') }}#companies" class="cd-filter-bar">
            <div class="cd-filter-field">
                <label for="company_search">جستجو (نام شرکت یا مالک)</label>
                <input type="text" id="company_search" name="search" value="{{ $search }}"
                    placeholder="مثلاً: جعبه‌ابزار یا نام مالک...">
            </div>

            <div class="cd-filter-actions">
                <button type="submit" class="cd-btn" style="border:none;cursor:pointer;">جستجو</button>
                @if ($search !== '' || $ownerFilter !== null)
                <a href="{{ route('dashboard.admin') }}#companies" class="cd-filter-clear">حذف فیلتر</a>
                @endif
            </div>
        </form>

        @if ($search !== '' || $ownerFilter !== null)
        <p class="cd-filter-count">{{ $companies->count() }} شرکت پیدا شد</p>
        @endif

        @forelse ($companies as $company)
        @php $owner = $company->owner(); @endphp
        <details class="cd-order cd-admin-item">
            <summary class="cd-admin-summary">
                <div>
                    <p class="cd-order-name">{{ $company->name }}</p>
                    <p class="cd-order-meta">
                        مالک: {{ $owner ? $owner->first_name . ' ' . $owner->last_name . ' · ' . $owner->username : 'بدون مالک' }}
                    </p>
                </div>
            </summary>

            <form action="{{ route('admin.companies.update', $company) }}" method="POST" style="margin-top:16px;">
                @csrf
                @method('PUT')

                <p class="cd-eyebrow" style="margin-top:0;">اطلاعات شرکت</p>

                <div class="cd-form-grid">

                    <div class="cd-field">
                        <label class="cd-label" for="company_name_{{ $company->companyID }}">نام شرکت</label>
                        <input type="text" id="company_name_{{ $company->companyID }}" name="name" class="cd-input"
                            value="{{ old('name', $company->name) }}">
                        @error('name')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="company_founding_date_{{ $company->companyID }}">تاریخ تأسیس</label>
                        <input type="date" id="company_founding_date_{{ $company->companyID }}" name="founding_date" class="cd-input"
                            value="{{ old('founding_date', optional($company->founding_date)->format('Y-m-d')) }}">
                        @error('founding_date')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field cd-field-full">
                        <label class="cd-label" for="company_descriptions_{{ $company->companyID }}">توضیحات</label>
                        <textarea id="company_descriptions_{{ $company->companyID }}" name="descriptions" class="cd-input" rows="3">{{ old('descriptions', $company->descriptions) }}</textarea>
                        @error('descriptions')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <p class="cd-eyebrow" style="margin-top:20px;">اطلاعات مالک شرکت</p>

                <div class="cd-form-grid">

                    <div class="cd-field">
                        <label class="cd-label" for="owner_username_{{ $company->companyID }}">نام کاربری</label>
                        <input type="text" id="owner_username_{{ $company->companyID }}" name="username" class="cd-input"
                            value="{{ old('username', $owner?->username) }}">
                        @error('username')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="owner_contact_number_{{ $company->companyID }}">شماره موبایل</label>
                        <input type="text" id="owner_contact_number_{{ $company->companyID }}" name="contact_number" class="cd-input"
                            value="{{ old('contact_number', $owner?->contact_number) }}">
                        @error('contact_number')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="owner_first_name_{{ $company->companyID }}">نام</label>
                        <input type="text" id="owner_first_name_{{ $company->companyID }}" name="first_name" class="cd-input"
                            value="{{ old('first_name', $owner?->first_name) }}">
                        @error('first_name')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="owner_last_name_{{ $company->companyID }}">نام خانوادگی</label>
                        <input type="text" id="owner_last_name_{{ $company->companyID }}" name="last_name" class="cd-input"
                            value="{{ old('last_name', $owner?->last_name) }}">
                        @error('last_name')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="owner_date_of_birth_{{ $company->companyID }}">تاریخ تولد</label>
                        <input type="date" id="owner_date_of_birth_{{ $company->companyID }}" name="date_of_birth" class="cd-input"
                            value="{{ old('date_of_birth', optional($owner?->date_of_birth)->format('Y-m-d')) }}">
                        @error('date_of_birth')
                        <p class="cd-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="cd-field">
                        <label class="cd-label" for="owner_password_{{ $company->companyID }}">رمز عبور جدید (اختیاری)</label>
                        <input type="password" id="owner_password_{{ $company->companyID }}" name="password" class="cd-input"
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

            <form action="{{ route('admin.companies.destroy', $company) }}" method="POST"
                style="margin-top:12px;"
                onsubmit="return confirm('مطمئنی می‌خوای این شرکت رو به‌همراه مالک، ادمین‌ها، سفارش‌ها و پیام‌هاش برای همیشه حذف کنی؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="cd-btn cd-btn-danger" style="border:none;cursor:pointer;">حذف این شرکت</button>
            </form>
        </details>
        @empty
        <div class="cd-empty">
            <p>{{ $search !== '' || $ownerFilter !== null ? 'شرکتی با این فیلتر پیدا نشد' : 'هنوز هیچ شرکتی ثبت نشده' }}</p>
        </div>
        @endforelse
    </div>
</div>

@endsection