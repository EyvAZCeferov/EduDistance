@extends('frontend.layouts.app')
@section('title', $exam->name[app()->getLocale() . '_name'])
@section('content')
    @php
        $questions = collect();
        $qesutions = $exam->sections->pluck('questions');
        foreach ($qesutions as $qesution) {
            foreach ($qesution as $key => $qest) {
                $questions[] = $qest;
            }
        }
    @endphp
    <section class="exam_page">
        <input type="hidden" name="first_question" id="first_question" value="{{ $questions[0]->id }}">
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
        <input type="hidden" name="answered_questions[]" id="answered_questions">
        <input type="hidden" name="notanswered_questions[]" id="notanswered_questions">
        <input type="hidden" name="user_id" id="user_id" value="{{ auth('users')->id() }}">
        <input type="hidden" name="exam_id" id="exam_id" value="{{ $exam->id }}">
        <input type="hidden" name="language" id="language" value="{{ app()->getLocale() }}">
        @include('frontend.exams.results.parts.header_result', [
            'exam' => $exam,
        ])
        @include('frontend.exams.results.parts.content', [
            'exam' => $exam,
            'questions' => $questions,
            'exam_results' => $exam_results,
            'hide_abc' => 'hide',
        ])
        @include('frontend.exams.results.parts.footer', [
            'exam' => $exam,
            'questions' => $questions,
            'exam_results' => $exam_results,
        ])
    </section>
    {{-- Users Modal --}}
    <div id="usersmodal" class="modal custom-modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h3 class="text-white">@lang('additional.pages.exams.students')</h3>
                    <button type="button" class="close text-white" onclick="toggleModalnow('usersmodal', 'hide')"
                        data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body overflow-scroll max-h-32">
                    @foreach ($exam_results->whereNotNull('point') as $result)
                        <div class="row mb-1 p-1 pb-2" style="border-bottom:1px solid #ccc;">
                            <h6>{{ $result->user->name }} / {{ $result->user->email }}</h6>
                            <div class='text text-dark'>
                                @lang('additional.pages.exams.earned_point'): {{ round($result->point, 2) }} / <span
                                    class="text-success">{{ $exam->point }}</span>
                                &nbsp;&nbsp;@lang('additional.pages.exams.timespent'):
                                <div class="hour_area d-inline-block text text-info">
                                    <span id="minutes">{{ floor($result->time_reply / 60) % 60 }}</span>:<span
                                        id="seconds">{{ $result->time_reply % 60 }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
        <br>
    </div>
    {{-- Users Modal --}}
@endsection
@push('js')
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
                var nextDivQuestion = document.querySelectorAll(`.content_exam[data-key="${new_key}"]`);
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

        function tonext(tolast = false) {
            var current_question = document.getElementById("current_question").value;
            var all_questions = document.getElementById("all_questions").value;
            var currentDivQuestion = document.getElementById(`content_exam_${current_question}`);
            var time_range_sections = document.getElementById("time_range_sections").value;
            var next_section = document.getElementById("next_section").value;
            var section_start_time = document.getElementById("section_start_time");
            var loader_for_sections = document.getElementById("loader_for_sections");
            var form = document.getElementById("exam");
            if (all_questions == currentDivQuestion.dataset.key) {
                window.location.href = "/";
            } else {
                currentDivQuestion.classList.remove("show");
                var new_key = parseInt(currentDivQuestion.dataset.key) + 1;
                var nextDivQuestion = document.querySelectorAll(`.content_exam[data-key="${new_key}"]`);
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
            var first_question = document.getElementById("first_question").value;

            var footer_question_buttons = document.getElementsByClassName('footer_question_buttons');
            if (current_question == first_question) {
                document.getElementById("to_back").classList.add('hide');
            } else {
                if (document.getElementById("to_back").classList.contains('hide'))
                    document.getElementById("to_back").classList.remove('hide');
            }
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
                        next_button.innerHTML =
                            `@lang('additional.buttons.nextsection') @if ($exam->layout_type == 'standart')<i class="fa fa-angle-right"></i>@endif`;
                    } else {
                        next_button.innerHTML =
                            `@lang('additional.buttons.finished')`;
                    }
                } else {
                    next_button.innerHTML =
                        `@lang('additional.buttons.finished')`;
                }
            } else {
                next_button.classList.add("btn-secondary");
                next_button.classList.remove("active");
                next_button.innerHTML =
                    `@lang('additional.buttons.next')@if ($exam->layout_type == 'standart') <i class="fa fa-angle-right"></i> @endif`;
            }

            var current_question_text = document.getElementById("current_question_text");
            current_question_text.innerText = currentDivQuestion.dataset.key;

            for (var i = 0; i < buttons.length; i++) {
                if (buttons[i].id == `question_row_button_${current_question}`)
                    buttons[i].classList.add("current");

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
    <script defer>
        const leftCol = document.getElementsByClassName('left_col');
        const resizer = document.getElementsByClassName('resizer');

        function draggingleftandrightcolumns() {
            for (let index = 0; index < resizer.length; index++) {
                const element = resizer[index];
                element.addEventListener("mousedown", (e) => {
                    e.preventDefault();
                    document.addEventListener("mousemove", resize);
                    document.addEventListener("mouseup", () => {
                        document.removeEventListener("mousemove", resize);
                    });
                });
                element.addEventListener("mouseover", (e) => {
                    element.style.opacity = 1;
                });
                element.addEventListener("mouseleave", (e) => {
                    element.style.opacity = 0.5;
                });
            }

        }

        function resize(e) {
            const size = `${e.clientX}px`;
            for (let index = 0; index < leftCol.length; index++) {
                const element = leftCol[index];
                element.style.width = size;
            }
        }

        function mark_list_questions(question_id) {
            try {
                showLoader();
                sendAjaxRequestOLD(`{{ route('api.get_markedquestions_users') }}`, "post", {
                        exam_id: {{ $exam->id }},
                        question_id: question_id,
                    },
                    function(e,
                        t) {
                        if (e) toast(e, "error");
                        else {
                            let n = JSON.parse(t);
                            hideLoader();
                            if (n.message != null)
                                toast(n.message, n.status);

                            if (n.data != null && n.data.length > 0) {
                                var elements;
                                for (var i = 0; i < n.data.length; i++) {

                                    var element = `<div class="my-1 mb-2 p-1 row">
                                        <h6>${n.data[i].name} / ${n.data[i].email}</h6
                                        </div>`;
                                    elements += element;
                                }

                                var modalmarks=`<div id="modalmarks" class="modal custom-modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-secondary">
                                                <h3 class="text-white">@lang('additional.pages.exams.forviewselected')</h3>
                                                <button type="button" class="close text-white" onclick="toggleModalnow('modalmarks', 'hide')"
                                                    data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body overflow-scroll max-h-32">
                                                ${elements}
                                            </div>

                                        </div>
                                    </div>
                                    <br>
                                </div>`;

                                document.body.innerHTML+=modalmarks;
                                toggleModalnow('modalmarks', 'open');
                            }else{
                                var modalmarks=`<div id="modalmarks" class="modal custom-modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-secondary">
                                                <h3 class="text-white">@lang('additional.pages.exams.forviewselected')</h3>
                                                <button type="button" class="close text-white" onclick="toggleModalnow('modalmarks', 'hide')"
                                                    data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body overflow-scroll max-h-32">
                                                @lang('additional.pages.exams.notfound')
                                            </div>

                                        </div>
                                    </div>
                                    <br>
                                </div>`;
                                document.body.innerHTML+=modalmarks;
                                toggleModalnow('modalmarks', 'open');
                            }
                        }
                    });

            } catch (error) {
                toast(error, 'error');
            }
        }

        function showuserswhichanswered(object,type){
            try{
                showLoader();

                var showuseranswersmodal = document.getElementById('showuseranswers');
                if (showuseranswersmodal != null) {
                    showuseranswersmodal.remove();
                }

                if(object!=null && object.length>0){
                    var elementsanswers;
                    for (var i = 0; i < object.length; i++) {
                        if(object[i]!=null && object[i]!=undefined && object[i].result_model!=null && object[i].result_model!=undefined && object[i].result_model.user!=null && object[i].result_model.user!=undefined && object[i].result_model.user.name!=null && object[i].result_model.user.name!=undefined && object[i].result_model.user.name!='' && object[i].result_model.user.name!=' '){
                            var element = `<div class="my-1 mb-2 p-1 row">
                                <h6>${object[i].result_model.user.name} / ${object[i].result_model.user.email}</h6
                                </div>`;
                            elementsanswers += element;
                        }
                    }

                    var showuseranswers=`<div id="showuseranswers" class="modal custom-modal show" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-secondary">
                                    <h3 class="text-white">@lang('additional.pages.exams.answered_students')</h3>
                                    <button type="button" class="close text-white" onclick="toggleModalnow('showuseranswers', 'hide')"
                                        data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body overflow-scroll max-h-32">
                                    ${elementsanswers}
                                </div>

                            </div>
                        </div>
                        <br>
                    </div>`;

                    document.body.innerHTML+=showuseranswers;
                    toggleModalnow('showuseranswers', 'open');
                }else{
                    hideLoader();
                    toast("@lang('additional.pages.exams.notfound')",'warning');
                }

                hideLoader();
            }catch(error){
                hideLoader();
                toast(error, 'error');
            }
        }

        window.addEventListener('load', function() {
            draggingleftandrightcolumns();
        });
    </script>
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
