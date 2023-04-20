{% block content %}
    <h5 class="text-capitalize">
        <span class="pull-right">{{ i('fa fa-lock') }}</span>
        {{ lang('payments_processed_by_2checkout') }}</h5>
    <hr/>
    <div class="row">
        <div class="col-md-12">
            {{ form_open(submit_url, 'role="form" id="payment-form"') }}
            <span class="payment-errors"></span>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('card_no') }}</label>

                <div class="col-md-9">
                    <input type="text" size="20" autocomplete="off" value="" class="form-control required" id="ccNo"/>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('exp_date') }}</label>

                <div class="col-md-4">
                    {{ form_dropdown('', options('cc_months'), '', 'id="expMonth" class="form-control"') }}
                </div>
                <div class="col-md-5">
                    {{ form_dropdown('', options('cc_years'), '', 'id="expYear" class="form-control"') }}
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('cvc') }}</label>

                <div class="col-md-4">
                    <input type="text" size="4" autocomplete="off" value="" class="form-control required" id="cvv"/>
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
        // Called when token created successfully.
        var successCallback = function(data) {
            var myForm = document.getElementById('payment-form');

            // Set the token as the value for the token input
            myForm.token.value = data.response.token.token;

            // IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
            submit_payment_form(myForm);
        };

        // Called when token creation fails.
        var errorCallback = function(data) {
            if (data.errorCode === 200) {tokenRequest();} else {alert(data.errorMsg);}
        };

        var tokenRequest = function() {
            // Setup token request arguments
            var args = {
                sellerId: "{{ module_payment_gateways_2checkout_seller_id }}",
                publishableKey: "{{ module_payment_gateways_2checkout_publishable_key }}",
                ccNo: $("#ccNo").val(),
                cvv: $("#cvv").val(),
                expMonth: $("#expMonth").val(),
                expYear: $("#expYear").val()
            };

            // Make the token request
            TCO.requestToken(successCallback, errorCallback, args);
        };

        $(document).ready(function () {
            // Pull in the public encryption key for our environment
            TCO.loadPubKey('{{ module_payment_gateways_2checkout_environment }}'); //sandbox or production

            $("#payment-form").submit(function(e) {
                // Call our token request function
                tokenRequest();

                // Prevent form from submitting
                return false;
            });
        });
    </script>
    {{ include('js/gateway_payment_ajax.tpl') }}
{% endblock content %}