<div class="conlol hide" id="showfinishmodal">
    <div class="footer_questions" id="footer_questions_top">
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
</div>
<div id="content_area_exam" class="conlol">
    @foreach ($questions as $key => $value)
        <div class="content_exam @if ($key == 0) show @endif  {{ $value->layout }}"
            data-key="{{ $key + 1 }}" data-id="{{ $value->id }}" id="content_exam_{{ $value->id }}"
            data-section_id="{{ $value->section_id }}" data-section_name="{{ $value->section->name }}">
            <div class="col left_col" id="left_col">
                @if ($value->type != 5)
                    <div class="buttons_top_aplusandminus">
                        <div></div>
                        <div class="buttons">
                            <a href="javascript:void(0)" class="button left"
                                onclick="increase_decrease_font('increase')">A+</a>
                            <a href="javascript:void(0)" class="button right"
                                onclick="increase_decrease_font('decrease')">A-</a>
                        </div>
                    </div>
                @endif
                <div class="content_exam_info @if ($value->type == 5) classcenter @endif">
                    @if ($value->type == 5)
                        <audio controlsList="nodownload" controls
                            @if ($exam->repeat_sound == false) class="only1time" @endif id="audio_{{ $value->id }}">
                            <source src="{{ getImageUrl($value->question, 'exam_questions') }}"
                                type="audio/{{ pathinfo($value->question, PATHINFO_EXTENSION) }}">
                            Your browser does not support the audio element.
                        </audio>
                    @else
                        {!! $value->question !!}
                    @endif
                </div>
            </div>
            @if ($value->layout != 'onepage')
                <div id="resizer"></div>
            @endif
            <div class="col right_col" id="right_col">
                <div class="question_header">
                    <div>
                        <span class="question_number">{{ $key + 1 }}</span>
                        <a href="javascript:void(0)" onclick="mark_unmark_question({{ $value->id }})"
                            id="mark_question_button_{{ $value->id }}"
                            class="mark_button @if (!empty(question_is_marked($value->id, $exam->id, $exam_result->id, auth('users')->id()))) active @endif">
                            @if (!empty(question_is_marked($value->id, $exam->id, $exam_result->id, auth('users')->id())))
                                <i class="fa fa-bookmark"></i>
                            @else
                                <i class="far fa-bookmark"></i>
                            @endif
                        </a>
                        <span class="info_text">@lang('additional.pages.exams.question_info_text')</span>
                    </div>
                    <div>
                        @if ($value->type != 3 && !isset($hide_abc))
                            <a href="javascript:void(0)" class="remove_button" onclick="remove_button_toggler()">
                                ABC
                            </a>
                        @endif
                    </div>

                </div>
                <div class="question_content">
                    @if ($value->type == 1 || $value->type == 5)
                        @include('frontend.exams.exam_main_process.parts.question_radio', [
                            'question' => $value,
                            'exam_result' => $exam_result,
                        ])
                    @elseif($value->type == 2)
                        @include('frontend.exams.exam_main_process.parts.question_checkbox', [
                            'question' => $value,
                            'exam_result' => $exam_result,
                        ])
                    @elseif($value->type == 3)
                        @include('frontend.exams.exam_main_process.parts.question_textbox', [
                            'question' => $value,
                            'exam_result' => $exam_result,
                        ])
                    @elseif($value->type == 4)
                        @include('frontend.exams.exam_main_process.parts.question_match', [
                            'question' => $value,
                            'exam_result' => $exam_result,
                        ])
                    @endif
                </div>

            </div>
        </div>
    @endforeach
</div>
