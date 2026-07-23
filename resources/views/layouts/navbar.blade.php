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
                <a href="{{ url('/componies') }}">
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

            <a href="{{ url('/login') }}" class="btn btn-outline btn-sm">
                ورود
            </a>

            <a href="{{ url('/register') }}" class="btn btn-primary btn-sm">
                ثبت‌نام رایگان
            </a>

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
            <a href="{{ url('/componies') }}">
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

            <a href="{{ url('/login') }}" class="btn btn-outline">
                ورود
            </a>

            <a href="{{ url('/register') }}" class="btn btn-primary">
                ثبت‌نام رایگان
            </a>

        </li>

    </ul>

</nav>