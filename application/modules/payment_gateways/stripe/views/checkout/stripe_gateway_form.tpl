{% block content %}
    <h5 class="text-capitalize">
        <span class="pull-right">{{ i('fa fa-lock') }}</span>
        {{ lang('enter_payment_information') }}</h5>
    <hr/>
    <div class="row">
        <div class="col-md-12">

            {% if sess('user_logged_in') %}
                {% if customer_token  %}
                <div class="form-group row">
                    <div class="col-md-12">
                        {{ form_dropdown('status', options('use_saved_billing'), 'saved', 'id="billing-option" class="form-control"') }}
                    </div>
                </div>
                <hr />
                <div id="saved" class="hide">
                    {{ form_open(submit_url, 'role="form" id="saved-form"') }}
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ lang('credit_card') }}</label>
                        <div class="col-md-9">
                            <input type="text" value="{{ customer_token.cc_type }} XXXX-XXXX-XXXX-{{ customer_token.cc_four }}" class="form-control" disabled />
                            <input type="hidden" name="saved" value="1"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">{{ lang('expires') }}</label>
                        <div class="col-md-9">
                            <input type="text" value="{{ customer_token.cc_month }} / {{ customer_token.cc_year }}" class="form-control" disabled />
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
                    </div>
                    {{ form_close() }}
                </div>
            {% endif %}
            {% endif %}
            <div id="enter">
                {{ form_open(submit_url, 'role="form" id="payment-form"') }}
                <span class="payment-errors"></span>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label text-md-right">{{ lang('card_number') }}</label>

                    <div class="col-md-9">
                       <span id="card-number" class="form-control">
                            <!-- Stripe Card Element -->
                        </span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label text-md-right">{{ lang('exp_date') }}</label>

                    <div class="col-md-3">
                       <span id="card-exp" class="form-control">
                            <!-- Stripe Card Expiry Element -->
                        </span>
                    </div>
                    <label class="col-md-3 col-form-label text-md-right">{{ lang('CVC') }}</label>
                    <div class="col-md-3">
                        <span id="card-cvc" class="form-control">
                            <!-- Stripe CVC Element -->
                        </span>
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
                </div>
                <input type="hidden" name="name" value="{{ customer.billing_fname }} {{ customer.billing_lname }}" />
                <input type="hidden" name="email" value="{{ customer.primary_email }}" />
                <input type="hidden" name="address_line1" value="{{ customer.billing_address_1 }}" />
                <input type="hidden" name="address_line2" value="{{ customer.billing_address_2 }}" />
                <input type="hidden" name="address_city" value="{{ customer.billing_city }}" />
                <input type="hidden" name="address_zip" value="{{ customer.billing_postal_code }}" />
                <input type="hidden" name="address_country" value="{{ customer.billing_country_iso_code_3 }}" />
                {{ form_close() }}

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            {% if module_payment_gateways_stripe_enable_testing == 1 %}
            var stripe = Stripe('{{ module_payment_gateways_stripe_api_test_publishable_key }}');
            {% else %}
            var stripe = Stripe('{{ module_payment_gateways_stripe_api_publishable_key }}');
            {% endif %}
            var elements = stripe.elements();
            var style = {
                base: {
                    'lineHeight': '1.35',
                    'fontSize': '1.11rem',
                    'color': '#495057',
                    'fontFamily': 'apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif'
                }
            };

            // Card number
            var card = elements.create('cardNumber', {
                'placeholder': 'XXXX XXXX XXXX XXXX',
                'style': style
            });
            card.mount('#card-number');

            // CVC
            var cvc = elements.create('cardCvc', {
                'placeholder': '123',
                'style': style
            });
            cvc.mount('#card-cvc');

            // Card expiry
            var exp = elements.create('cardExpiry', {
                'placeholder': '{{ current_date('m') }}/{{ current_date('y') }}',
                'style': style
            });
            exp.mount('#card-exp');

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);

                form.appendChild(hiddenInput);

                // Submit the form
                //form.submit();
                submit_payment_form(form);
            }

            function createToken() {
                stripe.createToken(card).then(function(response) {
                    if (response.error) {
                        // re-enable the submit button
                        $('.submit-button').removeAttr("disabled");
                        $('.submit-button i ').removeClass('fa-spin');
                        $('.submit-button span').html('{{ lang('submit') }}');

                        // show the errors on the form
                        $(".payment-errors").html('<div class="alert alert-danger animated shake text-capitalize hover-msg"><button type="button" class="close" data-dismiss="alert">×</button><h5><i class="fa fa-exclamation-triangle "></i>{{ lang('please_check_errors') }}</h5> <div id="msg-details">' + response.error.message + '</div></div>');

                    } else {
                        // Send the token to your server
                        stripeTokenHandler(response.token);
                    }
                });
            };

            // Create a token when the form is submitted.
            $("#payment-form").submit(function (e) {
                // disable the submit button to prevent repeated clicks
                $('.submit-button').attr("disabled", "disabled");
                $('.submit-button i ').addClass('fa-spin');
                $('.submit-button span').html('{{ lang('please_wait') }}');

                e.preventDefault();
                createToken();
            });
        });

        {% if sess('user_logged_in') %}
        {% if customer_token  %}
        $('#saved').removeClass('hide');
        $('#enter').addClass('hide');
        {% endif %}
        {% endif %}

        $('#billing-option').on('change', function () {
            if (this.value == 'saved') {
                $('#saved').removeClass('hide');
                $('#enter').addClass('hide');
            }
            else {
                $('#enter').removeClass('hide');
                $('#saved').addClass('hide');
            }
        });

        /*
        {% if customer_token.saved %}
        $('#saved').removeClass('hide');
        $('#enter').addClass('hide');
        {% endif %}
        */

        $('#saved-form').submit(function (e) {
            submit_payment_form($("#payment-form"));
            return false;
        });
    </script>
    {{ include('js/gateway_payment_ajax.tpl') }}
{% endblock content %}