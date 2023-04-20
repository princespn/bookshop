{% extends "global/base.tpl" %}
{% block title %} {{ parent() }} {{ lang('store') }}{% endblock %}
{% block meta_description %}{{ parent() }} store{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }}, store{% endblock meta_keywords %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ member.fname }}'s  {{ lang('wish_list') }}</h2>
            </div>
        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="product-list">
        {{ breadcrumb }}
        {% if products %}
        <div class="row">
            {% if layout_design_product_page_sidebar == 'left' %}
                {% include ('product/default_sidebar.tpl') %}
            {% endif %}
            <div class="col-md-{% if layout_design_product_page_sidebar == 'none' %}12{% else %}8{% endif %}">
                    <div class="products featured">
                        <div class="scroll">
                            {% for p in products %}
                                <div id="product-{{ p.product_id }}" class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="thumbnail">
                                                            {{ image('products', p.photo_file_name, p.product_name, 'img-fluid img-thumbnail', TRUE) }}
                                                            <hr class="hidden-sm-up" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <h3 class="name">
                                                            <a href="{{ page_url('product', p) }}">
                                                                {{ p.product_name }}
                                                            </a>
                                                        </h3>
                                                        <hr/>
                                                        {% if p.avg_ratings %}
                                                        <div class="star-rating">{{ format_ratings(p.avg_ratings)}}</div>
                                                        {% endif %}
                                                        <p class="overview">{{ p.product_overview }}</p>
                                                        <p class="price">{{ product_price(p) }}</p>
                                                        <p class="text-right">
                                                            <a href="{{ page_url('product', p) }}"
                                                               class="btn btn-secondary more-info">{{ i('fa fa-info-circle') }}
                                                                {{  lang('more_info') }}</a>
                                                            <a href="{{ site_url }}cart/add/{{ p.product_id }}"
                                                               class="btn btn-primary buy-now">
                                                                {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }} {{ lang('buy_now') }}</a>
                                                        </p>
                                                        <p class="hide">
                                                            <a name="{{ p.product_id }}"></a>
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
            </div>
            {% if layout_design_product_page_sidebar == 'right' %}
                {% include ('product/default_sidebar.tpl') %}
            {% endif %}
        </div>
        {%  else %}
        <div role="alert" class="alert alert-info">
            <strong>{{ lang('sorry') }}... </strong>{{ lang('no_products_in_your_wish_list') }}
        </div>
        {% endif %}
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    {% if layout_design_products_enable_infinite_scroll == 1 %}
        {% if next_scroll %}
            <script src="{{ base_url }}js/jscroll/jquery.jscroll.min.js"></script>
            <script>
                $('.products').jscroll({
                    loadingHtml: '',
                    contentSelector: '.scroll'
                });
            </script>
        {% endif %}
    {% endif %}
{% endblock javascript_footer %}
