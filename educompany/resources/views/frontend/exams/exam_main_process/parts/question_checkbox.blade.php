@foreach ($question->answers as $key => $value)
    <div id="question_answer_one_{{ $question->id }}_{{ $value->id }}_checkbox"
        class="question_answer_one answers_{{ $question->id }}"
        onclick="select_answer({{ $question->id }},{{ $value->id }},'checkbox')">
        <input type="checkbox" name="answers[{{ $value->question->section_id }}][{{ $question->id }}][]" value="{{ $value->id }}">
        <div class="rowNumber">{{ int_to_abcd_value($key) }}</div>
        <div class="question">{!! $value->answer !!}</div>
    </div>
@endforeach
