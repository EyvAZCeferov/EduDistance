@push('css')
    <link rel="stylesheet" href="{{ asset('front/assets/js/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('front/assets/js/slick/slick-theme.css') }}">
@endpush

@extends('frontend.layouts.app')
@section('content')
    @include('frontend.light_parts.sliders')
    @include('frontend.light_parts.categories')
    @include('frontend.light_parts.about')
@endsection
@push('js')
    <script defer type="text/javascript" src="{{ asset('front/assets/js/slick/slick.min.js') }}"></script>
    <script defer>
        $(function() {
            $(".slick_slider").each(function() {
                $(this).slick({
                    infinite: true,
                    slidesToShow: $(this).data('slick-show'),
                    slidesToScroll: $(this).data('slick-scroll'),
                    autoplay: true,
                    autoplaySpeed: 2000,
                    dots: false,
                    arrows: true,
                });
            });
        });

        function searchnow(event) {
            var parentDiv = event.target.parentNode.parentNode;
            var input = parentDiv.querySelector('input[type="text"]');
            if (input !== null) {
                var value = input.value;
                if (value.trim() !== '') {
                    window.location.href = `/exams?search=${value}`;
                } else {
                    toast('@lang('additional.messages.required_fill')', 'error');
                }
            }
        }
    </script>
@endpush
