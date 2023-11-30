@foreach ($question->answers as $key => $value)
    <div id="question_answer_one_{{ $question->id }}_{{ $value->id }}_radio"
        class="question_answer_one answers_{{ $question->id }}
        @if (!empty($exam_result->point) && answer_result_true_or_false($question->id, $value->id) == true) true @else false @endif

        @if (
            !empty($exam_result->point) &&
                your_answer_result_true_or_false($question->id, $value->id, $exam_result->id) == true) your_choise @endif"
        onclick="select_answer({{ $question->id }},{{ $value->id }},'radio')">
        <input type="radio" name="answers[{{ $value->question->section_id }}][{{ $question->id }}]"
            value="{{ $value->id }}">
        <div class="rowNumber">{{ int_to_abcd_value($key) }}</div>
        <div class="question">{{ $question->id }} {{ $value->id }} {{ $exam_result->id }}
            {{ your_answer_result_true_or_false($question->id, $value->id, $exam_result->id) }} {!! $value->answer !!}
        </div>
    </div>
@endforeach
