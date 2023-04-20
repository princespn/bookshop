{% extends "global/base.tpl" %}
{% block title %}{{ lang('your_'~layout_design_shopping_cart_or_bag)|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('your_shopping_'~layout_design_shopping_cart_or_bag) }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('your_shopping_'~layout_design_shopping_cart_or_bag) }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }} {{ lang('your_shopping_'~layout_design_shopping_cart_or_bag) }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="cart-list min-pad-bottom">
        {{ breadcrumb }}
        {% if cart.items %}
            <div class="row">
                <div class="col-md-8">
                    {{ form_open('cart/update', 'id="cart-form"') }}
                    {{ include ('cart/cart_div.tpl') }}
                    <hr/>
                    <div class="row">
                        <div class="col-md-8 text-md-left">
                            <a href="{{ site_url() }}" class="btn btn-primary btn-block-sm">
                                {{ i('fa fa-caret-right') }}
                                {{ lang('keep_shopping') }}</a>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <button class="btn btn-outline-primary btn-block-sm">
                                {{ i('fa fa-refresh') }}
                                {{ lang('update_your_'~layout_design_shopping_cart_or_bag) }}</button>
                        </div>
                    </div>
                    {{ form_close() }}
                </div>
                <div class="col-md-4 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row text-sm-right">
                                <div class="col-6"><h4>{{ lang('subtotal') }}</h4></div>
                                <div class="col-6"><h4>{{ format_amount(cart.totals.sub_total) }}</h4></div>
                            </div>
                            {% if cart.totals.discounts %}
                                <div class="row text-sm-right">
                                    <div class="col-6"><h4>{{ lang('discounts') }}</h4></div>
                                    <div class="col-6"><h4>{{ format_amount(cart.totals.discounts) }}</h4>
                                    </div>
                                </div>
                            {% endif %}
                            {% if cart.totals.coupons %}
                                <div class="row text-sm-right">
                                    <div class="col-6">
                                        <h4>{{ lang('coupon_code') }}</h4>
                                        {% if cart.totals.coupon_codes %}
                                            {% for c in cart.totals.coupon_codes %}
                                                <a href="{{ site_url('cart/remove_coupon/'~c.id) }}" class="text-danger">
                                                    <small>{{ i('fa fa-times-circle') }} {{ c.text }} -
                                                        {% if c.percent == 'percent' %}
                                                            {{ c.amount|number_format(0) }}% {{ lang('percent') }}
                                                        {% else %}
                                                            {{ format_amount(c.amount) }}
                                                        {% endif %}
                                                        {{ lang('off') }}
                                                    </small>
                                                </a>
                                            {% endfor %}
                                        {% endif %}
                                    </div>
                                    <div class="col-6">
                                        <h4>
                                            {% if  cart.totals.total == 0 %}
                                                {{ format_amount(-(cart.totals.sub_total_discounts + cart.totals.taxes)) }}
                                            {% else %}
                                                {{ format_amount(cart.totals.coupons) }}
                                            {% endif %}
                                        </h4>
                                    </div>
                                </div>
                            {% endif %}
                            <hr />
                            {% if cart.totals.taxes %}
                                <div class="row text-sm-right">
                                    <div class="col-6"><h4>{{ lang('taxes') }}</h4></div>
                                    <div class="col-6"><h4>{{ format_amount(cart.totals.taxes) }}</h4></div>
                                </div>
                            {% endif %}
                            <div class="row text-sm-right">
                                <div class="col-6"><h4>{{ lang('total') }}</h4></div>
                                <div class="col-6"><h4>{{ format_amount(cart.totals.total) }}</h4></div>
                            </div>
                            <hr/>
                            <div class="form-group">
                                <div class="input-group">
                                    <input id="discount-code" type="text" name="discount_code" class="form-control"
                                           placeholder="{{ lang('discount_code') }}">
                                    <div class="input-group-append">
                                        <button id="apply-discount" class="btn btn-secondary" type="button">
                                            {{ i('fa fa-refresh') }} {{ lang('apply') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="payment">
                                {% if check_minimum_purchase(cart.totals.total) %}
                                    {% if config_enabled('sts_affiliate_require_referral_code') %}
                                        <a href="{{ site_url('cart/referral') }}"
                                           class="btn btn-block btn-lg btn-dark">
                                            {{ i('fa fa-caret-right') }}
                                            {{ lang('proceed_to_checkout') }}</a>
                                    {% else %}
                                        <a href="{{ ssl_url('checkout/cart') }}"
                                           class="btn btn-block btn-lg btn-primary">
                                            {{ i('fa fa-caret-right') }}
                                            {{ lang('proceed_to_checkout') }}</a>
                                    {% endif %}
                                {% else %}
                                    <div class="alert alert-warning">
                                      {{ i('fa fa-info-circle') }}  {{ format_amount(config_option('sts_cart_minimum_purchase_checkout')) }} {{ lang('minimum_amount_required_to_checkout') }}
                                    </div>
                                {% endif %}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="col-md-12">
                    {% if recommended_products %}
                        <div class="card">
                            <div class="card-header">{{ lang('you_may_also_like') }}</div>
                            <div class="card-body">
                                <div class="row recommended_products carousel-four">
                                    {% for p in recommended_products %}
                                        <div class="item">
                                            <div id="product-{{ p.product_id }}" class="gallery-item">
                                                <figure>
                                                    {{ image('products', p.photo_file_name, p.product_name, 'img-fluid mx-auto d-block') }}
                                                    <figcaption class="hover-box">
                                                        <h5>
                                                            <a href="{{ page_url('product', p) }}"
                                                               class="btn btn-secondary more-info">{{ i('fa fa-info-circle') }}
                                                                {{ lang('info') }}</a>
                                                        </h5>
                                                    </figcaption>
                                                </figure>
                                            </div>
                                            <div class="text-md-center">
                                                <strong>
                                                    <a href="{{ page_url('product', p) }}">{{ p.product_name }}</a>
                                                </strong>
                                                <p class="price">{{ product_price(p) }}</p>
                                            </div>
                                        </div>
                                    {% endfor %}
                                </div>
                            </div>
                        </div>
                    {% endif %}
                </div>
            </div>
        {% else %}
            <div role="alert" class="alert alert-light">
                <h3>{{ lang('no_items_in_cart') }}</h3>
                <p>{{ lang('your_shopping_cart_is_empty') }}</p>
                <p>
                    <a href="{{ site_url }}" class="btn btn-secondary">
                        {{ lang('continue_shopping') }}</a>
                </p>
            </div>
        {% endif %}
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
{{ include('js/cart_js.tpl') }}
{% endblock javascript_footer %}