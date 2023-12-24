<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="index, follow">
    <meta name="apple-mobile-web-app-status-bar-style" content="#1365A0">
    <meta name="msapplication-navbutton-color" content="#1365A0">
    <meta name="theme-color" content="#1365A0">
    <title>@yield('title', \Illuminate\Support\Facades\Session::has('subdomain') ? settings(\Illuminate\Support\Facades\Session::get('subdomain'))->name : settings()->name[app()->getLocale() . '_name'])</title>
    <meta name="description" content="{{ settings()->description[app()->getLocale() . '_description'] }}">
    <meta property="og:site_name"
        content="{{ \Illuminate\Support\Facades\Session::has('subdomain') ? settings(\Illuminate\Support\Facades\Session::get('subdomain'))->name : settings()->name[app()->getLocale() . '_name'] }}" />
    <meta property="og:title" content="@yield('title', \Illuminate\Support\Facades\Session::has('subdomain') ? settings(\Illuminate\Support\Facades\Session::get('subdomain'))->name : settings()->name[app()->getLocale() . '_name'])" />
    <meta property="og:description" content="@yield('description', settings()->description[app()->getLocale() . '_description'])" />
    <meta property="og:locale" content="{{ app()->getLocale() }}_{{ strtoupper(app()->getLocale()) }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image:url" content="@yield('image', getImageUrl(settings()->logo, 'settings'))" />
    <meta property="og:image:secure_url" content="@yield('image', getImageUrl(settings()->logo, 'settings'))" />
    <meta property="og:image:alt" content="@yield('title', getImageUrl(settings()->logo, 'settings'))" />
    <meta property="og:type" content="website" />
    <meta name="robots" content="index,follow">
    <meta name="googlebot" content="index,follow">
    <meta name="generator" content="Globalmart Group MMC -  Development">
    <meta name="author" content="Globalmart Group MMC -  Development">
    <link rel="canonical" href="{{ route('page.welcome') }}">
    <link rel="shortlink" href="{{ route('page.welcome') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/css/fontawesome-all.min.css') }}">
    @stack('css')
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div class="container container-xxl">
        @include('frontend.layouts.parts.header')
        @yield('content')
    </div>
    @include('frontend.layouts.parts.footer')

    <script src="{{ asset('backend/assets/js/jquery-3.1.1.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('front/assets/js/eyvaz/base.js') }}"></script>

    <script defer>
        $(function() {
            @if (Session::has('message'))
                toast("{{ session('message') }}", 'info');
            @endif

            @if (Session::has('error'))
                toast("{{ session('error') }}", 'error');
            @endif

            @if (Session::has('info'))
                toast("{{ session('info') }}", 'info');
            @endif

            @if (Session::has('warning'))
                toast("{{ session('warning') }}", 'warning');
            @endif
            @if (Session::has('success'))
                toast("{{ session('success') }}", 'success');
            @endif
        });
    </script>

    <div id="loader">
        <div class="icon">
            <div class="bar" style="background-color: #3498db; margin-left: -60px;"></div>
            <div class="bar" style="background-color: #e74c3c; margin-left: -20px;"></div>
            <div class="bar" style="background-color: #f1c40f; margin-left: 20px;"></div>
            <div class="bar" style="background-color: #2eB869; margin-left: 60px;"></div>
        </div>
    </div>
    @stack('js')
</body>

</html>
