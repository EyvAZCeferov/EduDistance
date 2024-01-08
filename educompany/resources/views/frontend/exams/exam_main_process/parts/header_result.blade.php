@php
    $exam = $exam_result->exam;
@endphp
<div class="header_columns">
    <h1 id="section_name" class="section_name">{{ $exam->sections[0]->name }}</h1>
    <div class="timer_section" id="timer_section">
        <div class="hour_area">
            <span id="minutes">{{ floor($exam_result->time_reply / 60) % 60 }}</span>:<span
                id="seconds">{{ $exam_result->time_reply % 60 }}</span>
        </div>
    </div>

</div>
