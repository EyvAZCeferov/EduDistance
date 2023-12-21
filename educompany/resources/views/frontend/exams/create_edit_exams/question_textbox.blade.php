<div class="answers">
    @php($answerid = createRandomCode('string', 11))
    <div class="answer textbox" id="{{ $answerid }}">
        <div class="answer_content">
            <textarea rows="5" name="answer_reply[0]" class="text-input" placeholder="@lang('additional.forms.answer')"></textarea>
        </div>
    </div>
</div>
<p class="text-muted notification_element">Cavabları (əgər birdən çox düzgün cavab varsa) vergül ilə ayıra bilərsiniz</p>
