<nav class="navbar">
    <input type="checkbox" id="nav-toggle" class="nav-toggle">

    <div class="nav-container">

        <!-- Logo -->
        <div class="logo">
            <a href="{{ url('/') }}" title="بازگشت به صفحه اصلی">
                <img src="{{ asset('images/logo.png') }}"
                    alt="جعبه‌ابزار لوگو"
                    loading="lazy"
                    height="40">
                <span>جعبه‌ابزار</span>
            </a>
        </div>

        <!-- Desktop Navigation -->
        <ul class="nav-center">

            <li>
                <a href="{{ url('/') }}">
                    <i>🏠</i>
                    <span>خانه</span>
                </a>
            </li>

            <li>
                <a href="{{ url('/experts') }}">
                    <i>🛠️</i>
                    <span>متخصص‌ها</span>
                </a>
            </li>

            <li>
                <a href="{{ route('companies.index') }}">
                    <i>🏢</i>
                    <span>شرکت‌ها</span>
                </a>
            </li>

            <li>
                <a href="{{ url('/about') }}">
                    <i>❓</i>
                    <span>درباره ما</span>
                </a>
            </li>

            <li>
                <a href="{{ url('/contact') }}">
                    <i>📞</i>
                    <span>تماس با ما</span>
                </a>
            </li>

        </ul>

        <!-- Right Side -->
        <div class="nav-left">

            @guest
            <a href="{{ route('login') }}" class="btn btn-outline btn-sm">
                ورود
            </a>

            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                ثبت‌نام
            </a>
            @endguest

            @auth
            <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm">
                    خروج
                </button>
            </form>
            <a href="{{ route(auth()->user()->dashboardRoute())}}#profile-form" class="nav-profile-link" title="داشبورد کاربری">
                <img src="{{ asset(auth()->user()->role == 2 ? 'images/expert.png' : 'images/default-pfp.png') }}" alt="پروفایل کاربری" class="nav-profile-img">
                <span class="nav-profile-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
            </a>
            @endauth
            <label class="nav-burger" for="nav-toggle" aria-label="باز کردن منو">
                <span></span>
            </label>

        </div>

    </div>

    <!-- Mobile Navigation -->

    <ul class="nav-mobile-panel">
        <li>
            <a href="{{ url('/') }}">
                🏠 خانه
            </a>
        </li>

        <li>
            <a href="{{ url('/experts') }}">
                🛠️ متخصص‌ها
            </a>
        </li>

        <li>
            <a href="{{ route('companies.index') }}">
                🏢 شرکت‌ها
            </a>
        </li>

        <li>
            <a href="{{ url('/about') }}">
                🧰 درباره ما
            </a>
        </li>

        <li>
            <a href="{{ url('/contact') }}">
                📞 تماس با ما
            </a>
        </li>

        <li class="nav-mobile-actions">
            @guest
            <a href="{{ route('login') }}" class="btn btn-outline">
                ورود
            </a>

            <a href="{{ route('register') }}" class="btn btn-primary">
                ثبت‌نام
            </a>
            @endguest

            @auth
            <a href="{{ route(auth()->user()->dashboardRoute())}}#profile-form" class="btn btn-outline">
                <img src="{{ asset(auth()->user()->role == 2 ? 'images/expert.png' : 'images/default-pfp.png') }}" alt="پروفایل کاربری" class="nav-profile-img-mobile">
                داشبورد
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    خروج
                </button>
            </form>
            @endauth
        </li>
    </ul>

</nav>