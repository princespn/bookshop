{% extends "global/base.tpl" %}
{% block title %} {{ parent() }} {{ lang('store') }}{% endblock %}
{% block meta_description %}{{ parent() }} store{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }}, store{% endblock meta_keywords %}
{% block page_header %}
<div class="container">
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
<div class="product-grid">
    {{ breadcrumb }}
    <div class="row">
        {% if layout_design_product_page_sidebar == 'left' %}
        {% include ('product/store_sidebar.tpl') %}
        {% endif %}
        <div class="col-md-{% if layout_design_product_page_sidebar == 'none' %}12{% else %}9{% endif %}">
            {% if b.brand_banner %}
            <div class="store-banner thumbnail">
                {{ image('product_brand', b.brand_banner, b.brand_name, 'img-fluid') }}
            </div>
            <br/>
            {% elseif b.brand_name %}
            {% if b.description %}
            <p class="hide">{{ b.description }}</p>
            {% endif %}
            {% elseif c.category_banner %}
            <div class="store-banner thumbnail">
                {{ image('product_category', c.category_banner, c.category_name, 'img-fluid') }}
            </div>
            <br/>
            {% elseif c.category_name %}
            {% if c.description %}
            <p class="hide">{{ c.description }}</p>
            {% endif %}
            {% endif %}
            <div class="row">
                <div class="grid col-md-12">
                    {% if products %}
                    <div class="products featured">
                        <div class="scroll">
                            {% for p in products %}
                            <div class="item  col-md-{{ config_option('default_product_grid_size') }}">
                                <div class="card mb-3">
                                    <figure class="gallery-item">
                                        {{ image('products', p.photo_file_name, p.product_name, 'animated fadeIn img-fluid card-img-top') }}
                                        <figcaption class="hover-box">
                                            <h5><a href="{{ page_url('product', p) }}"
                                                   class="btn btn-primary btn-sm item-details">{{ lang('details') }}</a>
                                            </h5>
                                        </figcaption>
                                    </figure>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="name"><a
                                                            href="{{ page_url('product', p) }}">{{ p.product_name }}</a>
                                                </h4>
                                                <hr/>
                                                {% if p.avg_ratings %}
                                                <div class="star-rating">{{ format_ratings(p.avg_ratings)}}</div>
                                                {% endif %}

                                                {% if p.product_type != 'subscription' %}
                                                <p class="price">{{ product_price(p) }}</p>
                                                {% endif %}
                                                <p class="text-right">
                                                    <a href="{{ page_url('product', p) }}"
                                                       class="btn btn-secondary more-info">{{ i('fa fa-info-circle') }}
                                                    </a>
                                                    {% if p.product_type == 'subscription' %}
                                                    <a href="{{ page_url('product', p) }}"
                                                       class="btn btn-info subscription">{{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }}
                                                    </a>
                                                    {% else %}
                                                    <a href="{{ site_url }}cart/add/{{ p.product_id }}"
                                                       class="btn btn-primary buy-now">
                                                        {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }}</a>
                                                    {% endif %}
                                                    {{ affiliate_store_button(p.product_id, FALSE) }}
                                                </p>
                                                <p class="hide">
                                                    <a id="{{ p.product_id }}"></a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {% endfor %}
                        </div>
                    </div>
                    {% include ('global/pagination.tpl') %}
                    {% else %}
                    <br/>
                    <div class="col-md-12">
                        <div role="alert" class="alert alert-info">
                            <strong>{{ lang('sorry') }}... </strong>{{ lang('no_products_found') }}
                        </div>
                    </div>
                    {% endif %}
                </div>
            </div>
        </div>
        {% if layout_design_product_page_sidebar == 'right' %}
        {% include ('product/store_sidebar.tpl') %}
        {% endif %}
    </div>
    {% endblock content %}
    {% block javascript_footer %}
    {{ parent() }}
    <script src="{{ base_url('js/wow/wow.min.js') }}"></script>
    <script src="{{ base_url('js/masonry/masonry.js') }}"></script>
    <script src="{{ base_url('js/masonry/imagesloaded.js') }}"></script>
    <script>
        //Initiat WOW JS
        new WOW().init();
        $(document).ready(function () {
            var $container = $('.products');

            $container.imagesLoaded(function () {
                $container.masonry({
                    isAnimated: true,
                    animationOptions: {
                        duration: 750,
                        easing: 'linear',
                        queue: false
                    },
                    itemSelector: '.item',
                    columnWidth: '.col-md-{{ config_option('default_product_grid_size') }}',
                    transitionDuration: 0
                });
            });

            $(window).scroll(function () {
                $container.imagesLoaded(function () {
                    $container.masonry({
                        isAnimated: true,
                        animationOptions: {
                            duration: 750,
                            easing: 'linear',
                            queue: false
                        },
                        itemSelector: '.item',
                        columnWidth: '.col-md-{{ config_option('default_product_grid_size') }}',
                        transitionDuration: 0
                    });
                });
            });
        });

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
