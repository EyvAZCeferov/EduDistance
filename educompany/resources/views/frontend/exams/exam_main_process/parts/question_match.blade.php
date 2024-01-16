@if (!empty($exam_result) && !empty($exam_result->point))
    <p class="text-muted text-small my-2 text-center">@lang('additional.pages.exams.trueanswer')</p>
@endif

<div class="match_questions_one">
    @php($questions = [])
    @php($answers = [])
    @foreach ($question->answers as $key => $value)
        @php($json_answers = json_decode($value->answer, true))
        @php($questions[] = ['content' => $json_answers['question_content'], 'elem_id' => $value->id])
        @php($answers[] = ['content' => $json_answers['answer_content'], 'elem_id' => $value->id])
    @endforeach
    <input type="hidden" name="answers[{{ $question->section_id }}][{{ $question->id }}][answered]"
        id="question_match_element_{{ $question->section_id }}_{{ $question->id }}" value="0">
    <div class="column question_match_area" id="question_match_area_question_{{ $question->id }}">
        @foreach ($questions as $key => $value)
            <div class="column_element question_match_element"
                id="question_match_element_{{ $value['elem_id'] }}">
                <input type="hidden" name="answers[{{ $question->section_id }}][{{ $question->id }}][questions][]"
                    value="{!! $value['content'] !!}">
                {!! $value['content'] !!}
            </div>
        @endforeach
    </div>
    @php($shuffledAnswers = $answers)
    @if (empty($exam_result->point))
        @php(shuffle($shuffledAnswers))
    @endif
    <div class="column answers_match_area" id="question_match_area_answer_{{ $question->id }}">
        @foreach ($shuffledAnswers as $key => $value)
            <div class="column_element answer_match_element" id="answer_match_element_{{ $value['elem_id'] }}"
                data-question_id="{{ $question->id }}" data-section_id="{{ $question->section_id }}">
                <input type="hidden" name="answers[{{ $question->section_id }}][{{ $question->id }}][answers][]"
                    value="{!! $value['content'] !!}">
                {!! $value['content'] !!}
            </div>
        @endforeach
    </div>
</div>
@if (empty($exam_result->point))
    <p class="text-muted text-small my-2 text-center">@lang('additional.pages.exams.sort_right_tab_elements')</p>
@else
    <p class="text-small my-2 text-center @if (!empty($exam_result) && !empty($exam_result->point) && !empty($exam_result->answers->where("question_id",$question->id)->first()) && !empty($exam_result->answers->where("question_id",$question->id)->first()->value)) @if (answer_result_true_or_false($question->id, $exam_result->answers->where("question_id",$question->id)->first()->value) == true) text-success @else text-danger @endif @endif ">@lang('additional.pages.exams.youranswer')</p>
    <div class="match_questions_one mt-2">
        @php($questions2 = [])
        @php($answers2 = [])
        @foreach (json_decode($exam_result->answers->where("question_id",$question->id)->first()->value,true) as $key => $value)
            @php($questions2[] = ['content' => $key])
            @php($answers2[] = ['content' => $value])
        @endforeach
        <input type="hidden" name="answers[{{ $question->section_id }}][{{ $question->id }}][answered]"
            id="question_match_element_{{ $question->section_id }}_{{ $question->id }}" value="0">
        <div class="column question_match_area" id="question_match_area_question_{{ $question->id }}">
            @foreach ($questions2 as $key => $value)
                <div class="column_element question_match_element">
                    <input type="hidden" name="answers[{{ $question->section_id }}][{{ $question->id }}][questions][]"
                        value="{!! $value['content'] !!}">
                    {!! $value['content'] !!}
                </div>
            @endforeach
        </div>
        @php($shuffledAnswers2 = $answers2)
        <div class="column answers_match_area" id="question_match_area_answer_{{ $question->id }}">
            @foreach ($shuffledAnswers2 as $key => $value)
                <div class="column_element answer_match_element"
                    data-question_id="{{ $question->id }}" data-section_id="{{ $question->section_id }}">
                    <input type="hidden" name="answers[{{ $question->section_id }}][{{ $question->id }}][answers][]"
                        value="{!! $value['content'] !!}">
                    {!! $value['content'] !!}
                </div>
            @endforeach
        </div>
    </div>
@endif
