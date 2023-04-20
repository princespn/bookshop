{% extends "global/base.tpl" %}
{% block title %}{{ lang('thank_you_for_your_order')|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('thank_you') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('thank_you') }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block content %}
    <div class="checkout-thankyou">
        <div class="row">
            <div class="col-md-12">
                {{ breadcrumb }}
                <div class="card content">
                    <div class="card-body">
                        <h2>{{ lang(config_option('module_payment_gateways_check_payment_title')) }}</h2>
                        <p>{{ lang(config_option('module_payment_gateways_check_payment_instructions')) }}</p>
                        {% if order_data.order.order_number %}
                            <p>{{ lang('order_number') }} #{{ order_data.order.order_number }}</p>
                        {% endif %}
                        {% if order_data.invoice.data.invoice_number %}
                            <p>{{ lang('invoice_number') }} #{{ order_data.invoice.data.invoice_number }}</p>
                        {% endif %}
                        <p class="text-sm-right">
                            <a href="{{ site_url('members') }}"
                               class="btn btn-primary">{{ i('fa fa-caret-right') }} {{ lang('continue_to_members_area') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock content %}