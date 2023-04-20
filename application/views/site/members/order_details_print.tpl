{% extends "global/base_no_header.tpl" %}
{% block title %}{{ lang('order')|capitalize }} {{ p.order_number }}{% endblock %}
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
    <hr/>
{% endblock header %}
{% block content %}
    <div class="row">
        <div class="col-md-12">
            <div id="order-details">
                <div class="row">
                    <div class="col-6">
                        <h2>{{ lang('order') }} #{{ p.order_number }}</h2>
                    </div>
                    <div class="col-6 text-right">
                        <h2>
                    <span class="badge badge-info"
                          style="background-color: {{ p.color }}">{{ lang(p.order_status) }}</span>
                        </h2>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div id="date-ordered" class="col-6">
                        <h5>{{ lang('order_date') }}: {{ display_date(p.date_ordered) }}</h5>
                    </div>
                    {% if p.invoice_id %}
                        <div id="invoice" class="col-6 text-right">
                            <h5>
                                <a href="{{ page_url('members', 'invoices/details/'~p.invoice_id) }}">
                                    {{ lang('invoice') }} #{{ invoice_number(p) }}
                                </a>
                            </h5>
                        </div>
                    {% endif %}
                </div>

                <div id="account-info" class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card-body">
                                    <h4 class="card-title">{{ lang('account_information') }}</h4>
                                    <address>
                                        {{ p.order_name }}<br/>
                                        {% if p.order_company %}
                                            {{ p.order_company }}<br/>
                                        {% endif %}
                                        {{ p.order_address_1 }}<br/>
                                        {% if p.order_address_2 %}
                                            {{ p.order_address_2 }}<br/>
                                        {% endif %}
                                        {{ p.order_city }} {{ p.order_region_name }} {{ p.order_postal_code }} <br/>
                                        {{ p.order_country_name }}
                                    </address>
                                </div>
                            </div>
                            {% if  p.shipping_address_1 %}
                            <div class="col-md-6">
                                <div class="card-body">
                                    <h4 class="card-title">{{ lang('shipping_address') }}</h4>
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
                <div class="row">
                    <div class="col-md-12">
                        <table id="order-products-table" class="table table-striped">
                            <thead>
                            <tr>
                                <th class="text-md-center" style="width: 15%">{{ lang('product_code') }}</th>
                                <th>{{ lang('description') }}</th>
                                <th class="text-md-center">{{ lang('quantity') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {% if p.items %}
                                {% for s in p.items %}
                                    <tr>
                                        <td class="text-md-center">{{ s.product_sku }}</td>
                                        <td>
                                            {{ s.order_item_name }}
                                            {% if s.attribute_data %}
                                                <ul class="list-unstyled">
                                                    {% for k, a in unserialize(s.attribute_data) %}
                                                        <li>
                                                            <small class="text-muted">  {{ order_attributes(k, a, FALSE) }}</small>
                                                        </li>
                                                    {% endfor %}
                                                </ul>
                                            {% endif %}
                                            {% if s.specification_data %}
                                                <span>  {{ lang('specs') }}</span>
                                                <ul class="list-unstyled">
                                                    {% for k, a in unserialize(s.specification_data) %}
                                                        <li>
                                                            <small class="text-muted">  {{ order_specs(a) }}</small>
                                                        </li>
                                                    {% endfor %}
                                                </ul>
                                            {% endif %}
                                            {% if s.order_item_notes %}
                                                <p>
                                                    <small class="text-muted">{{ format_notes(s.order_product_notes) }}</small>
                                                </p>
                                            {% endif %}
                                        </td>
                                        <td class="text-md-center">{{ s.quantity }}</td>
                                    </tr>
                                {% endfor %}
                            {% endif %}
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col-md-12">
                        <h5>{{ lang('order_notes') }}</h5>
                        <p>{{ format_notes(p.order_notes) }}</p>
                    </div>
                    <div class="col-md-3 text-md-right"></div>
                </div>
                {% if p.osid %}
                    <div id="shipping-tracking" class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>{{ lang('shipping_information') }}</h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ p.carrier }}</strong> {{ p.service }}<br/>
                                            {% if p.tracking_id %}
                                                <strong>{{ lang('tracking') }}</strong> :
                                                {{ p.tracking_id }}
                                            {% endif %}
                                        </div>
                                        <div class="col-md-6">
                                            {{ p.shipping_notes }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endblock content %}
