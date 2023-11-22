<div class="my-4 py-2">
    @include('frontend.light_parts.section_title',['title'=>trans('additional.pages.exams.' .$title)])
    <ul class="nav nav-pills custom_nav-pills mb-3" id="{{ $nav_id }}_tab" role="tablist">
        @foreach ($tab_datas as $key => $value)
            <li class="nav-item" role="presentation">
                <button class="btn-sm nav-link @if ($key == 0) active @endif"
                    onclick="change_tabs_elements('{{ $nav_id }}',{{ $key }})"
                    id="{{ $nav_id }}-{{ $key }}_button" data-bs-toggle="pill"
                    data-bs-target="#{{ $nav_id }}-{{ $key }}" type="button" role="tab"
                    aria-controls="{{ $nav_id }}-{{ $key }}-tab"
                    aria-selected="true">@lang('additional.pages.exams.' . $key)</button>
            </li>
        @endforeach

    </ul>
    <div class="tab-content custom_tab-content" id="{{ $nav_id }}_tabContent">
        @foreach ($tab_datas as $key => $value)
            <div class="tab-pane fade @if ($key == 0) show active @endif"
                id="{{ $nav_id }}-{{ $key }}_tab" role="tabpanel"
                aria-labelledby="{{ $nav_id }}-{{ $key }}">
                @if (!empty($value) && count($value) > 0)
                @else
                    <p class="not_found_text text-center">@lang('additional.pages.exams.notfound') </p>
                @endif
            </div>
        @endforeach

    </div>
</div>
