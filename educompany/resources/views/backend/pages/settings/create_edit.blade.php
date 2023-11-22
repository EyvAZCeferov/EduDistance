@extends('backend.layouts.main')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Parametrlər</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('settings.index') }}">Parametrlər</a>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <div class="ibox-tools mb-3">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="fullscreen-link">
                                <i class="fa fa-expand"></i>
                            </a>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-6">
                                <h5>Parametrlər</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('settings.index') }}" class="btn btn-w-m btn-default">Geri</a>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form
                            @if (isset($data) && !empty($data) && isset($data->id)) action="{{ route('settings.update', $data->id) }}" @else action="{{ route('settings.store') }}" @endif
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($data) && !empty($data) && isset($data->id))
                                @method('PUT')
                            @endif

                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link AZ active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true" onclick="changeTabElements('AZ')">AZ</button>
                                    &nbsp;
                                    <button class="nav-link RU" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false"
                                        onclick="changeTabElements('RU')">RU</button>
                                    &nbsp;
                                    <button class="nav-link EN" id="nav-contact-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-contact" type="button" role="tab"
                                        aria-controls="nav-contact" aria-selected="false"
                                        onclick="changeTabElements('EN')">EN</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane AZ fade show active pt-3" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Ad </label>
                                                <input type="text"
                                                    value="{{ old('az_name', isset($data) && !empty($data) && isset($data->name['az_name']) && !empty($data->name['az_name']) ? $data->name['az_name'] : null) }}"
                                                    name="az_name"
                                                    class="form-control {{ $errors->first('az_name') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Address </label>
                                                <input type="text"
                                                    value="{{ old('az_address', isset($data) && !empty($data) && isset($data->address['az_address']) && !empty($data->address['az_address']) ? $data->address['az_address'] : null) }}"
                                                    name="az_address"
                                                    class="form-control {{ $errors->first('az_address') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Address 2</label>
                                                <input type="text"
                                                    value="{{ old('az_address_2', isset($data) && !empty($data) && isset($data->address_2['az_address_2']) && !empty($data->address_2['az_address_2']) ? $data->address_2['az_address_2'] : null) }}"
                                                    name="az_address_2"
                                                    class="form-control {{ $errors->first('az_address_2') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div> --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Açıqlama </label>
                                                <textarea name="az_description" class="form-control {{ $errors->first('az_description') ? 'is-invalid' : '' }}"
                                                    rows="4">{{ old('az_description', isset($data) && !empty($data) && isset($data->description['az_description']) && !empty($data->description['az_description']) ? $data->description['az_description'] : null) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane RU fade pt-3" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Имя </label>
                                                <input type="text"
                                                    value="{{ old('ru_name', isset($data) && !empty($data) && isset($data->name['ru_name']) && !empty($data->name['ru_name']) ? $data->name['ru_name'] : null) }}"
                                                    name="ru_name"
                                                    class="form-control {{ $errors->first('ru_name') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Адрес </label>
                                                <input type="text"
                                                    value="{{ old('ru_address', isset($data) && !empty($data) && isset($data->address['ru_address']) && !empty($data->address['ru_address']) ? $data->address['ru_address'] : null) }}"
                                                    name="ru_address"
                                                    class="form-control {{ $errors->first('ru_address') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Адрес 2</label>
                                                <input type="text"
                                                    value="{{ old('ru_address_2', isset($data) && !empty($data) && isset($data->address_2['ru_address_2']) && !empty($data->address_2['ru_address_2']) ? $data->address_2['ru_address_2'] : null) }}"
                                                    name="ru_address_2"
                                                    class="form-control {{ $errors->first('ru_address_2') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div> --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Раскрытие информации </label>
                                                <textarea name="ru_description" class="form-control {{ $errors->first('ru_description') ? 'is-invalid' : '' }}"
                                                    rows="4">{{ old('ru_description', isset($data) && !empty($data) && isset($data->description['ru_description']) && !empty($data->description['ru_description']) ? $data->description['ru_description'] : null) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane EN fade pt-3" id="nav-contact" role="tabpanel"
                                    aria-labelledby="nav-contact-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Name </label>
                                                <input type="text"
                                                    value="{{ old('en_name', isset($data) && !empty($data) && isset($data->name['en_name']) && !empty($data->name['en_name']) ? $data->name['en_name'] : null) }}"
                                                    name="en_name"
                                                    class="form-control {{ $errors->first('en_name') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">Address </label>
                                                <input type="text"
                                                    value="{{ old('en_address', isset($data) && !empty($data) && isset($data->address['en_address']) && !empty($data->address['en_address']) ? $data->address['en_address'] : null) }}"
                                                    name="en_address"
                                                    class="form-control {{ $errors->first('en_address') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Address 2</label>
                                                <input type="text"
                                                    value="{{ old('en_address_2', isset($data) && !empty($data) && isset($data->address_2['en_address_2']) && !empty($data->address_2['en_address_2']) ? $data->address_2['en_address_2'] : null) }}"
                                                    name="en_address_2"
                                                    class="form-control {{ $errors->first('en_address_2') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div> --}}
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Description </label>
                                                <textarea name="en_description" class="form-control {{ $errors->first('en_description') ? 'is-invalid' : '' }}"
                                                    rows="4">{{ old('en_description', isset($data) && !empty($data) && isset($data->description['en_description']) && !empty($data->description['en_description']) ? $data->description['en_description'] : null) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row my-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Facebook</label>
                                        <input type="url"
                                            value="{{ old('facebook', isset($data) && !empty($data) && isset($data->social_media['facebook']) && !empty($data->social_media['facebook']) ? $data->social_media['facebook'] : null) }}"
                                            name="facebook"
                                            class="form-control {{ $errors->first('facebook') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Twitter</label>
                                        <input type="url"
                                            value="{{ old('twitter', isset($data) && !empty($data) && isset($data->social_media['twitter']) && !empty($data->social_media['twitter']) ? $data->social_media['twitter'] : null) }}"
                                            name="twitter"
                                            class="form-control {{ $errors->first('twitter') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Linkedin</label>
                                        <input type="url"
                                            value="{{ old('linkedin', isset($data) && !empty($data) && isset($data->social_media['linkedin']) && !empty($data->social_media['linkedin']) ? $data->social_media['linkedin'] : null) }}"
                                            name="linkedin"
                                            class="form-control {{ $errors->first('linkedin') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Instagram</label>
                                        <input type="url"
                                            value="{{ old('instagram', isset($data) && !empty($data) && isset($data->social_media['instagram']) && !empty($data->social_media['instagram']) ? $data->social_media['instagram'] : null) }}"
                                            name="instagram"
                                            class="form-control {{ $errors->first('instagram') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Tiktok</label>
                                        <input type="url"
                                            value="{{ old('tiktok', isset($data) && !empty($data) && isset($data->social_media['tiktok']) && !empty($data->social_media['tiktok']) ? $data->social_media['tiktok'] : null) }}"
                                            name="tiktok"
                                            class="form-control {{ $errors->first('tiktok') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Telegram</label>
                                        <input type="url"
                                            value="{{ old('telegram', isset($data) && !empty($data) && isset($data->social_media['telegram']) && !empty($data->social_media['telegram']) ? $data->social_media['telegram'] : null) }}"
                                            name="telegram"
                                            class="form-control {{ $errors->first('telegram') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Telefon</label>
                                        <input type="text"
                                            value="{{ old('phone', isset($data) && !empty($data) && isset($data->social_media['phone']) && !empty($data->social_media['phone']) ? $data->social_media['phone'] : null) }}"
                                            name="phone"
                                            class="form-control {{ $errors->first('phone') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Email</label>
                                        <input type="email"
                                            value="{{ old('email', isset($data) && !empty($data) && isset($data->social_media['email']) && !empty($data->social_media['email']) ? $data->social_media['email'] : null) }}"
                                            name="email"
                                            class="form-control {{ $errors->first('email') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Google Maps</label>
                                        <input type="url"
                                            value="{{ old('maps_google', isset($data) && !empty($data) && isset($data->social_media['maps_google']) && !empty($data->social_media['maps_google']) ? $data->social_media['maps_google'] : null) }}"
                                            name="maps_google"
                                            class="form-control {{ $errors->first('maps_google') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Ana səhifədəki video</label>
                                        <input type="url"
                                            value="{{ old('homepage_video', isset($data) && !empty($data) && isset($data->social_media['homepage_video']) && !empty($data->social_media['homepage_video']) ? $data->social_media['homepage_video'] : null) }}"
                                            name="homepage_video"
                                            class="form-control {{ $errors->first('homepage_video') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row my-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Logo</label>
                                        @if (isset($data) && !empty($data) && isset($data->logo) && !empty($data->logo))
                                            <img src="{{ getImageUrl($data->logo, 'settings') }}" alt="Logo"
                                                width="150">
                                            <br />
                                        @endif
                                        <input type="file" name="logo" class="form-conrol">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Bəyaz Logo</label>
                                        @if (isset($data) && !empty($data) && isset($data->logo_white) && !empty($data->logo_white))
                                            <img src="{{ getImageUrl($data->logo_white, 'settings') }}" alt="Logo_white"
                                                width="150" class="bg-danger">
                                            <br />
                                        @endif
                                        <input type="file" name="logo_white" class="form-conrol">
                                    </div>
                                </div>
                            </div>


                            <button class="btn btn-primary btn-block">Saxla</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
