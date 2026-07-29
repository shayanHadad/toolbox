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
                <a href="{{ route('home') }}#work-categories">
                    <i>📂</i>
                    <span>دسته‌بندی‌ها</span>
                </a>
            </li>

            <li>
                <a href="{{ url('/experts') }}">
                    <i>🛠️</i>
                    <span>متخصص‌ها</span>
                </a>
            </li>

            <li>
                <a href="{{ url('/companies') }}">
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
                @switch(auth()->user()->role)
                @case(2)
                {{-- Expert --}}
                <img src="{{ asset('images/expert.png') }}" alt="پروفایل کاربری" class="nav-profile-img">
                @break

                @case(3)
                {{-- Company admin --}}
                <img src="{{ asset('images/company.png') }}" alt="پروفایل کاربری" class="nav-profile-img">
                @break

                @case(4)
                {{-- Company owner --}}
                <img src="{{ asset('images/company.png') }}" alt="پروفایل کاربری" class="nav-profile-img">
                @break

                @default
                {{-- Admin --}}
                {{-- Customer --}}
                <img src="{{ asset('images/default-pfp.png') }}" alt="پروفایل کاربری" class="nav-profile-img">
                @endswitch
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
            <a href="{{ route('home') }}#work-categories">
                📂 دسته‌بندی‌ها
            </a>
        </li>

        <li>
            <a href="{{ url('/experts') }}">
                🛠️ متخصص‌ها
            </a>
        </li>

        <li>
            <a href="{{ url('/companies') }}">
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
                @switch(auth()->user()->role)
                @case(2)
                {{-- Expert --}}
                <img src="{{ asset('images/expert.png') }}" alt="پروفایل کاربری" class="nav-profile-img">
                @break

                @case(3)
                {{-- Company admin --}}
                <img src="{{ asset('images/company.png') }}" alt="پروفایل کاربری" class="nav-profile-img">
                @break

                @case(4)
                {{-- Company owner --}}
                <img src="{{ asset('images/company.png') }}" alt="پروفایل کاربری" class="nav-profile-img">
                @break

                @default
                {{-- Admin --}}
                {{-- Customer --}}
                <img src="{{ asset('images/default-pfp.png') }}" alt="پروفایل کاربری" class="nav-profile-img">
                @endswitch
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