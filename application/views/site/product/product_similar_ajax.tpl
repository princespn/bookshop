{% if products %}

<div class="container">
    <h2 class="headline">{{ lang('other_products_you_might_also_like') }}</h2>
    <div id="similar-carousel">
        <div class="row">
            {% for p in products %}
                <div class="col-md-3 {% if loop.first %}active{% endif %}">
                    <div id="product-{{ p.product_id }}" class="card">
                        <a href="{{ page_url('product', p) }}">
                        {{ image('products', p.photo_file_name, p.product_name, 'card-img-top img-fluid') }}
                        </a>
                        <div class="card-body text-center">
                            <h4 class="card-title"><a href="{{ page_url('product', p) }}">{{ p.product_name }}</a></h4>
                            <p class="card-text">{{ product_price(p) }}</p>
                            <a href="{{ site_url }}cart/add/{{ p.product_id }}"
                               class="btn btn-outline-primary btn-block buy-now">
                                {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }} {{ lang('add_to_'~layout_design_shopping_cart_or_bag) }}</a>

                        </div>
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>
</div>
{% endif %}