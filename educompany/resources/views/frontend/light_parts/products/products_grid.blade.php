<div class="products_section">
    <input type="hidden" name="deleting_question_id" id="deleting_question_id">
    <input type="hidden" name="deleting_question_type" id="deleting_question_type">
    @if (isset($addable) && $addable == true)
        @if (auth('users')->check() && auth('users')->user()->user_type == 2)
            <a class="products_section_element add_product"
                href="{{ route('exams_front.createoredit', ['category' => $value->id]) }}">
                <div class="content">
                    @lang('additional.buttons.add') <i class="fa fa-plus"></i>
                </div>
            </a>
        @endif
    @endif
    @foreach ($products as $key => $value)
        @include('frontend.light_parts.products.product_grid_element', ['product' => $value])
    @endforeach
</div>


<script defer>
    function deleteproduct(id = null, type = null) {
        var deleting_question_id = document.getElementById('deleting_question_id');
        var deleting_question_type = document.getElementById('deleting_question_type');
        var current_section = document.getElementById('current_section');
        if (type != null)
            deleting_question_type.value = type

        if (id != null) {
            var deleting_question_id = document.getElementById('deleting_question_id');
            deleting_question_id.value = id;
            toggleModalnow('deleteModal', 'open');
        } else {
            sendAjaxRequestOLD(`{{ route('front.questionsorsection.remove') }}`, "post", {
                element_id: deleting_question_id.value,
                element_type: deleting_question_type.value,
                language: '{{ app()->getLocale() }}'
            }, function(e,
                t) {
                if (e) toast(e, "error");
                else {
                    let n = JSON.parse(t);
                    if (n.message != null)
                        toast(n.message, n.status);

                    deleting_question_id.value = null;
                    deleting_question_type.value = null;
                    toggleModalnow('deleteModal', 'hide');

                    if (deleting_question_type == "question")
                        get_section(current_section.value);
                    else
                        getsectiondatas();
                }
            });
        }
    }
</script>
