@if (empty($product->start_time) || (!empty($product->start_time) && is_int($product->start_time)))
    @php
        $start_time = $product->start_time ? \Carbon\Carbon::createFromTimestamp($product->start_time) : null;
    @endphp
    <a class="products_section_element"
        @if (auth('users')->check() && auth('users')->user()->user_type == 2 && auth('users')->id() == $product->user_id) href="{{ route('exams_front.createoredit', ['slug' => $product->slug]) }}" @else
        @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time)) href="{{ route('exams.show', $product->slug) }}" @endif
        @endif>
        @if ($product->price > 0)
            @if ($product->endirim_price > 0)
                <div class="products_section_element_price_endirim">@lang('additional.pages.exams.endirim_with_faiz', ['count' => count_endirim_faiz($product->price, $product->endirim_price)])</div>
            @endif
        @endif
        @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time))
        @else
            <span class="products_section_element_price_endirim still_waiting text-white">@lang('additional.pages.exams.stillwait', ['timestamp' => $start_time->format('d.m.Y H:i')])</span>
        @endif

        @if (auth('users')->check() && auth('users')->user()->user_type == 2 && auth('users')->id() == $product->user_id)
            <span class="product_section_element_edit"
                onclick="window.location.href='{{ route('exams_front.createoredit', ['slug' => $product->slug]) }}';"><i
                    class="fa fa-pencil"></i></span>
        @endif

        <div class="products_section_element_header">
            <img src="{{ getImageUrl($product->image, 'exams') }}"
                alt="{{ $product->name[app()->getLocale() . '_name'] }}">
        </div>

        <div class="products_section_element_content">
            <h5 class="products_section_element_content_name">
                {{ mb_substr($product->name[app()->getLocale() . '_name'], 0, 10) }}</h5>
            @if ($start_time === null || \Carbon\Carbon::now()->greaterThan($start_time))
                <button
                    class="products_section_element_content_button @if ($product->price == 0) free_price @endif">
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
            @endif
        </div>
    </a>
@endif
