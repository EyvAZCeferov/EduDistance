@extends('frontend.layouts.app')
@section('title', trans('additional.pages.exams.exams'))
@section('content')
    <section class="exams">
        @include('frontend.exams.search_filter', ['type' => 'exams'])
        <div class="row my-3">
            <div class="col-sm-12 col-md-2 col-lg-1">
                
            </div>
            <div class="col-sm-12 col-md-10 col-lg-11">
                <div class="d-block">

                    @include('frontend.light_parts.section_title', [
                        'title' =>
                            !empty($category) &&
                            !empty($category->name) &&
                            isset($category->name[app()->getLocale() . '_name']) &&
                            !empty($category->name[app()->getLocale() . '_name'])
                                ? $category->name[app()->getLocale() . '_name']
                                : '',
                        'url' => null,
                        'button' => true,
                    ])
                </div>
                <div class="d-block">
                    @if (!empty($exams) && count($exams)>0)
                        @include('frontend.light_parts.products.products_grid', [
                            'products' => $exams,
                        ])
                    @else
                        <p class="text-center text-danger">@lang('additional.messages.examnotfound')</p>
                        <section class="not_found_page">
                            <div class="col-sm-12 col-md-4 col-lg-3 mx-auto text-center">
                                <a class="btn btn-primary notfound_button" href="{{ route("page.welcome") }}">
                                    <i class="fa fa-home"></i> @lang('additional.headers.welcome')
                                </a>
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
