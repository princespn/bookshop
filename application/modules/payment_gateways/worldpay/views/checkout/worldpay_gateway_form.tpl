{% block content %}
    <h5 class="text-capitalize">
        <span class="pull-right">{{ i('fa fa-lock') }}</span>
        {{ lang('payments_processed_by_worldpay') }}</h5>
    <hr/>
    <div class="row">
        <div class="col-md-12">
            {{ form_open(submit_url, 'role="form" id="payment-form"') }}
            <span id="payment-errors"></span>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('name_on_card') }}</label>

                <div class="col-md-9">
                    <input type="text" autocomplete="off" value="{{ customer.billing_fname }} {{ customer.billing_lname }}" name="name" class="form-control required" data-worldpay="name"/>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('card_number') }}</label>

                <div class="col-md-9">
                    <input type="text" size="20" autocomplete="off" value="" class="form-control required" data-worldpay="number"/>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('exp_date') }}</label>

                <div class="col-md-4">
                    {{ form_dropdown('', options('cc_months'), '', 'data-worldpay="exp-month" class="form-control"') }}
                </div>
                <div class="col-md-5">
                    {{ form_dropdown('', options('cc_years'), '', 'data-worldpay="exp-year" class="form-control"') }}
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('CVC') }}</label>

                <div class="col-md-4">
                    <input type="text" size="4" autocomplete="off" value="" class="form-control required" data-worldpay="cvc"/>
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
                <input id="token" name="token" type="hidden" value="">
                {{ form_close() }}
            </div>
        </div>
    </div>

    <script>
        var form = document.getElementById('payment-form');

        Worldpay.useOwnForm({
            'clientKey': '{{ module_payment_gateways_worldpay_client_key }}',
            'form': form,
            'reusable': {% if module_payment_gateways_worldpay_save_customer_token == 1 %}true{% else %}false{% endif %},
            'callback': function(status, response) {
                document.getElementById('payment-errors').innerHTML = '';
                if (response.error) {
                    if (response.error.message) {
                        $("#payment-errors").html('<div class="alert alert-danger animated shake text-capitalize hover-msg"><button type="button" class="close" data-dismiss="alert">×</button><h5><i class="fa fa-exclamation-triangle "></i>{{ lang('please_check_errors') }}</h5> <div id="msg-details">' + response.error.message + '</div></div>');
                    }
                   //Worldpay.handleError(form, document.getElementById('payment-errors'), response.error);
                } else {
                    var token = response.token;
                    Worldpay.formBuilder(form, 'input', 'hidden', 'token', token);
                    //form.submit();
                    submit_payment_form(form);
                }
            }
        });
    </script>

    {{ include('js/gateway_payment_ajax.tpl') }}
{% endblock content %}