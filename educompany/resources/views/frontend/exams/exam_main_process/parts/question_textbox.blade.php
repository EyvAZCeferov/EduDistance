<div id="question_textbox_{{ $question->id }}" class="d-flex classjustifstart">
    <textarea maxlength="5" rows="10" onkeyup="changeTextBox({{ $question->id }},'textbox')"
        id="question_answer_one_{{ $question->id }}_textbox" name="answers[{{ $question->section_id }}][{{ $question->id }}]"
        class="form-control"></textarea>
    <div class='question_textbox_text' id="question_textbox_text_{{ $question->id }}">@lang("additional.pages.exams.youranswer"): <span class='text text-dark' id='question_textbox_text_span_{{ $question->id }}'></span></div>
</div>
