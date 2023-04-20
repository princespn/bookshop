{% block content %}
    <h5 class="text-capitalize">
        <span class="pull-right">{{ i('fa fa-lock') }}</span>
        {{ lang('enter_payment_information') }}</h5>
    <hr/>
    <div class="row">
        <div class="col-md-12">
            {{ form_open(submit_url, 'role="form" id="payment-form"') }}
            <span class="payment-errors"></span>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('name_on_card') }}</label>
                <div class="col-md-9">
                    <input type="text" autocomplete="off"
                           value="{{ customer.billing_fname }} {{ customer.billing_lname }}"
                           class="form-control required" payeezy-data="cardholder_name"/>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('card_number') }}</label>
                <div class="col-md-3">
                    {{ form_dropdown('', options('cc_type'), '', 'class="form-control"  payeezy-data="card_type"') }}
                </div>
                <div class="col-md-6">
                    <input type="text" size="20" autocomplete="off" class="form-control required"
                           payeezy-data="cc_number" value=""/>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('exp_date') }}</label>

                <div class="col-md-4">
                    {{ form_dropdown('', options('cc_months'), date('m'), 'payeezy-data="exp_month" class="form-control"') }}
                </div>
                <div class="col-md-5">
                    {{ form_dropdown('', options('cc_years', TRUE), date('Y'), 'payeezy-data="exp_year" class="form-control"') }}
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('cvc') }}</label>

                <div class="col-md-4">
                    <input type="text" size="4" autocomplete="off" class="form-control required"
                           payeezy-data="cvv_code" value=""/>
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
                {% for k,v in form_data %}
                    <input type="hidden" payeezy-data="{{ k }}" value="{{ v }}"/>
                {% endfor %}
                <input type="hidden" name="token" value="{{ module_payment_gateways_payeezy_js_merchant_token }}"/>
                {{ form_close() }}
            </div>
        </div>
    </div>

    <script>
        var responseHandler = function (status, response) {
            var $form = $('#payment-form');
            $('.payment-errors').html('');
            console.log(response);
            if (status != 201) {
                if (response.Error && status != 400) {

                    var error = response.Error;
                    var errormsg = error.messages;
                    var errorcode = JSON.stringify(errormsg[0].code, null, 4);
                    var msg = JSON.stringify(errormsg[0].description, null, 4);
                    var errorMessages = errorcode + ': ' + msg;
                }
                if (status == 400 || status == 500) {
                    $('#payment-errors').html('');
                    var errormsg = response.Error.messages;
                    var errorMessages = "";
                    for(var i in errormsg){
                        var ecode = errormsg[i].code;
                        var eMessage = errormsg[i].description;
                    }

                    var errorMessages = errorMessages + ' ' + ecode + ': ' + eMessage;
                }

                $(".payment-errors").html('<div class="alert alert-danger animated shake text-capitalize hover-msg"><button type="button" class="close" data-dismiss="alert">×</button><h5><i class="fa fa-exclamation-triangle "></i>{{ lang('please_check_errors') }}</h5> <div id="msg-details">' + errorMessages + '</div></div>');

                $('.submit-button').removeAttr("disabled");
                $('.submit-button i ').removeClass('fa-spin');
                $('.submit-button span').html('{{ lang('submit') }}');

            } else {
                $('.payment-errors').html('');
                var form$ = $("#payment-form");
                var token = response.token;

                // insert the token into the form so it gets submitted to the server
                form$.append("<input type='hidden' name='cardholder_name' value='" + token.cardholder_name + "' />");
                form$.append("<input type='hidden' name='exp_date' value='" + token.exp_date + "' />");
                form$.append("<input type='hidden' name='type' value='" + token.type + "' />");
                form$.append("<input type='hidden' name='token' value='" + token.value + "' />");
                // and submit
                submit_payment_form(form$);
            }


        };

        <!-- Building JSON resquest and submitting request to Payeezy sever -->
        jQuery(function ($) {
            $('#payment-form').submit(function (e) {
                $('.payment-errors').html('');

                $('.submit-button').attr("disabled", "disabled");
                $('.submit-button i ').addClass('fa-spin');
                $('.submit-button span').html('{{ lang('please_wait') }}');

                Payeezy.setApiKey('{{ module_payment_gateways_payeezy_js_api_key }}');
                Payeezy.setJs_Security_Key('{{ module_payment_gateways_payeezy_js_js_security_key }}');
                Payeezy.setTa_token('{{ module_payment_gateways_payeezy_js_ta_token }}');
                {% if module_payment_gateways_payeezy_js_auth_only == 1 %}
                Payeezy.setAuth('true');
                {% else %}
                Payeezy.setAuth('false');
                {% endif %}
                Payeezy.createToken(responseHandler);

                return false;
            });
        });
    </script>
    {{ include('js/gateway_payment_ajax.tpl') }}
{% endblock content %}