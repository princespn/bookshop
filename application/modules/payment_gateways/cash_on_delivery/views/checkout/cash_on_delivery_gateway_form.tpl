{% block content %}
    <h5 class="text-capitalize">{{ lang('cash_on_delivery') }}</h5>
    <hr/>
    <div class="row">
        <div class="col-md-10 offset-md-1">
            {{ form_open(site_url('checkout/payment/pay'), 'role="form" id="payment-form"') }}
            <p>{{ config_option('module_payment_gateways_cash_on_delivery_description') }}</p>
        <div class="card-body">
            <h5 class="card-title">{{ i('fa fa-tag') }} {{ 'Payment Voucher.' }}</h5>
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
            <div class="form-group row">
                <div class="col-md-12 text-right">
                    <button type="submit" class="submit-button btn btn-primary">
                        {{ i('fa fa-refresh') }} <span>{{ lang('submit') }}</span>
                    </button>
                </div>
            </div>
            {{ form_close() }}
        </div>
    </div>
    {{ include('js/gateway_payment_ajax.tpl') }}
{% endblock content %}