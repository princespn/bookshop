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
                        <h2>{{ lang(config_option('module_payment_gateways_bank_transfer_title')) }}</h2>
                        <p>
                            {{ lang(config_option('module_payment_gateways_bank_transfer_instructions')) }}
                        </p>
                        {% if order_data.order.order_number %}
                            <p>{{ lang('order_number') }} #{{ order_data.order.order_number }}</p>
                        {% endif %}
                        {% if order_data.invoice.data.invoice_number %}
                            <p>{{ lang('invoice_number') }} #{{ order_data.invoice.data.invoice_number }}</p>
                        {% endif %}
                        <h5>{{ config_option('module_payment_gateways_bank_transfer_account_name') }} -
                            {{ config_option('module_payment_gateways_bank_transfer_bank_name') }}
                        </h5>
                        <br/>
                        <table class="cart-table table table-hover">
                            <thead>
                            <tr>
                                <th>{{ lang('account_number') }}</th>
                                <th>{{ lang('routing_number') }}</th>
                                <th>{{ lang('iban') }}</th>
                                <th>{{ lang('bic_swift') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{ config_option('module_payment_gateways_bank_transfer_account_number') }}</td>
                                <td>{{ config_option('module_payment_gateways_bank_transfer_routing_number') }}</td>
                                <td>{{ config_option('module_payment_gateways_bank_transfer_iban') }}</td>
                                <td>{{ config_option('module_payment_gateways_bank_transfer_bic_swift') }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <br />
                        <div class="row">
                            <div class="col-md-12 text-sm-right">
                                <a href="{{ site_url('members') }}" class="btn btn-primary">{{ i('fa fa-caret-right') }} {{ lang('continue_to_members_area') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock content %}