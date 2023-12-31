@extends('frontend.layouts.app')
@section('title', $data->name[app()->getLocale() . '_name'])
@push('css')
    <script language='javascript' type='text/javascript'>
        function DisableBackButton() {
            window.history.forward()
        }
        DisableBackButton();
        window.onload = DisableBackButton;
        window.onpageshow = function(evt) {
            if (evt.persisted) DisableBackButton()
        }
        window.onunload = function() {
            void(0)
        }
    </script>
@endpush
@section('content')
    <section class="examsshow my-3">
        <a class="btn btn-primary goback" href="{{ url()->previous() }}"><i class="fa fa-arrow-left"></i>&nbsp;
            @lang('additional.buttons.back')</a>
        <div class="content row my-4">
            <div class="col-sm-12 col-md-8 col-lg-7">
                @include('frontend.light_parts.section_title', [
                    'title' => $data->name[app()->getLocale() . '_name'],
                    'url' => null,
                ])
                <div class="exam_info_list">
                    <div class="exam_info_list_item left">
                        <div class="bold_text">@lang('additional.pages.exams.company'):</div>
                        <div class="normal_text">
                            {{ !empty($data->user) ? $data->user->name : settings()->name[app()->getLocale() . '_name'] }}
                        </div>
                    </div>
                    <div class="exam_info_list_item right">
                        <div class="bold_text">@lang('additional.pages.exams.price'):</div>
                        <div class="normal_text price_area @if ($data->price == 0 || $data->endirim_price > 0) free_price @endif ">
                            @if ($data->price > 0)
                                @if ($data->endirim_price > 0)
                                    {{ $data->endirim_price }}
                                    <span class="deleted_price">{{ $data->price }} AZN</span>
                                @else
                                    {{ $data->price }}
                                @endif

                                AZN
                            @else
                                @lang('additional.buttons.free')
                            @endif
                        </div>

                    </div>
                    <div class="exam_info_list_item left">
                        <div class="bold_text">@lang('additional.pages.exams.category'):</div>
                        <div class="normal_text">
                            {{ $data->category->name[app()->getLocale() . '_name'] ?? settings()->name[app()->getLocale() . '_name'] }}
                        </div>
                    </div>
                    @if ($data->endirim_price > 0)
                        <div class="exam_info_list_item right">
                            <div class="bold_text price_area @if ($data->price == 0 || $data->endirim_price > 0) free_price @endif ">
                                @lang('additional.pages.exams.endirim_with_faiz', ['count' => count_endirim_faiz($data->price, $data->endirim_price)])
                            </div>
                        </div>
                    @endif
                </div>
                <div class="exam_description">{!! $data->content[app()->getLocale() . '_description'] !!}</div>

                <div class="my-2">
                    @if (auth('users')->check()==true && !empty(auth('users')->user()) && auth('users')->user()!=null)
                        <a href="{{ route('user.exams.redirect_exam', ['exam_id' => $data->id]) }}"
                            class="btn btn-block btn-imtahanver">@lang('additional.buttons.imtahanver')</a>
                    @else
                        @if(!auth('users')->check() || (auth('users')->check() && isset(auth('users')->user()->user_type) && auth('users')->user()->user_type==1))
                            <a href="{{ route('login', ['savethisurl' => url()->current()]) }}"
                                class="btn btn-block btn-loginandredirect">@lang('additional.headers.login') / @lang('additional.headers.register') </a>
                        @endif
                    @endif
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-5 right_column_image">
                <img src="{{ getImageUrl($data->image, 'exams') }}" alt="{{ $data->name[app()->getLocale() . '_name'] }}">
            </div>
        </div>
    </section>
@endsection
