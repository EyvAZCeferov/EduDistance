@extends('frontend.layouts.exam_layout')
@section('title', $exam->name[app()->getLocale() . '_name'])
@section('description', $exam->description[app()->getLocale() . '_description'] ?? '')
@push('css')
    <style type="text/css" media="print">
        * {
            display: none;
        }

        #print_error {
            display: block;
        }
    </style>
@endpush
@section('content')
    @php
        $questions = collect();
        if ($exam->time_range_sections > 0) {
            $qesutions = $exam->sections[session()->get('selected_section')]->questions;
            foreach ($qesutions as $qesution) {
                $questions[] = $qesution;
            }
        } else {
            $qesutions = $exam->sections->pluck('questions');
            foreach ($qesutions as $qesution) {
                foreach ($qesution as $key => $qest) {
                    $questions[] = $qest;
                }
            }
        }
    @endphp

    <form action="" id="exam" class="d-block" method="POST">
        @csrf
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
        <section class="exam_page">
            @include('frontend.exams.exam_main_process.parts.header', [
                'exam' => $exam,
                'questions' => $questions,
            ])
            @include('frontend.exams.exam_main_process.parts.content', [
                'exam' => $exam,
                'questions' => $questions,
                'exam_result' => $exam_result,
            ])
            @include('frontend.exams.exam_main_process.parts.footer', [
                'exam' => $exam,
                'questions' => $questions,
            ])

            {{-- Desmos Calculator --}}
            <div id="desmoscalculator" class="modal custom-modal show" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel">
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
                            <button type="button" class="close text-white"
                                onclick="toggleModalnow('references', 'hide')" data-dismiss="modal" aria-label="Close">
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
                                                    class="img-fluid img-responsive"
                                                    alt="{{ $value->reference->image }}">
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
    </form>

    <div id="print_error" class="text text-danger w-100 text-center">
        @lang('additional.messages.noprint')
    </div>

    <div id="loader_for_sections" class="loader_for_sections">
        <div class="timer_section">
            <div class="hour_area">
                <span id="minutes_start_time">
                </span>:<span id="seconds_start_time"></span>
            </div>
        </div>

        <div class="time_section_arasi">
            @lang('additional.pages.exams.section_arasi_vaxd_gozle', ['time' => $exam->time_range_sections])
        </div>

        <div class="time_wait_please">
            <img src="{{ asset('front/assets/img/bg_images/time_wait_please.png') }}" alt="Time_wait_please">
        </div>

    </div>

