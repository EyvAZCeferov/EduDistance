@extends('backend.layouts.main')

@push('js')
@endpush

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Kateqoriyalar</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('categories.index') }}">Kateqoriyalar</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Yeni</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

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
                                <h5>Yeni</h5>
                            </div>
                            <div class="col-6 text-right">
                                <a href="{{ route('categories.index') }}" class="btn btn-w-m btn-default">Geri</a>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form
                            @if (isset($data) && !empty($data) && isset($data->id)) action="{{ route('categories.update', $data->id) }}" @else action="{{ route('categories.store') }}" @endif
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
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Ad </label>
                                                <input type="text"
                                                    value="{{ old('az_name', isset($data) && !empty($data) && isset($data->name['az_name']) && !empty($data->name['az_name']) ? $data->name['az_name'] : null) }}"
                                                    name="az_name"
                                                    class="form-control {{ $errors->first('az_name') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Açıqlama </label>
                                                <textarea name="az_description"
                                                    class="form-control summernote {{ $errors->first('az_description') ? 'is-invalid' : '' }}" rows="4">{!! old(
                                                        'az_description',
                                                        isset($data) &&
                                                        !empty($data) &&
                                                        isset($data->description['az_description']) &&
                                                        !empty($data->description['az_description'])
                                                            ? $data->description['az_description']
                                                            : null,
                                                    ) !!}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane RU fade pt-3" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Имя </label>
                                                <input type="text"
                                                    value="{{ old('ru_name', isset($data) && !empty($data) && isset($data->name['ru_name']) && !empty($data->name['ru_name']) ? $data->name['ru_name'] : null) }}"
                                                    name="ru_name"
                                                    class="form-control {{ $errors->first('ru_name') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Раскрытие информации </label>
                                                <textarea name="ru_description"
                                                    class="form-control summernote {{ $errors->first('ru_description') ? 'is-invalid' : '' }}" rows="4">{!! old(
                                                        'ru_description',
                                                        isset($data) &&
                                                        !empty($data) &&
                                                        isset($data->description['ru_description']) &&
                                                        !empty($data->description['ru_description'])
                                                            ? $data->description['ru_description']
                                                            : null,
                                                    ) !!}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane EN fade pt-3" id="nav-contact" role="tabpanel"
                                    aria-labelledby="nav-contact-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Name </label>
                                                <input type="text"
                                                    value="{{ old('en_name', isset($data) && !empty($data) && isset($data->name['en_name']) && !empty($data->name['en_name']) ? $data->name['en_name'] : null) }}"
                                                    name="en_name"
                                                    class="form-control {{ $errors->first('en_name') ? 'is-invalid' : '' }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Description </label>
                                                <textarea name="en_description"
                                                    class="summernote form-control {{ $errors->first('en_description') ? 'is-invalid' : '' }}" rows="4">{!! old(
                                                        'en_description',
                                                        isset($data) &&
                                                        !empty($data) &&
                                                        isset($data->description['en_description']) &&
                                                        !empty($data->description['en_description'])
                                                            ? $data->description['en_description']
                                                            : null,
                                                    ) !!}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Şəkil</label>
                                        @if (!empty($data) && isset($data->image) && !empty($data->image))
                                            <img src="{{ getImageUrl($data->image, 'category') }}" alt="image"
                                                width="150">
                                            <br />
                                        @endif
                                        <input type="file" name="image" class="form-conrol">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Sıra</label>
                                        <input type="number"
                                            value="{{ old('order_number', isset($data) && !empty($data) && isset($data->order_number) && !empty($data->order_number) ? $data->order_number : null) }}"
                                            name="order_number"
                                            class="form-control {{ $errors->first('order_number') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Parent</label>
                                        <select name="parent_id"
                                            class="form-control {{ $errors->first('parent_id') ? 'is-invalid' : '' }}">
                                            <option value="" selected>Seçim edin</option>
                                            @foreach (\App\Models\Category::whereNull('parent_id')->get() as $parent)
                                                <option @if (!empty($data) && isset($data->parent_id) && !empty($data->parent_id) && $data->parent_id == $parent->id) selected @endif
                                                    value="{{ $parent->id }}">{{ $parent->name['az_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <button class="btn btn-primary">Saxla</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
