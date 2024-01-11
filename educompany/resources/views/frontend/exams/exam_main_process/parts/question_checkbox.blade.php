{{-- @foreach ($question->answers as $key => $value)
    <div id="question_answer_one_{{ $question->id }}_{{ $value->id }}_checkbox"
        class="question_answer_one answers_{{ $question->id }}"
        onclick="select_answer({{ $question->id }},{{ $value->id }},'checkbox')">
        <input type="checkbox" name="answers[{{ $value->question->section_id }}][{{ $question->id }}][]" value="{{ $value->id }}">
        <div class="rowNumber">{{ int_to_abcd_value($key) }}</div>
        <div class="question">{!! $value->answer !!}</div>
    </div>
@endforeach --}}


@foreach ($question->answers as $key => $value)
<div class="question_answer_one_element_container" id="question_answer_one_element_container_{{ $question->id }}_{{ $value->id }}">
    <div id="question_answer_one_{{ $question->id }}_{{ $value->id }}_checkbox"
        class="question_answer_one answers_{{ $question->id }}
        @if (!empty($exam_result->point)) @if (answer_result_true_or_false($question->id, $value->id) == true) true @else false @endif @endif
        @if (!empty($exam_result->point)) @if (your_answer_result_true_or_false($question->id, $value->id, $exam_result->id) == true) your_choise @endif @endif"
        onclick="select_answer({{ $question->id }},{{ $value->id }},'checkbox')">
        <input type="checkbox" name="answers[{{ $value->question->section_id }}][{{ $question->id }}][]"
            value="{{ $value->id }}">
        <div class="rowNumber">{{ int_to_abcd_value($key) }}</div>
        <div class="question">
            {!! $value->answer !!}
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-question_container_undo_or_redo" id='question_container_undo_or_redo_{{ $question->id }}_{{ $value->id }}' onclick="toggleabcline({{ $question->id }},{{ $value->id }})">
        <i class="fa fa-plus-circle"></i>
    </button>
</div>
@endforeach

