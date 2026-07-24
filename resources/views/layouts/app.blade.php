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
    <main>
        @yield('content')
    </main>
    @include('components.footer')

    <button type="button" class="back-to-top" id="back-to-top" aria-label="برو به بالای صفحه">↑</button>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
