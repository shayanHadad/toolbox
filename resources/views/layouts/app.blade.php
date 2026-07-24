<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Toolbox')</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    @include('components.navbar')
    @auth


    <div class="floating-actions">
        <a href="{{ route(auth()->user()->dashboardRoute())}}" class="floating-btn" title="داشبورد">
            <img src="{{asset('images/dashboard-icon.png')}}" alt="داشبورد" class="floating-btn-icon">
            <span class="floating-btn-label">داشبورد</span>
        </a>

        <a href="{{ route('messages.index') }}" class="floating-btn" title="پیام‌ها">
            <img src="{{asset('images/message-icon.png')}}" alt="پیام‌ها" class="floating-btn-icon">
            <span class="floating-btn-label">پیام‌ها</span>
        </a>

        <a href="{{ route('bookmarks.index') }}" class="floating-btn" title="بوکمارک‌ها">
            <img src="{{asset('images/bookmark-icon.png')}}" alt="بوکمارک‌ها" class="floating-btn-icon">
            <span class="floating-btn-label">بوکمارک‌ها</span>
        </a>
    </div>
    @endauth

    <main class="page-bg">
        @yield('content')
    </main>
    @include('components.footer')

    <button type="button" class="back-to-top" id="back-to-top" aria-label="برو به بالای صفحه">↑</button>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>