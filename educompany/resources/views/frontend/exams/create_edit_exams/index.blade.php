@extends('frontend.layouts.create_exam_layout')
@section('title', trans('additional.pages.exams.exams') . isset($data) && !empty($data) && isset($data->id) ?
    trans('additional.buttons.edit') : trans('additional.buttons.add'))
@section('content')
    <section class="add_edit_exam">
        <form action="{{ route('user.exam.add_edit_exam') }}" class="d-block" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="language" id="language" value="{{ app()->getLocale() }}">
            <input type="hidden" name="auth_id" id="auth_id" value="{{ auth('users')->id() }}">
            <input type="hidden" name="current_section" id="current_section">
            @if (isset($data) && !empty($data) && isset($data->id)) <input type="hidden" name="top_id"
                    value="{{ $data->id }}"> @endif
            <div class="row my-2">
                @if (isset($data) && !empty($data) && isset($data->id))
                    @include('frontend.light_parts.section_title', [
                        'title' => trans('additional.buttons.edit'),
                    ])
                @else
                    @include('frontend.light_parts.section_title', [
                        'title' => trans('additional.buttons.add'),
                    ])
                @endif
            </div>
            <div class="row mt-1 my-3">
                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <input required type="text" class="form-control w-100" name="exam_name"
                        placeholder="@lang('additional.forms.exam_name')"
                        value="{{ isset($data) && !empty($data) && !empty($data->name) ? $data->name[app()->getLocale() . '_name'] : null }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <select required name="category_id" class="form-control select2" data-placeholder="@lang('additional.pages.exams.category')"
                        placeholder="@lang('additional.pages.exams.category')">
                        <option value="" disabled @if (!(isset($data) && !empty($data) && isset($data->category_id) && !empty($data->category_id))) selected @endif>@lang('additional.pages.exams.category')
                        </option>
                        @foreach (\App\Models\Category::whereNull('parent_id')->with('sub')->get() as $category)
                            <optgroup label="{{ $category->name['az_name'] }}">
                                @foreach ($category->sub as $sub)
                                    <option
                                        {{ old('category_id', isset($data) && !empty($data) && isset($data->category_id) && !empty($data->category_id) ? $data->category_id : null) == $sub->id ? 'selected' : '' }}
                                        value="{{ $sub->id }}">{{ $sub->name['az_name'] }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <input type="number" required
                        value="{{ old('duration', isset($data) && !empty($data) && !empty($data->duration) ? $data->duration : null) }}"
                        name="duration" placeholder="@lang('additional.forms.exam_duration')"
                        class="form-control {{ $errors->first('duration') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    @if (isset($data) && !empty($data) && !empty($data->image))

                        <img src="{{ getImageUrl($data->image, 'exams') }}" class="img-fluid img-responsive"
                            style="height: 150px">
                    @endif
                    <input type="file" @if (!(isset($data) && !empty($data) && !empty($data->image))) required @endif name="image"
                        class="form-control {{ $errors->first('image') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <input type="number" required
                        value="{{ old('point', isset($data) && !empty($data) && isset($data->point) && !empty($data->point) ? $data->point : null) }}"
                        name="point" placeholder="@lang('additional.forms.exam_point')"
                        class="form-control {{ $errors->first('point') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <input type="number" required
                        value="{{ old('price', isset($data) && !empty($data) && isset($data->price) && !empty($data->price) ? $data->price : null) }}"
                        name="price" placeholder="@lang('additional.forms.exam_price')"
                        class="form-control {{ $errors->first('price') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <input type="number" required
                        value="{{ old('endirim_price', isset($data) && !empty($data) && isset($data->endirim_price) && !empty($data->endirim_price) ? $data->endirim_price : null) }}"
                        name="endirim_price" placeholder="@lang('additional.forms.exam_endirim_price')"
                        class="form-control {{ $errors->first('endirim_price') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    {{-- Status --}}
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                        @if (isset($data) && !empty($data) && !empty($data->status)) @if ($data->status == true) checked @endif @else checked
                            @endif type="checkbox" id="exam_status" name="exam_status">
                        <label class="form-check-label" for="exam_status">@lang('additional.forms.exam_status')</label>
                    </div>
                    {{-- Status --}}

                    {{-- show_result_user --}}
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                        @if (isset($data) && !empty($data) && !empty($data->show_result_user)) @if ($data->show_result_user == true) checked @endif @else checked
                            @endif type="checkbox" id="exam_show_result_answer"
                        name="exam_show_result_answer">
                        <label class="form-check-label" for="exam_show_result_answer">@lang('additional.forms.exam_show_result_answer')</label>
                    </div>
                    {{-- show_result_user --}}

                    {{-- show_calc --}}
                    <div class="form-check form-switch">
                        <input class="form-check-input"
                        @if (isset($data) && !empty($data) && !empty($data->show_calc)) @if ($data->show_calc == true) checked @endif @else
                            checked @endif type="checkbox" id="show_calculator" name="show_calculator">
                        <label class="form-check-label" for="show_calculator">@lang('additional.forms.show_calculator')</label>
                    </div>
                    {{-- show_calc --}}
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <label for="start_time">@lang('additional.forms.exam_start_time')</label>
                    <input id="start_time" type="datetime-local"
                        value="{{ old('start_time', isset($data) && !empty($data) && isset($data->start_time) && !empty($data->start_time) ? $data->start_time : null) }}"
                        name="start_time" placeholder="@lang('additional.forms.exam_start_time')"
                        class="form-control {{ $errors->first('start_time') ? 'is-invalid' : '' }}">
                </div>

                <div class="col-sm-12 col-md-6 col-lg-3 my-1">
                    <label for="layout">@lang('additional.forms.exam_layout')</label>
                    <select name="layout_type"
                        class="form-control {{ $errors->first('layout_type') ? 'is-invalid' : '' }}">
                        @foreach (\App\Models\Exam::LAYOUTS as $key => $type)
                            <option
                                {{ old('layout_type', isset($data) && !empty($data) && isset($data->id) ? $data->layout_type : null) == $type ? 'selected' : '' }}
                                value="{{ $key }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-12 col-md-12 col-lg-12 my-2">
                    <textarea name="description" class="form-control summernote {{ $errors->first('description') ? 'is-invalid' : '' }}"
                        placeholder="@lang('additional.forms.exam_description')" rows="4">{!! old(
                            'description',
                            isset($data) &&
                            !empty($data) &&
                            !empty($data->content) &&
                            isset($data->content[app()->getLocale() . '_description']) &&
                            !empty($data->content[app()->getLocale() . '_description'])
                                ? $data->content[app()->getLocale() . '_description']
                                : null,
                        ) !!}</textarea>
                </div>
            </div>
            {{-- Suallar, Section lar --}}
            <div class="row my-2 classcenter">
                <button type="submit" class="btn btn-success btn-block">
                    @if (isset($data) && !empty($data) && isset($data->id)) @lang('additional.buttons.edit')
                    @else
                        @lang('additional.buttons.add') @endif
                </button>
            </div>
        </form>
        {{-- Suallar, Section lar --}}
        @if (isset($data) && !empty($data) && isset($data->id))
            @include('frontend.exams.create_edit_exams.section_and_questions', ['data' => $data])
        @endif

    </section>

    {{-- Add Section Calculator --}}
    <div id="create_sections" class="modal custom-modal show" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>@lang('additional.pages.exams.sections') @lang('additional.buttons.add')</h3>
                    <button type="button" class="close" onclick="toggleModalnow('create_sections', 'hide')"
                        data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row my-2">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="section_name">@lang('additional.forms.field_name')</label>
                                <input placeholder="@lang('additional.forms.field_name')" type="text" value="{{ old('section_name') }}"
                                    id="section_name" name="section_name"
                                    class="form-control {{ $errors->first('section_name') ? 'is-invalid' : '' }} w-100">
                            </div>

                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="section_duration">@lang('additional.forms.field_duration')</label>
                                <input type="number" value="{{ old('section_duration', 0) }}" name="section_duration"
                                    id="section_duration"
                                    class="form-control {{ $errors->first('section_duration') ? 'is-invalid' : '' }} w-100"
                                    placeholder="@lang('additional.forms.field_duration')">
                            </div>

                        </div>
                    </div>
                    <div class="row mt-2">
                        <button type="button" onclick="create_section(event)"
                            class="btn btn-success btn-block">@lang('additional.buttons.add')</button>
                    </div>
                </div>

            </div>
        </div>
        <br>
    </div>
    {{-- Add Section Calculator --}}

@endsection
@push('js')
    @if (isset($data) && !empty($data) && isset($data->id))
        {{-- Section Functions --}}
        <script defer>
            function create_section(event) {
                event.preventDefault();
                var section_name = document.getElementById('section_name').value.trim();
                var section_duration = document.getElementById('section_duration').value.trim();
                if (section_name && section_duration) {
                    sendAjaxRequest("{{ route('sections.store', $data->id) }}", "post", {
                        exam_id: {{ $data->id }},
                        language: document.getElementById("language").value,
                        user_id: document.getElementById("auth_id").value,
                        name: section_name,
                        time_range_sections: section_duration,
                        responseType: 'json'
                    }, function(e, t) {
                        if (e) toast(e, "error");
                        else {
                            let n = JSON.parse(t);
                            toast(n.message, n.status);

                            if (n.data != null && n.data.length > 0) {
                                var sectionElementsDiv = document.getElementById('section_elements');
                                section_elements.innerHTML='';
                                toggleModalnow('create_sections','hide');
                                n.data.forEach(function(item) {
                                    var divElement = `<div class='section_element' onclick=get_section(${item.id})
                                    id='section_element_${item.id}'>${item.name}</div>`;
                                    document.body.innerHTML+=divElement;
                                });
                            }
                        }
                    });
                } else {
                    toast("@lang('additional.messages.required_fill')", 'error');
                }
            }

            function get_section(id) {
                var section_id = document.getElementById(`section_element_${id}`);
                var section_elements = document.getElementsByClassName('section_element');
                var section_and_questions = document.getElementById('section_and_questions');
                section_and_questions.classList.add("hide");
                for (let i = 0; i < section_elements.length; i++) {
                    const element = section_elements[i];
                    element.classList.remove("active");
                }
                section_id.classList.add("active");
                section_and_questions.classList.remove("hide");

                var questions = [];
                sendAjaxRequest(`/admin/exams/questions/{{ $data->id }}/${id}?responseType=json`, "get", function(e, t) {
                    if (e) toast(e, "error");
                    else {
                        let n = JSON.parse(t);
                        toast(n.message, n.status);

                        if (n.data != null && n.data.length > 0) {
                            questions = n.data;
                            console.log(n.data);
                        }
                    }
                });
            }

            function create_question() {
                var section_and_questions_right = document.getElementById('section_and_questions_right');
                var element = `<div class="form_question">
                    <form class='d-block' method="post">
                        @csrf
                        <input type="hidden" name="section_id" id="section_id">
                    <div class="col-sm-12 col-md-12 col-lg-12 my-1 d-none" id="question_content_textbox">
                        <div class="form-group" style="height:150px">
                            <textarea name="question" rows="5" id="question" class="form-control summernote" plceholder="Sualınız" ></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 my-1 d-none" id="question_content_audio">
                        <div class="form-group">
                            <input type="file" name="question_audio" class="file" accept="audio/*">
                        </div>
                    </div>
                    <input type='hidden' name='question_type' id='question_type' />
                    <div id="answers_area"></div>
                    <div class="col-sm-12 col-md-12 col-lg-12 my-1 left_area">

                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="customDropdownButton" aria-haspopup="true" aria-expanded="false">
                                @lang('additional.buttons.answer_type')
                            </button>
                            <div class="dropdown-menu" aria-labelledby="customDropdownButton">
                                @foreach (App\Models\ExamQuestion::TYPES as $k => $type)
                                    <a class="dropdown-item" onclick="set_type({{ $type }})" href="javascript:void(0)">{{ $k }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-12 col-lg-12 my-1 classjustifybetween">
                        <div></div>
                        <button class='btn btn-primary btn-sm submit_answer hide' id="submit_answer" type="submit">Təsdiq et</button>
                    </div>
                </form>
                </div>`;

                section_and_questions_right.innerHTML = element;
                const dropdownButton = document.getElementById('customDropdownButton');
                const dropdownMenu = document.querySelector('.dropdown-menu');
                dropdownButton.addEventListener('click', function() {
                    if (dropdownMenu.classList.contains('show')) {
                        dropdownMenu.classList.remove('show');
                    } else {
                        dropdownMenu.classList.add('show');
                    }
                });

                document.addEventListener('click', function(e) {
                    if (!dropdownButton.contains(e.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }
        </script>
    @endif

    <script defer>
        function set_type(type) {
            var question_content_textbox = document.getElementById("question_content_textbox");
            var question_content_audio = document.getElementById("question_content_audio");
            var answers_area = document.getElementById('answers_area');
            var question_type = document.getElementById('question_type');
            var submit_answer = document.getElementById('submit_answer');
            if (type == 5) {
                question_content_audio.classList.remove('d-none');
                question_content_textbox.classList.add("d-none");
            } else {
                question_content_audio.classList.add('d-none');
                question_content_textbox.classList.remove("d-none");
            }

            submit_answer.classList.remove("hide");
            question_type.value = type;
            var answers = ``;
            if (type == 3) {
                answers = `@include('frontend.exams.create_edit_exams.question_textbox')`;
            } else {
                answers = `@include('frontend.exams.create_edit_exams.question_radio')`;
            }

            answers_area.innerHTML = answers;

        }

        function change_radio(type, id) {
            var input_radios = document.getElementsByClassName('input_radios');
            for (var i = 0; i < input_radios.length; i++) {
                var element = input_radios[i];
                element.prop('checked', false);
            }

            var element = docuemnt.getElementById('input_radios_' + id);
            element.prop('checked', true);
        }
        @if (isset($data) && !empty($data) && isset($data->id))
            function post_question() {
                var question_type = document.getElementById('question_type');
                var question = document.getElementById('question');

            }
        @endif
    </script>
    {{-- Section Functions --}}
@endpush
