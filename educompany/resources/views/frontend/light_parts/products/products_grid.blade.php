<div class="products_section">
    @foreach($products as $key => $value)
        @include('frontend.light_parts.products.product_grid_element',['product'=>$value])
    @endforeach
</div>
