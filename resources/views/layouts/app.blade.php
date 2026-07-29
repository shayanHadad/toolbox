<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'جعبه‌ابزار')</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @stack('styles')
</head>

<body>
    @include('components.navbar')
    @auth

    <div class="floating-actions">
        <a href="{{ route(auth()->user()->dashboardRoute())}}" class="floating-btn" title="داشبورد">
            <img src="{{asset('images/dashboard-icon.png')}}" alt="داشبورد" class="floating-btn-icon">
            <span class="floating-btn-label">داشبورد</span>
        </a>

        @if(auth()->user()->role != 0)
        <a href="{{ route('messages.index') }}" class="floating-btn" title="پیام‌ها">
            <img src="{{asset('images/message-icon.png')}}" alt="پیام‌ها" class="floating-btn-icon">
            <span class="floating-btn-label">پیام‌ها</span>
        </a>

        @if(auth()->user()->role == 1)
        <a href="{{ route('bookmarks.index') }}" class="floating-btn" title="بوکمارک‌ها">
            <img src="{{asset('images/bookmark-icon.png')}}" alt="بوکمارک‌ها" class="floating-btn-icon">
            <span class="floating-btn-label">بوکمارک‌ها</span>
        </a>
        <a href="{{ route('orders.index') }}" class="floating-btn" title="سفارش‌ها">
            <img src="{{asset('images/order-icon.jpg')}}" alt="سفارش‌ها" class="floating-btn-icon">
            <span class="floating-btn-label">سفارش‌ها</span>
        </a>
        @else
        <a href="{{ route('orders.requests') }}" class="floating-btn" title="سفارش‌ها">
            <img src="{{asset('images/order-icon.jpg')}}" alt="سفارش‌ها" class="floating-btn-icon">
            <span class="floating-btn-label">درخواست‌ها</span>
        </a>
        @endif
        @endif
    </div>
    @endauth

    <main class="page-bg">
        @yield('content')
    </main>
    @include('components.footer')

    <button type="button" class="back-to-top" id="back-to-top" aria-label="برو به بالای صفحه">↑</button>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>