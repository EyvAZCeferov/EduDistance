<textarea rows="10" onchange="changeTextBox({{ $question->id }},'textbox')"
    id="question_answer_one_{{ $question->id }}_textbox"
    name="answers[{{ $question->section_id }}][{{ $question->id }}]" placeholder="@lang('additional.pages.exams.question_text_area_placeholder')" class="form-control"></textarea>
