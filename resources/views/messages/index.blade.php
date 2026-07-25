@extends('layouts.app')

@section('title', 'پیام‌های من | جعبه‌ابزار')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/experts.css') }}">
@endpush

@section('content')

<section class="categories experts-section">
    <div class="container">

        <h1 class="expert-section-title" style="margin-bottom:20px;">پیام‌های من</h1>

        <div class="t-card">

            @forelse ($conversations as $conversation)
                <a href="{{ route('messages.show', $conversation->partner) }}" class="conversation-row">

                    <span class="expert-avatar" style="background-image:url('{{ asset('images/default-pfp.png') }}')"></span>

                    <div style="min-width:0;">
                        <p class="conversation-name">
                            {{ trim($conversation->partner->first_name . ' ' . $conversation->partner->last_name) }}
                        </p>
                        <p class="conversation-preview">
                            {{ \Illuminate\Support\Str::limit($conversation->lastMessage->message ?? '', 60) }}
                        </p>
                    </div>

                    <div class="conversation-meta">
                        <span class="conversation-time">
                            {{ $conversation->lastMessage?->created_at?->format('Y-m-d H:i') }}
                        </span>
                        @if($conversation->unreadCount > 0)
                            <span class="conversation-unread">{{ $conversation->unreadCount }}</span>
                        @endif
                    </div>

                </a>
            @empty
                <p class="t-text">هنوز هیچ مکالمه‌ای شروع نکردی.</p>
            @endforelse

        </div>

    </div>
</section>

@endsection
