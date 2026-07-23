<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Toolbox')</title>
    
    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}">
</head>
<body>
    @include('layouts.navbar')
    <main>
        @yield('content')
    </main>
    @include('layouts.footer')

    <button type="button" class="back-to-top" id="back-to-top" aria-label="برو به بالای صفحه">↑</button>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        (function () {
            const btn = document.getElementById('back-to-top');
            if (!btn) return;

            const toggle = () => {
                btn.classList.toggle('is-visible', window.scrollY > 400);
            };

            window.addEventListener('scroll', toggle, { passive: true });
            toggle();

            btn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        })();
    </script>
</body>
</html>