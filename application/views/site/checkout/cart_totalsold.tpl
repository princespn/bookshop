<div class="col-md-5">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ i('fa fa-file-text-o') }} {{ lang('order_totals') }}</h5>

            <div class="card-text">
                <hr/>
                <div class="row text-right">
                    <div class="col-7"><h5>{{ lang('subtotal') }}</h5></div>
                    <div class="col-5">
                        <h5><span id="sub_total">{{ format_amount(cart.totals.sub_total) }}</span>
                        </h5>
                    </div>
                </div>
                {% if cart.totals.discounts %}
                <div class="row text-right">
                    <div class="col-7"><h5>{{ lang('discounts_and_promos') }}</h5></div>
                    <div class="col-5">
                        <h5>
                            <span id="discounts">{{ format_amount(cart.totals.discounts) }}</span>
                        </h5>
                    </div>
                </div>
                {% endif %}
                {% if cart.totals.coupons %}
                <div class="row text-right">
                    <div class="col-7">
                        {% if cart.totals.coupon_codes %}
                        <h5>{{ lang('discount_codes') }}</h5>
                        {% endif %}
                    </div>
                    <div class="col-5">
                        <h5><span id="coupons">
                                                 {% if  cart.totals.total == 0 %}
                                {{ format_amount(-(cart.totals.sub_total_discounts + cart.totals.taxes)) }}
                                                 {% else %}
                                {{ format_amount(cart.totals.coupons) }}
                                                 {% endif %}
                                            </span></h5>
                    </div>
                </div>
                <div class="row text-right">
                    <div class="col-sm-12">
                        {% for c in cart.totals.coupon_codes %}
                        <div class="mb-2">
                            <small class="text-danger">{{ c.text }} -
                                {% if c.percent == 'percent' %}
                                {{ c.amount|number_format(0) }}% {{ lang('percent') }}
                                {% else %}
                                {{ format_amount(c.amount) }}
                                {% endif %}
                                {{ lang('off') }}
                            </small>
                        </div>
                        {% endfor %}
                    </div>
                </div>
                {% endif %}
                <div id="shipping-box" class="shipping-box row text-right hide">
                    <div class="col-7"><h5>{{ lang('shipping') }}</h5></div>
                    <div class="col-5">
                        <h5><span id="shipping">{{ format_amount(cart.totals.shipping) }}</span>
                        </h5>
                    </div>
                </div>

                <div id="tax-box" class="row text-right {% if cart.totals.taxes %}{% else %}hide{% endif %}">
                    <div class="col-7"><h5>{{ lang('taxes') }}</div>
                    <div class="col-5">
                        <h5><span id="taxes">{{ format_amount(cart.totals.taxes) }}</span></h5>
                    </div>
                </div>

                <hr/>
                <div class="row gift-certificate-item  {% if cart.totals.gift_certificates == false %}hide {% endif %}">
                    <div class="col-7 mb-2 text-right">
                                            <span id="remove-code" class="cursor">
                                                <small id="cert_code" class="text-muted">
                                                {{ cart.totals.gift_certificate.code }}
                                                    {{ i('fa fa-delete') }}
                                            </small>
                    </div>
                    <div class="col-5 text-right">
                        <small id="cert_amount" class="text-muted">
                            {{ format_amount(cart.totals.gift_certificate.amount) }}
                        </small>
                    </div>
                </div>
                <div class="row text-right">
                    <div class="col-7"><h5>{{ lang('total') }}</h5></div>
                    <div class="col-5">
                        <h5><span id="total">{{ format_amount(cart.totals.total) }}</span></h5>
                    </div>
                </div>
                {% if (config_enabled('sts_cart_allow_currency_conversion')) %}
                {% if (currency.code != sess('custom_currency')) %}
                <div class="row text-right">
                    <div class="col-7"><h5 class="text-danger">{{ lang('total_payment_in') }} {{currency.code}}</h5></div>
                    <div class="col-5">
                        <h5 class="text-danger pb-0 mb-0">{{currency.symbol_left}}<span id="original_total">{{ format_amount(cart.totals.total, false, false) }}
                                                </span>{{currency.symbol_right}}</h5>
                        <small id="currency_rate" class="text-muted hide"> {{ lang('rate')}}: {{ custom_currency_array.value }}</small>
                    </div>
                </div>
                {% endif %}
                {% endif %}
                <div class="row text-right">
                    <div class="col-12">
                        <small id="subscription_text">{{ cart.totals.subscription.text }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {% if config_enabled('sts_order_enable_gift_certificates') %}
    <div class="gift-certificate-box card  hide">
        <div class="card-body">
            <h5 class="card-title">{{ i('fa fa-tag') }} {{ lang('redeem_gift_certificate') }}</h5>
            <hr/>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="input-group">
                            <input id="gift-certificate" type="text" name="gift_certificate"
                                   class="form-control"
                                   placeholder="{{ lang('enter_code') }}">
                            <div class="input-group-append">
                                <button id="apply-code" class="btn btn-secondary" type="button">
                                    {{ i('fa fa-refresh') }} {{ lang('apply') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {% endif %}
    <div class="shipping-box card  mb-3 hide">
        <div class="card-body">
            <h5 class="card-title">{{ i('fa fa-truck') }} {{ lang('your_shipping_option') }}</h5>
            <hr/>
            <div id="shipping_info">{{ shipping_option }}</div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }} {{ lang(layout_design_shopping_cart_or_bag~'_contents') }}</h5>
            <div class="card-text">
                {% for p in cart.items %}
                <hr/>
                <div class="row">
                    <div class="col-7">{{ p.product_name }}</div>
                    <div class="col-5 text-right">
                        <small>{{ lang('qty') }} - {{ p.quantity }}</small>
                    </div>
                </div>
                {% endfor %}
            </div>
        </div>
    </div>
</div>