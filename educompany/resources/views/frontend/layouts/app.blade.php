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
    <meta name="subdomain" content="{{ session()->get('subdomain') }}">
    <title>@yield('title', settings('name'))</title>
    <meta name="description" content="{{ settings('description') }}">
    <meta property="og:site_name" content="{{ settings('name') }}" />
    <meta property="og:title" content="@yield('title', settings('name'))" />
    <meta property="og:description" content="@yield('description', settings('description'))" />
    <meta property="og:locale" content="{{ app()->getLocale() }}_{{ strtoupper(app()->getLocale()) }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:image:url" content="@yield('image', settings('logo'))" />
    <meta property="og:image:secure_url" content="@yield('image', settings('logo'))" />
    <meta property="og:image:alt" content="@yield('title', settings('logo'))" />
    <meta property="og:type" content="website" />
    <meta name="robots" content="index,follow">
    <meta name="googlebot" content="index,follow">
    <meta name="generator" content="Globalmart Group MMC -  Development">
    <meta name="author" content="Globalmart Group MMC -  Development">
    <link rel="canonical" href="{{ route('page.welcome') }}">
    <link rel="shortlink" href="{{ route('page.welcome') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/css/fontawesome-all.min.css') }}">

    {{-- Favicon --}}
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('front/assets/favicons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('front/assets/favicons/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('front/assets/favicons/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('front/assets/favicons/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('front/assets/favicons/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('front/assets/favicons/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('front/assets/favicons/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('front/assets/favicons/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('front/assets/favicons/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"
        href="{{ asset('front/assets/favicons/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('front/assets/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('front/assets/favicons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('front/assets/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('front/assets/favicons/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('front/assets/favicons/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">
    <base href="{{ env('APP_DOMAIN') }}" />
    {{-- Favicon --}}

    {{-- Analystics Scripts --}}
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('G_MENSURENT_ID') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', '{{ env('G_MENSURENT_ID') }}');
    </script>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m, e, t, r, i, k, a) {
            m[i] = m[i] || function() {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            for (var j = 0; j < document.scripts.length; j++) {
                if (document.scripts[j].src === r) {
                    return;
                }
            }
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(
                k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym({{ env('Y_CLIENT_ID') }}, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/{{ env('Y_CLIENT_ID') }}" style="position:absolute; left:-9999px;"
                alt="" /></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->
    {{-- Analystics Scripts --}}

    @stack('css')
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div class="container container-xxl">
        @include('frontend.layouts.parts.header')
        @yield('content')
    </div>
    @include('frontend.layouts.parts.footer')


    <div id="loader">
        <div class="icon">
            <div class="bar" style="background-color: #3498db; margin-left: -60px;"></div>
            <div class="bar" style="background-color: #e74c3c; margin-left: -20px;"></div>
            <div class="bar" style="background-color: #f1c40f; margin-left: 20px;"></div>
            <div class="bar" style="background-color: #2eB869; margin-left: 60px;"></div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="deleteModal custom-modal modal show" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-confirm">
            <div class="modal-content">
                <div class="modal-header flex-column">
                    <div class="icon-box">
                        <i class="fa fa-trash"></i>
                    </div>
                    <h4 class="modal-title w-100">@lang('additional.messages.suredelete')</h4>
                    <button type="button" class="close" data-dismiss="modal"
                        onclick="toggleModalnow('deleteModal','hide')" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <p>@lang('additional.messages.dontrestore')</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onclick="toggleModalnow('deleteModal','hide')">@lang('additional.buttons.back')</button>
                    @if (Illuminate\Support\Str::contains(url()->current(), 'createoreditexam'))
                        <button type="button" onclick="deletequestion()"
                            class="btn btn-danger">@lang('additional.buttons.remove')</button>
                    @else
                        <button type="button" onclick="deleteproduct()"
                            class="btn btn-danger">@lang('additional.buttons.remove')</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Delete Modal --}}
    <script type="text/javascript" src="{{ asset('front/assets/js/eyvaz/vendor/jquery-ui/external/jquery/jquery.js') }}">
    </script>
    <script type="text/javascript" src="{{ asset('front/assets/js/eyvaz/base.js') }}"></script>

    <script defer>
        $(function() {
            @if (session()->has('message'))
                toast("{{ session('message') }}", 'info');
            @endif

            @if (session()->has('error'))
                toast("{{ session('error') }}", 'error');
            @endif

            @if (session()->has('info'))
                toast("{{ session('info') }}", 'info');
            @endif

            @if (session()->has('warning'))
                toast("{{ session('warning') }}", 'warning');
            @endif
            @if (session()->has('success'))
                toast("{{ session('success') }}", 'success');
            @endif
        });
    </script>

    <script>
        function deleteproduct(id = null, type = null) {
            var deleting_question_id = document.getElementById('deleting_question_id');
            var deleting_question_type = document.getElementById('deleting_question_type');
            var current_section = document.getElementById('current_section');
            if (type != null)
                deleting_question_type.value = type

            if (id != null) {
                var deleting_question_id = document.getElementById('deleting_question_id');
                deleting_question_id.value = id;
                toggleModalnow('deleteModal', 'open');
            } else {
                sendAjaxRequestOLD(`{{ route('front.questionsorsection.remove') }}`, "post", {
                    element_id: deleting_question_id.value,
                    element_type: deleting_question_type.value,
                    language: '{{ app()->getLocale() }}'
                }, function(e,
                    t) {
                    if (e) toast(e, "error");
                    else {
                        let n = JSON.parse(t);
                        if (n.message != null)
                            toast(n.message, n.status);

                        deleting_question_id.value = null;
                        deleting_question_type.value = null;
                        toggleModalnow('deleteModal', 'hide');

                        window.location.reload();
                    }
                });
            }
        }

        function redirect_tourl(url) {
            window.location.href = url;
        }

        function toggleContent(event) {
            let minified = $(event.currentTarget).find('.minified');
            let full = $(event.currentTarget).find('.full');
            let products_section_element_content_button = $(event.currentTarget).find(
                ".products_section_element_content_button");

            if (minified.length && full.length && products_section_element_content_button.length) {
                if (event.type === 'mouseleave' || event.type === 'touchend') {
                    minified.removeClass('d-none');
                    full.addClass('d-none');
                    products_section_element_content_button.removeClass('d-none');
                } else {
                    minified.addClass('d-none');
                    full.removeClass('d-none');
                    products_section_element_content_button.addClass('d-none');
                }
            }
        }
        $(document).ready(function() {

            $('.products_section_element').on({
                'mouseenter': toggleContent,
                'mouseleave': toggleContent,
                'touchstart': toggleContent,
                'touchend': toggleContent
            });
        });
    </script>


    @stack('js')
</body>

</html>
