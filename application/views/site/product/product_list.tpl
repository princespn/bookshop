{% extends "global/base.tpl" %}
{% block title %}{{ lang('store') }}{% endblock %}
{% block meta_description %}{{ parent() }} store{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }}, store{% endblock meta_keywords %}
{% block page_header %}
    <div id="product-list-header" class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">
                    {% if search_term %}
                        {{ lang('search') }} - {{ search_term }}
                    {% elseif tag %}
                        {{ tag }}
                    {% elseif c.category_name %}
                        {{ c.category_name }}
                    {% elseif b.brand_name %}
                        {{ b.brand_name }}
                    {% else %}
                        {{ lang('store') }}
                        {% if category_name %}
                            <span class="float-right">{{ category_name }}</span>
                        {% endif %}
                    {% endif %}
                </h2>
            </div>
        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="product-list">
        {{ breadcrumb }}
        <div class="row">
            {% if layout_design_product_page_sidebar == 'left' %}
                {% include ('product/store_sidebar.tpl') %}
            {% endif %}
            <div class="col-md-{% if layout_design_product_page_sidebar == 'none' %}12{% else %}8{% endif %}">
                {% if c.category_banner %}
                    <div class="category-banner mb-3">
                        {{ image('category', c.category_banner, c.category_name, 'img-rounded img-fluid mx-auto d-block') }}
                    </div>
                {% elseif b.brand_banner %}
                    <div class="brand_banner mb-3">
                        {{ image('brand', b.brand_banner, b.brand_name, 'img-rounded img-fluid mx-auto d-block') }}
                    </div>
                {% endif %}
                {% if products %}
                    <div class="products featured">
                        <div class="scroll">
                            {% for p in products %}
                                <div id="product-{{ p.product_id }}" class="row">
                                    <div class="col-md-12">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="thumbnail">
                                                            {{ image('products', p.photo_file_name, p.product_name, 'img-fluid mx-auto img-thumbnail d-block', TRUE) }}
                                                            <hr class="d-sm-none"/>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-9">
                                                        <h4 class="name">
                                                            <a href="{{ page_url('product', p) }}">
                                                                {{ p.product_name }}
                                                            </a>
                                                        </h4>
                                                        <hr class="d-block d-md-none"/>
                                                        <div class="star-rating">{{ format_ratings(p.avg_ratings)}}</div>
                                                        <p class="overview">{{ p.product_overview }}</p>
                                                        {% if p.product_type != 'subscription' %}
                                                            <p class="price">{{ product_price(p) }}</p>
                                                        {% endif %}
                                                        <p class="text-right">
                                                            <a href="{{ page_url('product', p) }}"
                                                               class="btn btn-secondary more-info">{{ i('fa fa-info-circle') }}
                                                                {{ lang('more_info') }}</a>
                                                            {% if p.product_type == 'subscription' %}
                                                                <a href="{{ page_url('product', p) }}"
                                                                   class="btn btn-info subscription">{{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }}
                                                                    {{ lang('payment_options') }}</a>
                                                            {% else %}
                                                                {% if login_for_price(p) == false %}
                                                                    <a href="{{ site_url }}cart/add/{{ p.product_id }}"
                                                                       class="btn btn-primary buy-now">
                                                                        {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }} {{ lang('buy_now') }}
                                                                    </a>
                                                                {% endif %}
                                                            {% endif %}
                                                            {{ affiliate_store_button(p.product_id) }}
                                                        </p>
                                                        <p class="hide">
                                                            <a id="{{ p.product_id }}"></a>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {% endfor %}
                            {% if layout_design_products_enable_infinite_scroll == 1 %}
                                {% if next_scroll %}
                                    <div class="next jscroll-next-parent hide">
                                        <a href="{{ page_options.uri }}/{{ next_scroll }}">next</a></div>
                                {% endif %}
                            {% else %}
                                {% include ('global/pagination.tpl') %}
                            {% endif %}
                        </div>
                    </div>
                {% else %}
                    <div role="alert" class="alert alert-info">
                        <strong>{{ lang('sorry') }}... </strong>{{ lang('no_products_found') }}
                    </div>
                {% endif %}
            </div>
            {% if layout_design_product_page_sidebar == 'right' %}
                {% include ('product/store_sidebar.tpl') %}
            {% endif %}
        </div>
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    {% if config_enabled('layout_design_products_enable_infinite_scroll') %}
        {% if next_scroll %}
            <script src="{{ base_url }}js/jscroll/jquery.jscroll.js"></script>
            <script>
                $('.products').jscroll({
                    loadingHtml: '',
                    contentSelector: '.scroll'
                });
            </script>
        {% endif %}
    {% endif %}
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
{% endblock javascript_footer %}
