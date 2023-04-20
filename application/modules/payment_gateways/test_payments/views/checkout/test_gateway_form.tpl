{% block content %}
    <h5 class="text-capitalize"><span class="text-danger">{{ lang('test_payments_only') }}</span></h5>
    <hr/>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            {{ form_open(submit_url, 'role="form" id="payment-form"') }}
            <span class="payment-errors"></span>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('card_number') }}</label>

                <div class="col-md-9">
                    <input type="text" size="20" autocomplete="off" class="form-control required" readonly
                           value="4111111111111111" id="card-number"/>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('exp_date') }}</label>

                <div class="col-md-4">
                    {{ form_dropdown('', options('cc_months'), '12', 'id="card-expiry-month" readonly class="form-control"') }}
                </div>
                <div class="col-md-5">
                    {{ form_dropdown('', options('cc_years'), '2035', 'id="card-expiry-year" readonly class="form-control"') }}
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('cvc') }}</label>

                <div class="col-md-4">
                    <input type="text" size="4" autocomplete="off" class="form-control required" readonly value="111"
                           id="card-cvc"/>
                </div>
            </div>
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