{% extends "global/base.tpl" %}
{% block container %}
    {% if row.slide_shows %}
        {% include ('global/slideshows.tpl') %}
    {% endif %}
    <div class="product-list">
        {% if row.products.featured_products %}
            <div class="featured_products wow animated fadeInUp">
                <div id="featured-products" class="container section">
                    <h1 class="headline text-sm-center">{{ lang('featured_products') }}</h1>
                    <div class="items">
                        {% for p in  row.products.featured_products %}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="thumbnail">
                                        {{ image('products', p.photo_file_name, p.product_name, 'img-fluid mx-auto img-thumbnail d-block') }}
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h3 class="name">
                                        <a href="{{ page_url('product', p) }}">{{ p.product_name }}</a>
                                    </h3>
                                    <p class="card-text">{{ product_price(p) }}</p>
                                    <p class="card-text">{{ parse_text(p.product_overview) }}</p>
                                    <p class="card-text">
                                        <a href="{{ page_url('product', p) }}" class="btn btn-primary">
                                            {{ i('fa fa-search') }} {{ lang('view') }}</a>
                                    </p>
                                </div>
                            </div>
                            <hr />
                        {% endfor %}
                    </div>
                </div>
            </div>
        {% endif %}
        {% if row.products.latest_products %}
            <div class="latest_products wow animated fadeInUp">
                <div id="latest-products" class="container section">
                    <h1 class="headline text-sm-center">{{ lang('latest_products') }}</h1>
                    <div class="items">
                        {% for p in  row.products.latest_products %}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="thumbnail">
                                        {{ image('products', p.photo_file_name, p.product_name, 'img-fluid mx-auto img-thumbnail d-block') }}
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <h3 class="name">
                                        <a href="{{ page_url('product', p) }}">{{ p.product_name }}</a>
                                    </h3>
                                    <p class="card-text">{{ product_price(p) }}</p>
                                    <p class="card-text">{{ parse_text(p.product_overview) }}</p>
                                    <p class="card-text">
                                        <a href="{{ page_url('product', p) }}" class="btn btn-primary">
                                            {{ i('fa fa-search') }} {{ lang('view') }}</a>
                                    </p>
                                </div>
                            </div>
                            <hr />
                        {% endfor %}
                    </div>
                </div>
            </div>
        {% endif %}
    </div>
{% endblock container %}
{% block javascript_footer %}
    {{ parent() }}
    <script src="{{ base_url('js/wow/wow.min.js') }}"></script>
    <script src="{{ base_url('js/owl-carousel/owl.carousel.min.js') }}"></script>

    <script>
        {% if config_enabled('layout_design_product_enable_tag_cloud') %}
        $(document).ready(function () {
            $('#product-tags').fadeOut('slow', function () {
                $('#product-tags').load('{{ base_url }}product/tags/?q=ajax');
                $('#product-tags').fadeIn('300');
            });
        });
        {% endif %}
    </script>
    {{ row.footer_data }}
{% endblock javascript_footer %}