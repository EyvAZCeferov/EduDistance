@extends('frontend.layouts.app')
@section('title', $exam_result->exam->name[app()->getLocale() . '_name'])
@section('content')
    @php
        $exam = $exam_result->exam;
        $questions = collect();
        $qesutions = $exam->sections->pluck('questions');
        foreach ($qesutions as $qesution) {
            foreach ($qesution as $key => $qest) {
                $questions[] = $qest;
            }
        }
    @endphp

    <div class="container">
        <section class="result_page">
            <div class="header">
                <p>@lang('additional.buttons.result')</p>
                <div class="header_bottom_row">
                    <div class="col"></div>
                    <div class="col true">
                        @lang('additional.pages.exams.true_answers') <div class="blockwithbg"></div>
                    </div>
                    <div class="col false">
                        @lang('additional.pages.exams.false_answers') <div class="blockwithbg"></div>
                    </div>
                    <div class="col">
                        @lang('additional.pages.exams.notanswered_answers') <div class="blockwithbg"></div>
                    </div>
                    <div class="col"></div>
                </div>
            </div>

            <div class="content">
                @foreach ($questions as $key => $value)
                    <button
                        class="btn btn-sm btn-question {{ exam_result_answer_true_or_false($value->id, $exam_result->id) }}"
                        type="button">{{ $key + 1 }}</button>
                @endforeach
            </div>
            <div class="footer">
                <div>
                    @lang('additional.pages.exams.true_false_counts', ['true' => 10, 'false' => 2])
                </div>
                <div>
                    @lang('additional.pages.exams.your_result', ['point' => $exam_result->point])
                </div>
            </div>

            @if ($exam->show_result_user == true)
                <div class="row classcenter mt-3">
                    <a class="tonextbutton" href="{{ route('user.exam.result', $exam_result->id) }}">@lang('additional.buttons.result')</a>
                </div>
            @endif
        </section>
    </div>

@endsection
