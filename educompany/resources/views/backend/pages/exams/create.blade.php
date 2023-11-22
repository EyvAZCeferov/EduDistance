@extends('backend.layouts.main')

@push('js')
    <script>
        var elem = document.querySelector('.js-switch');
        var switchery = new Switchery(elem, {
            color: '#1AB394'
        });
    </script>
@endpush

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>İmtahanlar</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">İdarə Paneli</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('exams.index') }}">İmtahanlar</a>
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
                                <a href="{{ route('exams.index') }}" class="btn btn-w-m btn-default">Geri</a>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form
                            @if (isset($data) && !empty($data) && isset($data->id)) action="{{ route('exams.update', $data->id) }}" @else action="{{ route('exams.store') }}" @endif
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($data) && !empty($data) && isset($data->id))
                                @method('PUT')
                            @endif

                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                        <button
                                            class="nav-link {{ $localeCode }} @if ($localeCode == 'az') active @endif"
                                            id="nav-{{ $localeCode }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-{{ $localeCode }}" type="button" role="tab"
                                            aria-controls="nav-{{ $localeCode }}" aria-selected="true"
                                            onclick="changeTabElements('{{ $localeCode }}')">{{ $localeCode }}</button>
                                        &nbsp;
                                    @endforeach
                                </div>
                            </nav>

                            <div class="tab-content" id="nav-tabContent">
                                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                    <div class="tab-pane {{ $localeCode }} fade @if ($localeCode == 'az') show active @endif pt-3"
                                        id="nav-{{ $localeCode }}" role="tabpanel"
                                        aria-labelledby="nav-{{ $localeCode }}-tab">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Ad <span
                                                            class="badge badge-info">{{ strtoupper($localeCode) }}</span></label>
                                                    <input type="text"
                                                        value="{{ old('' . $localeCode . '_name', isset($data) && !empty($data) && isset($data->name['' . $localeCode . '_name']) && !empty($data->name['' . $localeCode . '_name']) ? $data->name['' . $localeCode . '_name'] : null) }}"
                                                        name="{{ $localeCode }}_name"
                                                        class="form-control {{ $errors->first('' . $localeCode . '_name') ? 'is-invalid' : '' }}">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="">Açıqlama <span
                                                            class="badge badge-info">{{ strtoupper($localeCode) }}</span></label>
                                                    <textarea name="{{ $localeCode }}_description"
                                                        class="form-control summernote {{ $errors->first('' . $localeCode . '_description') ? 'is-invalid' : '' }}"
                                                        rows="4">{!! old(
                                                            '' . $localeCode . '_description',
                                                            isset($data) &&
                                                            !empty($data) &&
                                                            isset($data->content['' . $localeCode . '_description']) &&
                                                            !empty($data->content['' . $localeCode . '_description'])
                                                                ? $data->content['' . $localeCode . '_description']
                                                                : null,
                                                        ) !!}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">İmtahan vaxtı</label>
                                        <input type="text"
                                            value="{{ old('duration', isset($data) && !empty($data) && isset($data->duration) && !empty($data->duration) ? $data->duration : null) }}"
                                            data-mask="99:99:99" name="duration"
                                            class="form-control {{ $errors->first('duration') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Şəkil</label>
                                        @if (isset($data) && !empty($data) && isset($data->image) && !empty($data->image))
                                            <img src="{{ getImageUrl($data->image, 'exams') }}" alt="image"
                                                width="150">
                                            <br />
                                        @endif
                                        <input type="file" name="image" class="form-conrol">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Bal</label>
                                        <input type="text"
                                            value="{{ old('point', isset($data) && !empty($data) && isset($data->point) && !empty($data->point) ? $data->point : null) }}"
                                            name="point"
                                            class="form-control {{ $errors->first('point') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Kateqoriya</label>
                                        <select name="category_id"
                                            class="form-control {{ $errors->first('category_id') ? 'is-invalid' : '' }}">
                                            <option hidden disabled selected>Seçim edin</option>
                                            @foreach (\App\Models\Category::whereNull('parent_id')->with('sub')->get() as $category)
                                                <optgroup label="{{ $category->name['az_name'] }}">
                                                    @foreach ($category->sub as $sub)
                                                        <option
                                                            {{ old('category_id', isset($data) && !empty($data) && isset($data->category_id) && !empty($data->category_id) ? $data->category_id : null) == $sub->id ? 'selected' : '' }}
                                                            value="{{ $sub->id }}">{{ $sub->name['az_name'] }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="w-100">Status</label>
                                        <input type="checkbox" checked value="1" name="status"
                                            {{ isset($data) && !empty($data) && $data->status ? 'checked' : '' }}
                                            class="js-switch {{ $errors->first('status') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="w-100">Sıra nömrəsi</label>
                                        <input type="number" checked value="1" name="order_number"
                                            {{ isset($data) && !empty($data) && $data->order_number ? $data->order_number : 1 }}
                                            class="form-control {{ $errors->first('order_number') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="w-100">Qiyməti</label>
                                        <input type="number"
                                            value="{{ isset($data) && !empty($data) && $data->price ? $data->price : 1 }}"
                                            name="price"
                                            class="form-control {{ $errors->first('price') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="w-100">Endirim Qiyməti</label>
                                        <input type="number"
                                            value="{{ isset($data) && !empty($data) && $data->endirim_price ? $data->endirim_price : 1 }}"
                                            name="endirim_price"
                                            class="form-control {{ $errors->first('endirim_price') ? 'is-invalid' : '' }}">
                                    </div>
                                </div>
                            </div>


                            <hr>

                            <button class="btn btn-primary">Saxla</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
