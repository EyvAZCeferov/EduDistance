<div class="answers">
    @for ($i = 1; $i < 15; $i++)
        <div class="answer single">
            <label class="radio-input">
                <input type="radio" name="answers[{{ $i++ }}]" onchange="change_radio(1,{{ $i }})" value="{{ $i }}" class="input_radios" id="input_radios_{{ $i }}" >
                <span class="checkmark"></span>
            </label>
            <input type="text" name="answer_reply[{{ $i++ }}]" class="text-input" placeholder="@lang('additional.forms.answer')">
        </div>
    @endfor
</div>
