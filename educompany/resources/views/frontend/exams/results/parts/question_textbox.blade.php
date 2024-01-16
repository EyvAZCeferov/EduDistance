<div id="question_textbox_{{ $question->id }}" class="d-flex classjustifstart">
    <textarea maxlength="5" rows="10"
        id="question_answer_one_{{ $question->id }}_textbox" name="answers[{{ $question->section_id }}][{{ $question->id }}]"
        class="form-control">{{ !empty($question->correctAnswer()) && isset($question->correctAnswer()->answer) && !empty($question->correctAnswer()->answer) ? strip_tags_with_whitespace($question->correctAnswer()->answer) : null }}</textarea>
    <div class='question_textbox_text' onclick="showuserswhichanswered(JSON.parse(`{{ json_encode(get_answer_choised($exam_results->pluck('id'), $question->id, 3, null)) }}`),3)" id="question_textbox_text_{{ $question->id }}">@lang("additional.pages.exams.youranswer"): <span class='text text-dark' id='question_textbox_text_span_{{ $question->id }}'>{{ count(get_answer_choised($exam_results->pluck('id'), $question->id, 3, null)) }}</span></div>
</div>
