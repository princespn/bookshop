{% extends "global/base.tpl" %}
{% block title %}{{ lang('invoice')|capitalize }} {{ invoice_number(p) }}{% endblock %}
{% block javascript_header %}
    {{ parent() }}
    {% if checkout_js %}
        {% for j in checkout_js %}
            <script src="{{ j }}"></script>
        {% endfor %}
    {% endif %}
    {% if checkout_css %}
        {% for c in checkout_css %}
            <link href="{{ c }}" rel="stylesheet" type="text/css"/>
        {% endfor %}
    {% endif %}
{% endblock javascript_header %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('invoice') }} #{{ invoice_number(p) }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    {{ breadcrumb }}
    <div class="row">
        <div class="col-md-6">
            <h2>
                {% if p.order_number %}
                    <a href="{{ page_url('members', 'orders/details/'~p.order_id) }}">
                        {{ lang('order') }} #{{ p.order_number }}
                    </a>
                {% else %}
                    {{ lang('invoice_details') }}
                {% endif %}
            </h2>
        </div>
        <div class="col-md-6 text-md-right d-none d-md-block">
            <h2>
                    <span class="badge badge-info"
                          style="background-color: {{ p.color }}">{{ p.payment_status }}</span> </span>
            </h2>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-7">
            <div id="invoice-details" class="card">
                <div class="card-body">
                    <div class="row">
                        <div id="date-purchased" class="col-md-6">
                            <h5>{{ lang('invoice_date') }}: {{ display_date(p.date_purchased) }}</h5>
                        </div>
                        <div id="due-date" class="col-md-6 text-md-right">
                            <h5>{{ lang('due_date') }}: {{ display_date(p.due_date) }}</h5>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div id="account-info" class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ lang('account_information') }}</h5>
                                        <address>
                                            {{ p.customer_name }}<br/>
                                            {% if p.customer_company %}
                                                {{ p.customer_company }}<br/>
                                            {% endif %}
                                            {{ p.customer_address_1 }}<br/>
                                            {% if p.customer_address_2 %}
                                                {{ p.customer_address_2 }}<br/>
                                            {% endif %}
                                            {{ p.customer_city }} {{ p.customer_region_name }} {{ p.customer_postal_code }}
                                            <br/>
                                            {{ p.customer_country_name }}
                                        </address>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card-body">
                                        {% if  p.shipping_address_1 %}
                                            <h5 class="card-title">{{ lang('shipping_information') }}</h5>
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
                                        {% endif %}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div id="invoice-products" class="row">
                        <div class="col-md-12">
                            <table id="invoice-products-table" class="table table-striped">
                                <thead>
                                <tr>
                                    <th
                                            class="text-md-center">{{ lang('sku') }}</th>
                                    <th>{{ lang('product_name') }}</th>
                                    <th
                                            class="text-md-center">{{ lang('unit_price') }}</th>
                                    <th
                                            class="text-md-center">{{ lang('quantity') }}</th>
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
                                        </tr>
                                    {% endfor %}
                                {% endif %}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div id="notes" class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">{{ i('fa fa-pencil') }} {{ lang('invoice_notes') }}</h4>

                                    <p>{{ format_notes(p.invoice_notes) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="totals" class="col-md-8 offset-md-4">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    {% if payment_options %}
                        <div id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="card">
                                <div class="card-header" role="tab" id="heading-one">
                                    <h4>
                                        <span id="step-one-heading">{{ lang('payment_options') }}</span>
                                    </h4>
                                </div>
                                <div id="step-one" class="card-body collapse show" role="tabpanel"
                                     aria-labelledby="heading-one">
                                    {{ form_open(ssl_url('checkout/invoice/select_payment'), 'id="step-one-form"') }}
                                    <div class="animated fadeIn">
                                        <div class="form-group row">
                                            <div class="col-md-10 offset-md-1">
                                                <div id="options">
                                                    {% for k,p in payment_options %}
                                                        <div class="radio">
                                                            <label class="cursor">
                                                                {% if p.module_id == sess('checkout_payment_option') %}
                                                                    <input type="radio" name="select_payment"
                                                                           class="required"
                                                                           onclick="showDesc('{{ p.module_id }}')"
                                                                           id="payment-option-{{ p.module_id }}"
                                                                           value="{{ p.module_id }}"
                                                                           checked/>
                                                                {% else %}
                                                                    <input type="radio" name="select_payment"
                                                                           class="required"
                                                                           onclick="showDesc('{{ p.module_id }}')"
                                                                           id="payment-option-{{ p.module_id }}" {% if k == 1 %}
                                                                           checked
                                                                           {% endif %}value="{{ p.module_id }}"/>
                                                                {% endif %}
                                                                {% if config_option('module_payment_gateways_'~p.module_folder~'_title') %}
                                                                    {{ config_option('module_payment_gateways_'~p.module_folder~'_title') }}
                                                                    <div id="option-{{ p.module_id }}"
                                                                         class="option-description">
                                                                        {% if file_exists(config_option('base_physical_path')~'/images/modules/module_payment_gateways_'~p.module_folder~'.png') %}
                                                                            <p>
                                                                                <img src="{{ base_url('images/modules/module_payment_gateways_'~p.module_folder~'.png') }}"
                                                                                     class="payment-option-image img-fluid"/>
                                                                            </p>
                                                                        {% endif %}
                                                                        <p>
                                                                            <small>{{ config_option('module_payment_gateways_'~p.module_folder~'_description') }}</small>
                                                                        </p>
                                                                    </div>
                                                                {% else %}
                                                                    {{ lang(p.module_description) }}
                                                                {% endif %}
                                                            </label>
                                                        </div>
                                                    {% endfor %}
                                                </div>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="row">
                                            <div class="col-md-11 text-right">
                                                <button type="submit" class="btn btn-primary">
                                                    {{ lang('continue') }} {{ i('fa fa-caret-right') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{ form_close() }}
                                </div>
                            </div>

                            <div class="card" id="payment-box">
                                <div class="card-header" role="tab" id="heading-two">
                                    <h4>
                                        <span id="step-two-heading">{{ lang('payment_details') }}</span>
                                    </h4>
                                </div>
                                <div id="step-two" class="card-body collapse" role="tabpanel"
                                     aria-labelledby="heading-two">
                                    {{ form_open(ssl_url('checkout/invoice/pay'), 'id="step-two-form"') }}
                                    <div id="payment-confirmation-box"></div>
                                    {{ form_close() }}
                                </div>
                            </div>
                        </div>

                    {% else %}
                        <div class="alert alert-danger" role="alert">
                            <h5>{{ i('fa fa-exclamation-circle') }} {{ lang('no_payment_options_found') }}</h5>
                            <a href="{{ site_url('contact') }}" class="alert-link" target="_blank">{{ lang('click_here') }}</a>
                            {{ lang('to_contact_us_regarding_this_error') }}
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>

{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    <script>

        $('.option-description').hide();
        {% if sess('checkout_payment_option') %}
        $('#option-' + {{ sess('checkout_payment_option') }}).show();
        {% endif %}

        function showDesc(id) {
            $('.option-description').hide(100);
            $('#option-' + id).show(100);
        }


        $("#step-one-form").validate({
            errorContainer: $("#error-alert"),
            submitHandler: function (form) {
                $.ajax({
                    url: '{{ ssl_url('checkout/invoice/select_payment') }}',
                    type: 'post',
                    data: $('#step-one-form').serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        $('#payment-confirmation-box').html('<div class="alert alert-secondary">{{ lang('please_wait') }}...</div>');
                    },
                    success: function (data) {
                        if (data['type'] == 'error') {
                            $('#response').html('{{ alert('error') }}');
                        }
                        else if (data['type'] == 'success') {

                            $('#payment-confirmation-box').load('{{ ssl_url('checkout/invoice/gateway_form/') }}' + data['module_id']);

                            //activate tabs
                            $('#step-two').collapse('show');
                        }

                        $('#msg-details').html(data['msg']);
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        });

    </script>
{% endblock javascript_footer %}