@endsection
@push('js')
    {{-- Header Buttons --}}
    <script defer>
        let intervalTimerID;
        function togglehours() {
            var clock_area = document.getElementById("timer_section");
            var clock_toggle_button = document.getElementById("timer_button");
            if (clock_area.classList.contains("hide")) {
                clock_area.classList.remove("hide");
                clock_toggle_button.text = '@lang('additional.buttons.hide')';
            } else {
                clock_area.classList.add("hide");

                clock_toggle_button.text = '@lang('additional.buttons.show')';
            }
        }

        function togglecalculator() {
            $('#desmoscalculator').toggle();
            $('#desmoscalculator').draggable();
        }

        function togglereferances() {
            $('#references').toggle();
            $('#references').draggable();
        }
    </script>
    {{-- Header Buttons --}}
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
                clearInterval(intervalTimerID);
                var forum = document.getElementById("exam");
                var formData = new FormData(forum);
                fetch("{{ route('finish_exam') }}", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Network response was not ok.");
                        }
                        return response.json();
                    })
                    .then(data => {
                        toast(data.message, data.status);
                        if(data.url!=null && data.url!='' && data.url!=' '){
                            window.location.href=data.url;
                        }
                    })
                    .catch(error => {
                        toast(error.message, "error");
                    });

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

    {{-- Create Timer --}}
    <script defer>
        let sec = 0;

        function pad(val) {
            return val.toString().padStart(2, '0');
        }

        function updateClock() {
            sec++;
            var inputtimeinput = document.getElementById("time_exam");
            var section_start_time = document.getElementById("section_start_time");
            var time_range_sections = document.getElementById("time_range_sections");
            var loader_for_sections = document.getElementById("loader_for_sections");
            var form = document.getElementById("exam");

            inputtimeinput.value = sec;
            if (loader_for_sections.classList.contains('active') && section_start_time.value > 0) {
                var qalan_vaxt = time_range_sections.value - section_start_time.value;
                section_start_time.value = parseInt(section_start_time.value) + 1;
                if (document.getElementById('seconds_start_time')) {
                    document.getElementById('seconds_start_time').innerHTML = pad(qalan_vaxt % 60);
                }

                if (document.getElementById('minutes_start_time')) {
                    document.getElementById('minutes_start_time').innerHTML = pad(parseInt(qalan_vaxt / 60, 10) % 60);
                }

                if (qalan_vaxt == 0) {
                    section_start_time.value = 0;
                    loader_for_sections.classList.remove('active');
                    form.classList.add('d-block');
                }
            } else {
                section_start_time.value = 0;
                loader_for_sections.classList.remove('active');
                form.classList.add('d-block');
            }

            if (document.getElementById('seconds')) {
                document.getElementById('seconds').innerHTML = pad(sec % 60);
            }

            if (document.getElementById('minutes')) {
                document.getElementById('minutes').innerHTML = pad(parseInt(sec / 60, 10) % 60);
            }

        }
        intervalTimerID= setInterval(updateClock, 1000);
    </script>
    {{-- Create Timer --}}

    {{-- Content Functions --}}
    {{-- References Functions --}}
    <script defer>
        function toggle_references_modal_content_element(key) {
            var toggler_button_reference = document.getElementById(`toggler_button_reference_${key}`);
            var body_reference = document.getElementById(`body_reference_${key}`);

            if (body_reference.classList.contains('hide')) {
                body_reference.classList.remove('hide');
                toggler_button_reference.innerHTML = '<i class="fa fa-minus"></i>';
            } else {
                body_reference.classList.add('hide');
                toggler_button_reference.innerHTML = '<i class="fa fa-plus"></i>';
            }
        }

        function toggle_references_modal_content(type) {
            var referance_toggle_buttons = document.getElementsByClassName('referance_toggle_button');
            var referance_bodyes = document.getElementsByClassName('referance_body');
            for (var i = 0; i < referance_toggle_buttons.length; i++) {
                if (type == "open") {
                    referance_toggle_buttons[i].innerHTML = '<i class="fa fa-minus"></i>';
                } else {
                    referance_toggle_buttons[i].innerHTML = '<i class="fa fa-plus"></i>';
                }
            }

            for (var i = 0; i < referance_bodyes.length; i++) {
                if (type == "open") {
                    referance_bodyes[i].classList.remove('hide');
                } else {
                    referance_bodyes[i].classList.add('hide');
                }
            }
        }
    </script>
    {{-- References Functions --}}

    {{-- Exam Functions --}}
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

        function remove_button_toggler() {
            var elements = document.getElementsByClassName('remove_button');

            for (var i = 0; i < elements.length; i++) {
                if (elements[i].classList.contains('active')) {
                    elements[i].classList.remove("active");
                } else {
                    elements[i].classList.add("active");
                }
            }
        }

        function select_answer(question_id, answer_id, type) {
            var all_questions = document.getElementById("all_questions").value;
            var currentDivQuestion = document.getElementById(`content_exam_${question_id}`);
            var answers_selected = document.getElementsByClassName(`answers_${question_id}`);

            if (type == "radio") {
                for (var i = 0; i < answers_selected.length; i++) {
                    answers_selected[i].classList.remove('selected');
                }
            }

            var clicked_el = document.getElementById(`question_answer_one_${question_id}_${answer_id}_${type}`);
            if (clicked_el.classList.contains('selected')) {
                clicked_el.classList.remove('selected');
            } else {
                clicked_el.classList.add('selected');
            }
            if (all_questions != currentDivQuestion.dataset.key) {
                if (type == "radio") {
                    tonext();
                }
            }

            var answer_footer_buttons = document.getElementById(`question_row_button_${question_id}`);
            answer_footer_buttons.classList.add('answered');
        }

        function changeTextBox(question_id, type) {
            var text_box = document.getElementById(`question_answer_one_${question_id}_${type}`).value;
            var answer_footer_buttons = document.getElementById(`question_row_button_${question_id}`);
            if (text_box.length > 0 && text_box != null && $.trim(text_box) != '' && $.trim(text_box) != null && $.trim(
                    text_box) != ' ') {
                answer_footer_buttons.classList.add('answered');
            } else {
                answer_footer_buttons.classList.remove('answered');
            }
        }

        function mark_unmark_question(id) {
            sendAjaxRequest("{{ route('api.mark_unmark_question') }}", "post", {
                question_id: id,
                exam_id: {{ $exam->id }},
                exam_result_id: {{ $exam_result->id }},
                language: '{{ app()->getLocale() }}',
                user_id: document.getElementById("user_id").value,
            }, function(e, t) {
                if (e) toast(e, "error");
                else {
                    let n = JSON.parse(t);
                    toast(n.message, n.status);
                    var marked_questions = document.getElementById('marked_questions');
                    marked_questions.value = JSON.stringify(n.data);
                    var element = document.getElementById(`mark_question_button_${id}`);
                    if (element.classList.contains('active')) {
                        element.classList.remove("active");
                        element.innerHTML = '<i class="far fa-bookmark"></i>';
                    } else {
                        element.classList.add("active");
                        element.innerHTML = '<i class="fa fa-bookmark"></i>';
                    }

                }
            });
        }
    </script>
    {{-- Exam Functions --}}
    {{-- Content Functions --}}

    {{-- Page Functions --}}
    <script defer>
        document.addEventListener("keyup", function(e) {
            var keyCode = e.keyCode ? e.keyCode : e.which;
            if (keyCode == 44) {
                stopPrntScr();
            }
        });

        function stopPrntScr() {
            var inpFld = document.createElement("input");
            inpFld.setAttribute("value", ".");
            inpFld.setAttribute("width", "0");
            inpFld.style.height = "0px";
            inpFld.style.width = "0px";
            inpFld.style.border = "0px";
            document.body.appendChild(inpFld);
            inpFld.select();
            document.execCommand("copy");
            inpFld.remove(inpFld);
        }

        function AccessClipboardData() {
            try {
                window.clipboardData.setData('text', "Access   Restricted");
            } catch (err) {}
        }
        setInterval(AccessClipboardData(), 300);

        function copyToClipboard() {
            var aux = document.createElement("input");
            aux.setAttribute("value", "print screen disabled!");
            document.body.appendChild(aux);
            aux.select();
            document.execCommand("copy");
            // Remove it from the body
            document.body.removeChild(aux);
            toast("@lang('additional.messages.noprint')", 'error')
        }

        $(window).keyup(function(e) {
            if (e.keyCode == 44) {
                copyToClipboard();
            }
        });
    </script>
    <script type="text/javascript">
        window.onbeforeunload = function() {
            return "Dude, are you sure you want to leave? Think of the kittens!";
        }
    </script>

    <script type="text/javascript">
        function disableF5(e) {
            if ((e.which || e.keyCode) == 116 || (e.which || e.keyCode) == 82) e.preventDefault();
        };

        $(document).ready(function() {
            $(document).on("keydown", disableF5);
            document.addEventListener('contextmenu', event => event.preventDefault());
        });
    </script>

    {{-- Page Functions --}}
@endpush