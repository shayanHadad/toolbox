<footer class="footer">

    <div class="container footer-top">

        <div class="footer-brand">
            <div style="display:flex;align-items:center;gap:8px;">
                <img src="{{ asset('images/logo.png') }}"
                    alt="جعبه‌ابزار لوگو"
                    loading="lazy"
                    height="40">
                <span class="footer-brand-name">جعبه‌ابزار</span>
            </div>
            <p>بستری آنلاین برای آسان‌سازی ارتباط با اشخاص و شرکت‌های ارائه‌دهنده‌ی خدمات روزمره و امور تخصصی خانگی و غیرخانگی. </p>
        </div>

        <div class="footer-col">
            <h4>جعبه‌ابزار</h4>
            <ul>
                <li><a href="{{ url('/about') }}">درباره ما</a></li>
                <li><a href="{{ url('/contact') }}">تماس با ما</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>متخصص‌ها</h4>
            <ul>
                <li><a href="{{ url('/experts') }}">صفحه‌ی متخصصین</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>شرکت‌ها</h4>
            <ul>
                <li><a href="{{ route('companies.index') }}">شرکت‌ها</a></li>
            </ul>
        </div>


    </div>

    <div class="container footer-bottom">
        <span>
            جعبه‌ابزار — طراحی شده توسط شایان حداد.
            <br>
            © {{ date('Y') }} تمامی حقوق محفوظ است.
        </span>
    </div>

</footer>