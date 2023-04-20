{% extends "global/base_no_header.tpl" %}
{% block title %}{{ lang('invoice')|capitalize }} {{ invoice_number(p) }}{% endblock %}
{% block header %}
    {% block logo %}
        <div class="row">
            <div class="col-12 text-center">
                {% if (config_enabled('sts_invoice_enable_logo')) %}
                    {% if layout_design_site_logo %}
                        <div class="logo">
                            <img src="{{ layout_design_site_logo }}" alt="{{ lang('logo') }}" class="img-fluid"/>
                        </div>
                    {% else %}
                        <h4 style="margin-top: 0">{{ sts_site_name }}</h4>
                    {% endif %}
                {% else %}
                    <h4 style="margin-top: 0">{{ sts_site_name }}</h4>
                {% endif %}
                <div>
                    <small class="text-muted">{{ sts_site_shipping_address_1 }}
                        {{ sts_site_shipping_city }} {{ sts_site_shipping_region_name }} {{ sts_site_shipping_postal_code }}
                        {{ sts_site_shipping_country_name }}
                        {{ sts_site_phone_number }}
                    </small>
                </div>

            </div>
        </div>
    {% endblock logo %}
    <hr />
{% endblock header %}
{% block content %}
    <div id="invoice-details">
        <div class="row">
            <div class="col-6">
                <h2>{{ lang('invoice') }}# {{ invoice_number(p) }}</h2>
            </div>
            <div class="col-6 text-right">
                <h2>
                    <span class="badge badge-info"
                          style="background-color: {{ p.color }}">{{ p.payment_status }}</span> </span>
                </h2>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div id="date-purchased" class="col-6">
                <h5>{{ lang('invoice_date') }}: {{ display_date(p.date_purchased) }}</h5>
            </div>
            <div id="due-date" class="col-6 text-right">
                <h5>{{ lang('due_date') }}: {{ display_date(p.due_date) }}</h5>
            </div>
        </div>
        <div class="row">
            <div id="account-info" class="col-sm-12">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card-body">
                            <h4 class="card-title">{{ lang('account_information') }}</h4>
                            <address>
                                {{ p.customer_name }}<br/>
                                {% if p.customer_company %}
                                    {{ p.customer_company }}<br/>
                                {% endif %}
                                {{ p.customer_address_1 }}<br/>
                                {% if p.customer_address_2 %}
                                    {{ p.customer_address_2 }}<br/>
                                {% endif %}
                                {{ p.customer_city }} {{ p.customer_region_name }} {{ p.customer_postal_code }} <br/>
                                {{ p.customer_country_name }}
                            </address>
                        </div>
                    </div>
                    {% if  p.shipping_address_1 %}
                    <div class="col-sm-6">
                        <div class="card-body">
                            <h4 class="card-title">{{ lang('shipping_information') }}</h4>
                            <address>
                                {{ p.shipping_name }}<br/>
                                {% if p.shipping_company %}
                                    {{ p.shipping_company }}<br/>
                                {% endif %}
                                {{ p.shipping_address_1 }}<br/>
                                {% if p.shipping_address_2 %}
                                    {{ p.shipping_address_2 }}<br/>
                                {% endif %}
                                {{ p.shipping_city }} {{ p.shipping_region_name }} {{ p.shipping_postal_code }}
                                <br/>
                                {{ p.shipping_country_name }}
                            </address>
                        </div>
                        {% endif %}
                    </div>
                </div>
            </div>
        </div>
        <div id="invoice-products" class="row">
            <div class="col-sm-12">
                <table id="invoice-products-table" class="table table-striped">
                    <thead>
                    <tr>
                        <th class="text-md-center">{{ lang('product_code') }}</th>
                        <th>{{ lang('product_name') }}</th>
                        <th class="text-md-center">{{ lang('unit') }}</th>
                        <th class="text-md-center">{{ lang('quantity') }}</th>
                        <th class="text-md-center">{{ lang('price') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {% if p.items %}
                        {% for s in p.items %}
                            <tr>
                                <td class="text-md-center">{{ s.product_sku }}</td>
                                <td>
                                    {{ s.invoice_item_name }}
                                    {% if s.product_notes %}
                                        <p>
                                            <small class="text-muted">{{ format_notes(s.product_notes) }}</small>
                                        </p>
                                    {% endif %}
                                </td>
                                <td class="text-md-center">{{ unit_price(s.unit_price, s.tax_amount) }}</td>
                                <td class="text-md-center">{{ s.quantity }}</td>
                                <td class="text-md-center">{{ unit_price((s.unit_price * s.quantity), s.tax_amount)}}</td>
                            </tr>
                        {% endfor %}
                    {% endif %}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div id="notes" class="col-sm-8">
                <table class="table">
                    <tr>
                        <td>
                        <h5>{{ lang('invoice_notes') }}</h5>
                        <p>{{ format_notes(p.invoice_notes) }}</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="totals" class="col-sm-4">
                <table class="table table-striped">
                    <thead>
                    <tr class="text-md-center">
                        <th></th>
                        <th class="text-md-center">
                            {{ lang('invoice_amount') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    {% if p.totals %}
                        {% for t in p.totals %}
                            {% if t.type != 'points' %}
                                <tr>
                                    <td class="text-md-right"><h5>{{ lang(t.type) }}</h5></td>
                                    <td class="text-md-center"><h5>{{ format_amount(t.amount) }}</h5></td>
                                </tr>
                            {% endif %}
                        {% endfor %}
                    {% endif %}
                    </tbody>
                </table>
                {% if (p.require_payment == '1') %}
                    <a href="{{ site_url() }}invoice/pay/{{ p.invoice_id }}"
                       class="btn btn-lg btn-block btn-success">
                        {{ i('fa fa-arrow-circle-o-right') }} {{ lang('pay_invoice') }}
                    </a>
                    <a href="{{ page_url('members', 'invoices/details/'~p.invoice_id~'/email') }}"
                       class="btn btn-block btn-lg btn-primary">{{ i('fa fa-envelope') }} {{ lang('email_invoice') }}</a>
                    <a href="{{ page_url('members', 'invoices/details/'~p.invoice_id~'/print') }}"
                       class="btn btn-secondary btn-lg btn-block">{{ i('fa fa-print') }} {{ lang('print_invoice') }}</a>
                {% endif %}
            </div>
        </div>

        {% if p.payments %}
            <hr/>
            <h5>{{ lang('invoice_payments') }}</h5>
            <div class="row">
                <div id="payments" class="col-sm-12">
                    <table id="invoice-payments-table" class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ lang('payment_date') }}</th>
                            <th class="text-md-center">{{ lang('method') }}</th>
                            <th class="text-md-center">{{ lang('transaction_id') }}</th>
                            <th class="text-md-center">{{ lang('payment_amount') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for s in p.payments %}
                            <tr>
                                <td>{{ display_date(s.date) }}</td>
                                <th class="text-md-center">{{ lang(s.method) }}</th>
                                <td class="text-md-center">{{ s.transaction_id }}</td>
                                <td class="text-md-center">{{ format_amount(s.amount) }}</td>
                            </tr>
                        {% endfor %}
                        </tbody>
                    </table>
                </div>
            </div>
        {% endif %}

        {% if (config_enabled('sts_invoice_show_commissions')) %}
            {% if p.commissions %}
                <hr/>
                <h5>{{ lang('referred_commissions') }}</h5>
                <div class="row">
                    <div id="commissions" class="col-sm-12">
                        <table id="invoice-commissions-table" class="table table-striped">
                            <thead>
                            <tr>
                                <th class="text-md-center">{{ lang('status') }}</th>
                                <th class="text-md-center">{{ lang('referred_by') }}</th>
                                <th class="text-md-center">{{ lang('commission_amount') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {% for c in p.commissions %}
                                <tr>
                                    <td class="text-md-center">
                                        <span class="badge badge-info badge-{{ c.comm_status }}">{{ c.comm_status }}</span>
                                    </td>
                                    <td class="text-md-center">{{ c.username }}</td>
                                    <td class="text-md-center">{{ format_amount(c.commission_amount - c.tax_amount) }}</td>
                                </tr>
                            {% endfor %}
                            </tbody>
                        </table>
                    </div>
                </div>
            {% endif %}
        {% endif %}
    </div>
{% endblock content %}