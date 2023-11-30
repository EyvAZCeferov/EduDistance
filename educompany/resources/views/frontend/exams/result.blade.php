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
    <section class="exam_page">
        <input type="hidden" name="current_question" id="current_question" value="{{ $questions[0]->id }}">
        <input type="hidden" name="current_section" id="current_section" value="{{ $questions[0]->section_id }}">
        <input type="hidden" name="current_section_name" id="current_section_name"
            value="{{ $questions[0]->section->name }}">
        <input type="hidden" name="selected_section" id="selected_section"
            value="{{ session()->get('selected_section') }}">
        <input type="hidden" name="time_range_sections" id="time_range_sections" value="{{ $exam->time_range_sections }}">
        <input type="hidden" name="next_section" id="next_section"
            value="{{ !empty($exam->sections[session()->get('selected_section')]) ? true : false }}">
        <input type="hidden" name="all_questions" id="all_questions" value="{{ count($questions) }}">
        <input type="hidden" name="show_time" id="show_time" value="true">
        <input type="hidden" name="time_exam" id="time_exam" value="0">
        <input type="hidden" name="section_start_time" id="section_start_time" value="0">
        <input type="hidden" name="marked_questions[]" id="marked_questions"
            value="{{ $exam_result->marked->pluck('question_id') }}">
        <input type="hidden" name="answered_questions[]" id="answered_questions">
        <input type="hidden" name="notanswered_questions[]" id="notanswered_questions">
        <input type="hidden" name="user_id" id="user_id" value="{{ auth('users')->id() }}">
        <input type="hidden" name="exam_id" id="exam_id" value="{{ $exam->id }}">
        <input type="hidden" name="exam_result_id" id="exam_result_id" value="{{ $exam_result->id }}">
        <input type="hidden" name="language" id="language" value="{{ app()->getLocale() }}">
        @include('frontend.exams.exam_main_process.parts.header_result', [
            'exam_result' => $exam_result
        ])
        @include('frontend.exams.exam_main_process.parts.content', [
            'exam' => $exam,
            'questions' => $questions,
            'exam_result' => $exam_result,
            "hide_abc"=>"hide"
        ])
        @include('frontend.exams.exam_main_process.parts.footer', [
            'exam' => $exam,
            'questions' => $questions,
        ])

        {{-- Desmos Calculator --}}
        <div id="desmoscalculator" class="modal custom-modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" onclick="toggleModalnow('desmoscalculator', 'hide')"
                            data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        {{-- <iframe src="https://www.desmos.com/calculator/ob0gttdkxf?embed"
                        style="width:100%;height:100%;"frameborder=0></iframe> --}}
                        <iframe src="https://www.geogebra.org/graphing/bbrq7kwt?embed" allowfullscreen
                            style="width:100%;height:100%;" frameborder="0"></iframe>
                    </div>

                </div>
            </div>
            <br>
        </div>
        {{-- Desmos Calculator --}}

        {{-- References --}}
        <div id="references" class="modal custom-modal modal-lg show" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-secondary">
                        <h3 class="text-white">@lang('additional.pages.exams.referances')</h3>
                        <button type="button" class="close text-white" onclick="toggleModalnow('references', 'hide')"
                            data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="references_top_buttons">
                            <div></div>
                            <div>
                                <a href="javascript:void(0)"
                                    onclick="toggle_references_modal_content('open')">@lang('additional.pages.exams.references_open')</a>
                                <a href="javascript:void(0)"
                                    onclick="toggle_references_modal_content('hide')">@lang('additional.pages.exams.references_hide')</a>
                            </div>
                        </div>

                        @foreach ($exam->references as $key => $value)
                            <div class="reference" id="reference_{{ $key }}">
                                <div class="referance_title">
                                    <h4>{{ $value->reference->name[app()->getLocale() . '_name'] }}</h4>
                                    <a href="javascript:void(0)" id="toggler_button_reference_{{ $key }}"
                                        class="referance_toggle_button"
                                        onclick="toggle_references_modal_content_element({{ $key }})"><i
                                            class="fa fa-plus"></i></a>
                                </div>
                                <div class="referance_body hide" id="body_reference_{{ $key }}">
                                    @if (isset($value->reference->image) && !empty($value->reference->image))
                                        <div class="col-sm-12 col-md-6 col-lg-8 img_area">
                                            <img src="{{ getImageUrl($value->reference->image, 'exams') }}"
                                                class="img-fluid img-responsive" alt="{{ $value->reference->image }}">
                                        </div>
                                    @endif
                                    <div
                                        class="@if (isset($value->reference->image) && !empty($value->reference->image)) col-sm-12 col-md-6 col-lg-4 @else col-sm-12 col-md-12 col-lg-12 @endif">
                                        {!! $value->reference->description[app()->getLocale() . '_description'] !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
            <br>
        </div>
        {{-- References --}}
    </section>
@endsection


@push('js')
    {{-- Footer Buttons --}}
    <script defer>
        function toback() {
            var current_question = document.getElementById("current_question").value;
            var first_question = document.getElementsByClassName('content_exam')[0];
            var currentDivQuestion = document.getElementById(`content_exam_${current_question}`);
            if (current_question == first_question.dataset.id) {
                window.location.href = '/exams';
            } else {
                currentDivQuestion.classList.remove("show");
                var new_key = parseInt(currentDivQuestion.dataset.key) - 1;
                var nextDivQuestion = document.querySelectorAll(`[data-key="${new_key}"]`);
                nextDivQuestion.forEach(function(element) {
                    element.classList.add("show");
                    document.getElementById("current_question").value = element.dataset.id;
                    document.getElementById("current_section_name").value = element.dataset.section_name;
                    document.getElementById("current_section").value = element.dataset.section_id;
                });
            }
        }

        function togglequestions() {
            var footer_questions = document.getElementById('footer_questions');
            if (footer_questions.classList.contains('active')) {
                footer_questions.classList.remove("active");
            } else {
                footer_questions.classList.add("active");
            }
        }

        function tonext() {
            var current_question = document.getElementById("current_question").value;
            var all_questions = document.getElementById("all_questions").value;
            var currentDivQuestion = document.getElementById(`content_exam_${current_question}`);
            var time_range_sections = document.getElementById("time_range_sections").value;
            var next_section = document.getElementById("next_section").value;
            var section_start_time = document.getElementById("section_start_time");
            var loader_for_sections = document.getElementById("loader_for_sections");
            var form = document.getElementById("exam");
            if (all_questions == currentDivQuestion.dataset.key) {
                window.location.href="{{ route('user.profile') }}"

                if (time_range_sections > 0) {
                    if (next_section == 1) {
                        section_start_time.value = document.getElementById("time_exam").value;
                        form.classList.remove('d-block');
                        form.style.display = "none";
                        loader_for_sections.classList.add("active");
                    }
                }
            } else {
                currentDivQuestion.classList.remove("show");
                var new_key = parseInt(currentDivQuestion.dataset.key) + 1;
                var nextDivQuestion = document.querySelectorAll(`[data-key="${new_key}"]`);
                nextDivQuestion.forEach(function(element) {
                    element.classList.add("show");
                    document.getElementById("current_question").value = element.dataset.id;
                    document.getElementById("current_section_name").value = element.dataset.section_name;
                    document.getElementById("current_section").value = element.dataset.section_id;
                });
            }

        }

        function updatepad() {
            var current_question = document.getElementById("current_question").value;
            var all_questions = document.getElementById("all_questions").value;
            var footer_active_button = document.getElementById(`question_row_button_${current_question}`);
            var buttons = document.getElementsByClassName("btn-question");
            var currentDivQuestion = document.getElementById(`content_exam_${current_question}`);
            var next_button = document.getElementById("next_button");
            var time_range_sections = document.getElementById("time_range_sections").value;
            var next_section = document.getElementById("next_section").value;
            if (all_questions == currentDivQuestion.dataset.key) {
                next_button.classList.remove("btn-secondary");
                next_button.classList.add("active");

                if (time_range_sections > 0) {
                    if (next_section == 1) {
                        next_button.innerHTML = `@lang('additional.buttons.nextsection') <i class="fa fa-angle-right"></i>`;
                    } else {
                        next_button.innerHTML = `@lang('additional.buttons.finish') <i class="fa fa-check"></i>`;
                    }
                } else {
                    next_button.innerHTML = `@lang('additional.buttons.finish') <i class="fa fa-check"></i>`;
                }
            } else {
                next_button.classList.add("btn-secondary");
                next_button.classList.remove("active");
                next_button.innerHTML = `@lang('additional.buttons.next') <i class="fa fa-angle-right"></i>`;
            }

            var current_question_text = document.getElementById("current_question_text");
            current_question_text.innerText = currentDivQuestion.dataset.key;

            for (var i = 0; i < buttons.length; i++) {
                buttons[i].classList.remove("current");
                buttons[i].classList.remove("saved");
                if (buttons[i].id == `question_row_button_${current_question}`) {
                    buttons[i].classList.add("current");
                }
            }
            var marked_questions = document.getElementById('marked_questions').value;
            if (marked_questions.length > 0) {
                marked_questions = JSON.parse(marked_questions);
                for (var i = 0; i < marked_questions.length; i++) {
                    var markingbutton = document.getElementById(`question_row_button_${marked_questions[i]}`);
                    markingbutton.classList.add("saved");
                }
            }

            var section_name_area = document.getElementById("section_name");
            var current_section_name = document.getElementById("current_section_name").value;
            section_name_area.innerText = current_section_name;

        }

        function getquestion(id) {
            var activecontentquestions = document.getElementsByClassName("content_exam");
            for (var i = 0; i < activecontentquestions.length; i++) {
                activecontentquestions[i].classList.remove("show");
            }

            var selected = document.getElementById(`content_exam_${id}`);
            selected.classList.add("show");
            document.getElementById("current_question").value = id;

        }

        setInterval(updatepad, 500);
    </script>
    {{-- Footer Buttons --}}
    <script defer>
        function increase_decrease_font(type) {
            var elements = document.getElementsByClassName('content_exam_info');

            for (var i = 0; i < elements.length; i++) {
                var fontSize = parseInt(window.getComputedStyle(elements[i]).fontSize); // Mevcut font boyutunu al

                if (type === "increase") {
                    elements[i].style.fontSize = (fontSize + 1) + 'px'; // Font boyutunu artır
                } else if (type === "decrease") {
                    elements[i].style.fontSize = (fontSize - 1) + 'px'; // Font boyutunu azalt
                }
            }
        }
    </script>
@endpush
