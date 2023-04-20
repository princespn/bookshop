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
        <div class="row">
            <div class="col-md-12">
                {% if cart.items %}
                    {{ form_open('cart/update', 'id="cart-form"') }}
                    {{ include ('cart/cart_div.tpl') }}
                    <hr/>
                    <div class="row">
                        <div class="col-md-5 r">
                            <div class="form-group">
                                
                            </div>
                        </div>
                        <div class="col-md-7 text-right">
                            
                        </div>
                    </div>
                    {{ form_close() }}
                    <div class="row mt-3">
                        <div class="col-md-7">
                            {% if recommended_products %}
                                <div class="mb-3">
                                    <div>
                                        <h6>{{ lang('you_may_also_like') }}</h6>
                                        <div class="row recommended_products carousel-three">
                                            {% for p in recommended_products %}
                                                <div class="item">
                                                    <div id="product-{{ p.product_id }}" class="gallery-item">
                                                        <figure>
                                                            {{ image('products', p.photo_file_name, p.product_name, 'img-thumbnail') }}
                                                            <figcaption class="hover-box">
                                                                <h5>
                                                                    <a href="{{ page_url('product', p) }}"
                                                                       class="btn btn-secondary btn-sm more-info">{{ i('fa fa-info-circle') }}
                                                                        {{ lang('details') }}</a>
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
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row text-sm-right">
                                        <div class="col-7"><h3>{{ lang('subtotal') }}</h3></div>
                                        <div class="col-5"><h3>{{ format_amount(cart.totals.sub_total) }}</h3></div>
                                    </div>
                                    {% if cart.totals.discounts %}
                                        <div class="row text-sm-right">
                                            <div class="col-7"><h3>{{ lang('discounts') }}</h3></div>
                                            <div class="col-5"><h3>{{ format_amount(cart.totals.discounts) }}</h3>
                                            </div>
                                        </div>
                                    {% endif %}
                                    {% if cart.totals.coupons %}
                                        <div class="row text-sm-right">
                                            <div class="col-7">
                                                <h3>{{ lang('coupon_code') }}</h3>
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
                                            <div class="col-5">
                                                <h3>
                                                    {% if  cart.totals.total == 0 %}
                                                        {{ format_amount(-(cart.totals.sub_total_discounts + cart.totals.taxes)) }}
                                                    {% else %}
                                                        {{ format_amount(cart.totals.coupons) }}
                                                    {% endif %}
                                                </h3>

                                            </div>
                                        </div>
                                    {% endif %}
                                    <hr />
                                    {% if cart.totals.taxes %}
                                        <div class="row text-sm-right">
                                            <div class="col-7"><h3>{{ lang('taxes') }}</h3></div>
                                            <div class="col-5"><h3>{{ format_amount(cart.totals.taxes) }}</h3></div>
                                        </div>
                                    {% endif %}
                                    <div class="row text-sm-right">
                                        <div class="col-7"><h3>{{ lang('total') }}</h3></div>
                                        <div class="col-5"><h3>{{ format_amount(cart.totals.total) }}</h3></div>
                                    </div>
                                    <hr/>
                                    <div class="payment">
                                        {% if check_minimum_purchase(cart.totals.total) %}
                                            {% if config_enabled('sts_affiliate_require_referral_code') %}
                                                <a href="{{ site_url('cart/referral') }}"
                                                   class="btn btn-block btn-lg btn-dark">
                                                    {{ i('fa fa-caret-right') }}
                                                    {{ lang('proceed_to_checkout') }}</a>
                                            {% else %}
                                                <a href="{{ ssl_url('checkout/cart') }}"
                                                   class="submit-button btn btn-block btn-lg btn-primary">
                                                    {{ i('fa fa-caret-right') }}
                                                    <span>{{ lang('proceed_to_checkout') }}</span></a>
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
                {% else %}
                    <div role="alert" class="alert alert-light">
                        <h3>{{ lang('no_items_in_cart') }}</h3>
                        <p>{{ lang('your_shopping_cart_is_empty') }}</p>
                        <p>
                            <a href="{{ site_url }}" class="btn btn-secondary">{{ lang('continue_shopping') }}</a>
                        </p>
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
{{ include('js/cart_js.tpl') }}
{% endblock javascript_footer %}