<div class="footer_questions" id="footer_questions">
    <div class="title">@lang('additional.pages.exams.questions_title_on_exam_page')</div>
    <div class="question_info_row">
        <div class="col-sm-4">
            <i class="fas fa-map-marker-alt"></i> @lang('additional.pages.exams.cari')
        </div>
        <div class="col-sm-4">
            <i class="fas fa-border-none"></i> @lang('additional.pages.exams.cavablandirilmamis')
        </div>
        <div class="col-sm-4">
            <i class="fas fa-bookmark"></i> @lang('additional.pages.exams.saved')
        </div>
    </div>
    <div class="questions_row">
        @foreach ($questions as $key => $value)
            <button class="btn btn-sm btn-question not_answered" type="button"
                id="question_row_button_{{ $value->id }}"
                onclick="getquestion({{ $value->id }})">{{ $key + 1 }}</button>
        @endforeach
    </div>
    <div class="center_back_button">
        <button type="button" onclick="togglequestions()" class="center_back">
            <i class="fas fa-list-ul"></i>
            @lang('additional.buttons.gotohome')
        </button>
    </div>
    <div class="bottomcorner"></div>
</div>
<div class="footer_buttons">
    <button class="btn btn-primary back_button" type="button" onclick="toback()">
        <i class="fa fa-angle-left"></i> @lang('additional.buttons.back')
    </button>
    <button class="btn btn-primary question_button" type="button" onclick="togglequestions()">
        <i class="fa fa-circle-question"></i>
        @lang('additional.pages.exams.questions_on_exam_page', ['current' => 1, 'total' => count($questions)])
        <i class="fa fa-chevron-up"></i>
    </button>
    <button class="btn btn-secondary next_button" id="next_button" type="button" onclick="tonext()">
        @lang('additional.buttons.next')
        <i class="fa fa-angle-right"></i>
    </button>
</div>
