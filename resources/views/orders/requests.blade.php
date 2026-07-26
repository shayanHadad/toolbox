@extends('layouts.app')

@section('title', 'درخواست‌های سفارش | جعبه‌ابزار')

@section('content')

<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

<div class="ord-wrap">

    <div class="ord-header">
        <h1 class="ord-title">درخواست‌های سفارشِ در انتظار تأیید</h1>
        <a href="{{ route($user->dashboardRoute()) }}" class="ord-back">← بازگشت به پنل</a>
    </div>

    @if (session('success'))
        <div class="ord-alert ord-alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="ord-alert ord-alert-danger">{{ session('error') }}</div>
    @endif

    @forelse ($orders as $order)
        @php
            $customerName = trim(($order->customer->first_name ?? '') . ' ' . ($order->customer->last_name ?? ''));
        @endphp
        <div class="ord-card">
            <div class="ord-card-top">
                <div>
                    <p class="ord-order-id">سفارش #{{ $order->orderID }}</p>
                    <p class="ord-partner">مشتری: {{ $customerName ?: '—' }}</p>
                </div>
                <span class="cd-badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
            </div>

            <dl class="ord-meta-grid">
                <div>
                    <dt>تاریخ مدنظر مشتری</dt>
                    <dd>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('Y/m/d') : '—' }}</dd>
                </div>
                <div>
                    <dt>شماره تماس مشتری</dt>
                    <dd>{{ $order->customer->contact_number ?? '—' }}</dd>
                </div>
            </dl>

            @if ($order->details)
            <div class="ord-details">
                <p class="ord-details-label">توضیحات مشتری:</p>
                <p class="ord-details-text">{{ $order->details }}</p>
            </div>
            @endif

            <div class="ord-actions">
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
        </div>
    @empty
        <div class="ord-empty">
            <p>در حال حاضر درخواست جدیدی برای تأیید وجود نداره</p>
        </div>
    @endforelse

</div>

@endsection
