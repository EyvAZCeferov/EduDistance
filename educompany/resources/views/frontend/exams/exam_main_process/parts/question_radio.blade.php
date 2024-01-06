@foreach ($question->answers as $key => $value)
    <div id="question_answer_one_{{ $question->id }}_{{ $value->id }}_radio"
        class="question_answer_one answers_{{ $question->id }}
        @if (!empty($exam_result->point)) @if (answer_result_true_or_false($question->id, $value->id) == true) true @else false @endif @endif

        @if (!empty($exam_result->point)) @if (your_answer_result_true_or_false($question->id, $value->id, $exam_result->id) == true) your_choise @endif @endif"
        onclick="select_answer({{ $question->id }},{{ $value->id }},'radio')">
        <input type="radio" name="answers[{{ $value->question->section_id }}][{{ $question->id }}]"
            value="{{ $value->id }}">
        <div class="rowNumber">{{ int_to_abcd_value($key) }}</div>
        <div class="question">
            {!! $value->answer !!}
        </div>
    </div>
@endforeach
