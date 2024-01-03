@extends('frontend.layouts.app')
@section('title', trans('additional.headers.register'))
@section('content')
    <section class="register_or_login py-5">
        <div class="row">
            <div class="col-0 col-sm-0 col-md-6 col-lg-7">
                <img src="{{ asset('front/assets/img/bg_images/register_bg.png') }}" class="img-responsive"
                    alt="{{ trans('additional.headers.login') . ' / ' . trans('additional.headers.register') }}">
            </div>
            <div class="col-sm-12 col-md-6 col-lg-5 right_column">
                <h2 class="text-center mt-2 mb-5">@lang('additional.headers.register')</h2>
                <form action="{{ route('user.register') }}" method="post" class="w-100" enctype="multipart/form-data">
                    @csrf
                    <input name="subdomain" value="{{session()->get('subdomain')??null}}" type="hidden">
                    <div class="row">
                        <div class="user_or_freelancer_row">
                            <div class="user_or_freelancer_tab user_or_freelancer_tab_student active"
                                onclick="tabselect('student')">
                                @lang('additional.forms.user_type_1')
                            </div>
                            <div class="user_or_freelancer_tab user_or_freelancer_tab_company"
                                onclick="tabselect('company')">
                                @lang('additional.forms.user_type_2')
                            </div>
                        </div>
                    </div>

                    @csrf
                    <input type="hidden" name="user_type" id="user_type" value="1">

                    <div class="account-form-item mb-3">
                        <div class="account-form-input">
                            <input type="text" placeholder="@lang('additional.forms.name')" name="name" value="{{ old('name') }}"
                                class="form-control form-control-lg">
                        </div>
                    </div>

                    <div class="account-form-item mb-3">
                        <div class="account-form-input">
                            <input type="email" placeholder="@lang('additional.forms.email')" name="email" value="{{ old('email') }}"
                                class="form-control form-control-lg">
                        </div>
                    </div>

                    <div class="account-form-item mb-3">
                        <div class="account-form-input">
                            <input type="text" placeholder="@lang('additional.forms.phone')" id="phone" name="phone"
                                value="{{ old('phone') }}" class="form-control form-control-lg">
                        </div>
                    </div>

                    <div class="account-form-item mb-3 tab_company_element" style="display: none">
                        <label for="file-upload" class="account-form-input custom-file-upload ">
                            <input id="file-upload" onchange="changedFileLabel('file-upload')" name="picture"
                                type="file">
                            <span class="file-name"> @lang('additional.forms.picture')</span>
                        </label>
                    </div>

                    <div class="account-form-item mb-3">
                        <div class="account-form-input account-form-input-pass">
                            <input type="password" class="form-control form-control-lg" placeholder="@lang('additional.forms.password')"
                                name="password" value="{{ old('password') }}" id="password">
                            <span id="password_icon" class="input_icon" onclick="toggleInputFunction('password')"><i
                                    class="fa fa-eye-slash"></i></span>
                        </div>
                    </div>

                    <div class="account-form-item mb-3">
                        <div class="account-form-input account-form-input-pass">
                            <input type="password" class="form-control form-control-lg" placeholder="@lang('additional.forms.password_confirmation')"
                                name="password_confirmation" value="{{ old('password_confirmation') }}"
                                id="password_confirmation">
                            <span id="password_confirmation_icon" class="input_icon"
                                onclick="toggleInputFunction('password_confirmation')"><i
                                    class="fa fa-eye-slash"></i></span>
                        </div>
                    </div>

                    <div class="account-form-button mt-4 mb-2">
                        <button type="submit" class="btn btn-primary btn-block">@lang('additional.headers.register')</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
