@extends('frontend.layouts.app')
@section('title', $exam->name[app()->getLocale() . '_name'])
@push('js')
    <script defer>
        function changetab(id) {
            var navlinks = document.getElementsByClassName("nav-link");
            var tabpanes = document.getElementsByClassName('tab-pane');

            // Nav linklerinde döngü yapma
            Array.from(navlinks).forEach(element => {
                if (element.classList.contains("active"))
                    element.classList.remove("active");
            });

            // Tab panes döngüsü
            Array.from(tabpanes).forEach(element => {
                if (element.classList.contains("active"))
                    element.classList.remove("active");
                element.classList.remove("show");
            });

            // Seçilen nav link ve tab pane'ı aktif hale getirme
            var selectednavlink = document.getElementById(`nav-${id}-tab`);
            var selectedtabpane = document.getElementById(`nav-${id}`);
            selectednavlink.classList.add('active');
            selectedtabpane.classList.add('active');
            selectedtabpane.classList.add('show');
        }
    </script>
@endpush

@section('content')
    <div class="container">
        @foreach ($exam_results as $exam_result)
            @if(!empty($exam_result->user))
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

                <section class="result_page">
                    <div class="header">
                        <p>{{ $exam_result->user->name }} @lang('additional.buttons.result')</p>
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
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button onclick="changetab('home')" class="nav-link active" id="nav-home-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab"
                                    aria-controls="nav-home" aria-selected="true">@lang('additional.pages.exams.allquestions')</button>

                                @if (!empty($exam->sections) && count($exam->sections) > 0)
                                    @foreach ($exam->sections as $key => $value)
                                        <button onclick="changetab({{ $key }})" class="nav-link"
                                            id="nav-{{ $key }}-tab" data-bs-toggle="tab"
                                            data-bs-target="#nav-{{ $key }}" type="button" role="tab"
                                            aria-controls="nav-{{ $key }}"
                                            aria-selected="true">{{ $value->name }}</button>
                                    @endforeach
                                @endif
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                aria-labelledby="nav-home-tab">
                                @foreach ($questions as $key => $value)
                                    <button
                                        class="btn btn-sm btn-question {{ exam_result_answer_true_or_false($value->id, $exam_result->id) }}"
                                        type="button">{{ $key + 1 }}</button>
                                @endforeach

                                <div class="footer">
                                    <div>
                                        @lang('additional.pages.exams.true_false_counts', ['true' => $exam_result->correctAnswers(), 'false' => $exam_result->wrongAnswers()])
                                    </div>
                                    <div>
                                        @lang('additional.pages.exams.your_result', ['point' => $exam_result->point])
                                    </div>
                                </div>
                            </div>

                            @if (!empty($exam->sections) && count($exam->sections) > 0)
                                @foreach ($exam->sections as $key => $value)
                                    <div class="tab-pane fade" id="nav-{{ $key }}" role="tabpanel"
                                        aria-labelledby="nav-{{ $key }}-tab">
                                        @foreach ($value->questions as $key1 => $val)
                                            <button
                                                class="btn btn-sm btn-question {{ exam_result_answer_true_or_false($val->id, $exam_result->id) }}"
                                                type="button">{{ $key1 + 1 }}</button>
                                        @endforeach

                                        <div class="footer">
                                            <div>
                                                @lang('additional.pages.exams.true_false_counts', ['true' => $value->correctAnswers($exam_result->id), 'false' => $value->wrongAnswers($exam_result->id)])
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                        </div>

                    </div>


                    @if ($exam->show_result_user == true)
                        <div class="row classcenter mt-3">
                            <a class="tonextbutton"
                                href="{{ route('user.exam.result', $exam_result->id) }}">@lang('additional.buttons.result')</a>
                        </div>
                    @endif
                </section>
            @endif
        @endforeach
    </div>
@endsection
