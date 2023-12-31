<div class="match_questions_one">
    @php($questions = [])
    @php($answers = [])
    @foreach ($question->answers as $key => $value)
        @php($json_answers = json_decode($value->answer, true))
        @php($questions[] = ['content' => $json_answers['question_content'], 'elem_id' => $value->id])
        @php($answers[] = ['content' => $json_answers['answer_content'], 'elem_id' => $value->id])
    @endforeach
    <div class="column question_match_area" id="question_match_area_question_{{ $question->id }}">
        @foreach ($questions as $key => $value)
            <div class="column_element question_match_element @if (!empty($exam_result->point)) @if (answer_result_true_or_false($question->id, $exam_result->id) == true) true @else false @endif @endif"
                id="question_match_element_{{ $value['elem_id'] }}">
                <input type="hidden" name="answers[{{ $question->section_id }}][{{ $question->id }}][questions][]"
                    value="{!! $value['content'] !!}">
                {!! $value['content'] !!}
            </div>
        @endforeach
    </div>
    @php($shuffledAnswers = $answers)
    @php(shuffle($shuffledAnswers))

    <div class="column answers_match_area" id="question_match_area_answer_{{ $question->id }}">
        @foreach ($shuffledAnswers as $key => $value)
            <div class="column_element answer_match_element" id="answer_match_element_{{ $value['elem_id'] }}"
                data-question_id="{{ $question->id }}">
                <input type="hidden" name="answers[{{ $question->section_id }}][{{ $question->id }}][answers][]"
                    value="{!! $value['content'] !!}">
                {!! $value['content'] !!}
            </div>
        @endforeach
    </div>
</div>

<p class="text-muted text-small my-2 text-center">@lang('additional.pages.exams.sort_right_tab_elements')</p>
