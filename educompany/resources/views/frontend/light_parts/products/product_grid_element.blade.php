<a class="products_section_element" href="{{ route('exams.show', $product->slug) }}">
    @if ($product->price > 0)
        @if ($product->endirim_price > 0)
            <div class="products_section_element_price_endirim">@lang("additional.pages.exams.endirim_with_faiz",['count'=>count_endirim_faiz($product->price,$product->endirim_price)])</div>
        @endif
    @endif

    <div class="products_section_element_header">
        <img src="{{ getImageUrl($product->image, 'exams') }}" alt="{{ $product->name[app()->getLocale() . '_name'] }}">
    </div>

    <div class="products_section_element_content">
        <h5 class="products_section_element_content_name">{{ mb_substr($product->name[app()->getLocale() . '_name'],0,10) }}</h5>
        <button class="products_section_element_content_button @if ($product->price == 0) free_price @endif">
            @if ($product->price > 0)
                @if ($product->endirim_price > 0)
                    {{ $product->endirim_price }} <span class="deleted_price">{{ $product->price }}</span>
                @else
                    {{ $product->price }}
                @endif

                AZN
            @else
                @lang('additional.buttons.free')
            @endif
        </button>
    </div>
</a>
