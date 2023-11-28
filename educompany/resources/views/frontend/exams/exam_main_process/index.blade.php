@extends('frontend.layouts.exam_layout')
@section('title', $exam->name[app()->getLocale() . '_name'])
@section('description', $exam->description[app()->getLocale() . '_description'] ?? '')
@push('css')
    <style>
        #site_footer {
            height: 60px;
        }
    </style>
@endpush
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

    <form action="" id="exam" class="d-block" method="POST">
        @csrf
        <input type="hidden" name="current_question" id="current_question" value="1">
        <input type="hidden" name="show_time" id="show_time" value="true">
        <input type="hidden" name="time_exam" id="time_exam" value="0">
        <input type="hidden" name="marked_questions[]" id="marked_questions">
        <input type="hidden" name="answered_questions[]" id="answered_questions">
        <input type="hidden" name="notanswered_questions[]" id="notanswered_questions">
        <section class="exam_page">
            @include('frontend.exams.exam_main_process.parts.header', [
                'exam' => $exam,
                'questions' => $questions,
            ])
            @include('frontend.exams.exam_main_process.parts.content', [
                'exam' => $exam,
                'questions' => $questions,
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
                            <iframe src="https://www.desmos.com/calculator/ob0gttdkxf?embed"
                                style="width:100%;height:100%;"frameborder=0></iframe>
                        </div>

                    </div>
                </div>
                <br>
            </div>
            {{-- Desmos Calculator --}}

        </section>
    </form>

@endsection
@push('js')
    {{-- Header Buttons --}}
    <script defer>
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
    </script>
    {{-- Header Buttons --}}
    {{-- Footer Buttons --}}
    <script defer>
        // current_question
        // section_name
        function toback() {}

        function togglequestions() {
            var footer_questions = document.getElementById('footer_questions');
            if (footer_questions.classList.contains('active')) {
                footer_questions.classList.remove("active");
            } else {
                footer_questions.classList.add("active");
            }
        }

        function tonext() {}
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
            inputtimeinput.value = sec;
            if (document.getElementById('seconds')) {
                document.getElementById('seconds').innerHTML = pad(sec % 60);
            }

            if (document.getElementById('minutes')) {
                document.getElementById('minutes').innerHTML = pad(parseInt(sec / 60, 10) % 60);
            }

            if (document.getElementById("time")) {
                document.getElementById("time").value = sec;
            }
        }
        setInterval(updateClock, 1000);
    </script>
    {{-- Create Timer --}}
@endpush
