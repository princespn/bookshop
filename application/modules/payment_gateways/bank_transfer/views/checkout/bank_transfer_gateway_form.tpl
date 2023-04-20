{% block content %}
    <h5 class="text-capitalize">{{ lang('bank_wire_transfer') }}</h5>
    <hr/>
    <div class="row">
        <div class="col-md-10 offset-md-1">
            {{ form_open(site_url('checkout/payment/pay'), 'role="form" id="payment-form"') }}
            <p>{{ config_option('module_payment_gateways_bank_transfer_description') }}</p>
            <div class="form-group row">
                <div class="col-md-12">
                    <textarea name="order_notes" class="form-control" rows="3" id="order-notes"
                              placeholder="{{ lang('add_notes_to_order') }}"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-12 text-right">
                    <button type="submit" class="submit-button btn btn-primary">
                        {{ i('fa fa-refresh') }} <span>{{ lang('submit') }}</span>
                    </button>
                </div>
                {{ form_close() }}
            </div>
        </div>
    </div>
    {{ include('js/gateway_payment_ajax.tpl') }}
{% endblock content %}