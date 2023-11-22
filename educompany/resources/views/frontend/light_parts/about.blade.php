@if (!empty(standartpages('about', 'type')) && !empty(standartpages('about', 'type')->name))
    @include('frontend.light_parts.section_title',['title'=>standartpages('about', 'type')->name[app()->getLocale().'_name']])
    <section class="row about">
        <div class="col-sm-12 col-md-6 column">
            <p class="text">{!! standartpages('about', 'type')->description[app()->getLocale() . '_description'] !!}</p>
        </div>
        @if (!empty(standartpages('about', 'type')) && !empty(standartpages('about', 'type')->images))
            <div class="col-sm-12 col-md-6 column imagecolumn">
                @foreach (standartpages('about', 'type')->images as $key => $value)
                    <img src="{{ getImageUrl($value, 'standartpages') }}" alt="{{ $value }}"
                        class="img-fluid img-responsive">
                @endforeach
            </div>
        @endif
    </section>
@endif
