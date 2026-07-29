@extends('layouts.app')

@php
$showAsCompany = $company && (int) auth()->user()->role === 1;
@endphp

@section('title', 'گفتگو با ' . ($showAsCompany ? $company->name : trim($partner->first_name . ' ' . $partner->last_name)) . ' | جعبه‌ابزار')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/experts.css') }}">
@endpush

@section('content')

<section class="categories experts-section">
    <div class="container">

        <a href="{{ route('messages.index') }}" class="expert-back-link">→ رفتن به پیام‌ها</a>

        @if (session('success'))
        <div class="experts-flash experts-flash-success">{{ session('success') }}</div>
        @endif

        <div class="t-card chat-wrap">

            <div class="chat-header">
                <span class="expert-avatar" style="background-image:url('{{ asset($showAsCompany ? 'images/company.png' : ($partner->role == 2 ? 'images/expert.png' : 'images/default-pfp.png')) }}')"></span>

                <div>
                    <p class="chat-header-name">{{ $showAsCompany ? $company->name : trim($partner->first_name . ' ' . $partner->last_name) }}</p>
                    @if(!$showAsCompany && $partner->role == 2 && $partner->expertDetail)
                    <a href="{{ route('experts.show', $partner) }}" class="chat-header-link">مشاهده پروفایل متخصص</a>
                    @endif
                </div>
            </div>

            <div class="chat-messages">
                @forelse ($messages as $message)
                @php
                    // توی چتِ شرکتی، سمتِ «خودمون» یعنی «هر پیامی که هر
                    // عضوی از تیمِ شرکت (role=3 یا 4) فرستاده»، نه فقط
                    // پیام‌هایی که خودِ همین کاربرِ لاگین‌کرده فرستاده؛
                    // وگرنه یه ادمینِ تازه‌اضافه‌شده، پیام‌های قبلیِ بقیه‌ی
                    // همکارهاش رو هم مثل پیامِ مشتری می‌دید و نمی‌تونست
                    // پیام‌های واقعیِ مشتری رو از پیام‌های شرکت تشخیص بده.
                    $isMine = $company && (int) auth()->user()->role !== 1
                        ? in_array((int) $message->sender->role, [3, 4], true)
                        : $message->senderID == auth()->id();
                @endphp
                <div class="chat-bubble-row {{ $isMine ? 'chat-bubble-row-mine' : '' }}">
                    <div class="chat-bubble {{ $isMine ? 'chat-bubble-mine' : 'chat-bubble-theirs' }}">
                        <p class="chat-bubble-text">{{ $message->message }}</p>
                        <span class="chat-bubble-meta">
                            <span class="chat-bubble-time">{{ $message->created_at?->format('Y-m-d H:i') }}</span>
                            @if($isMine)
                            <span class="chat-tick {{ $message->status == 1 ? 'chat-tick-read' : '' }}" title="{{ $message->status == 1 ? 'دیده شده' : 'ارسال شده' }}">
                                {{ $message->status == 1 ? '✓✓' : '✓' }}
                            </span>
                            @endif
                        </span>
                    </div>
                </div>
                @empty
                <p class="t-text" style="text-align:center;">هنوز پیامی بین شما رد و بدل نشده.</p>
                @endforelse
            </div>

            @auth
            @if(in_array(auth()->user()->role, [1, 2, 3, 4]))
            <form action="{{ route('messages.store', $partner) }}" method="POST" class="chat-form">
                @csrf
                <textarea
                    name="message"
                    class="chat-textarea"
                    rows="3"
                    maxlength="2000"
                    placeholder="پیامت رو اینجا بنویس..."
                    required></textarea>

                @error('message')
                <p class="cd-error">{{ $message }}</p>
                @enderror

                <button type="submit" class="btn btn-primary btn-sm">ارسال پیام</button>
            </form>
            @endif
            @endauth

        </div>

    </div>
</section>

@endsection
