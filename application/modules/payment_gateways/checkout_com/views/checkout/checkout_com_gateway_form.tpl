{% block content %}
    <h5 class="text-capitalize">
        <span class="pull-right">{{ i('fa fa-lock') }}</span>
        {{ lang('enter_payment_information') }}</h5>
    <hr/>
    <div id="checkout-com-form" class="row">
        <div class="col-md-12">
            {% if sess('user_logged_in') %}
            {% if customer_token  %}
            <div class="form-group row">
                <div class="col-md-12">
                    {{ form_dropdown('status', options('use_saved_billing'), 'new', 'id="billing-option" class="form-control"') }}
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
                    {{ form_open('submit_url', 'role="form" id="payment-form"') }}
                    <label for="card-number">{{ lang('credit_card_number') }}</label>
                    <div class="input-container card-number">
                        <div class="icon-container">
                            <img id="icon-card-number" src="{{base_folder_path}}/images/card-icons/card.svg" alt="PAN" />
                        </div>
                        <div class="card-number-frame"></div>
                        <div class="icon-container payment-method">
                            <img id="logo-payment-method" />
                        </div>
                        <div class="icon-container">
                            <img id="icon-card-number-error" src="{{base_folder_path}}/images/card-icons/error.svg" />
                        </div>
                    </div>

                    <div class="date-and-code">
                        <div>
                            <label for="expiry-date">{{ lang('expiry_date') }}</label>
                            <div class="input-container expiry-date">
                                <div class="icon-container">
                                    <img id="icon-expiry-date" src="{{base_folder_path}}/images/card-icons/exp-date.svg" alt="Expiry date" />
                                </div>
                                <div class="expiry-date-frame"></div>
                                <div class="icon-container">
                                    <img id="icon-expiry-date-error" src="{{base_folder_path}}/images/card-icons/error.svg" />
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="cvv">{{ lang('security_code') }}</label>
                            <div class="input-container cvv">
                                <div class="icon-container">
                                    <img id="icon-cvv" src="{{base_folder_path}}/images/card-icons/cvv.svg" alt="CVV" />
                                </div>
                                <div class="cvv-frame"></div>
                                <div class="icon-container">
                                    <img id="icon-cvv-error" src="{{base_folder_path}}/images/card-icons/error.svg"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button id="pay-button" class="submit-button btn btn-primary btn-block" disabled="">
                        {{ lang('submit') }}
                    </button>

                    <div>
                        <span class="error-message error-message__card-number"></span>
                        <span class="error-message error-message__expiry-date"></span>
                        <span class="error-message error-message__cvv"></span>
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
    var payButton = document.getElementById("pay-button");
    var form = document.getElementById("payment-form");

    {% if module_payment_gateways_checkout_com_enable_testing == 1 %}
    Frames.init({
        publicKey: "{{module_payment_gateways_checkout_com_api_test_public_key}}",
        style: {
            base: {
                color: "{{layout_design_theme_text_color}}",
                fontSize: "18px"
            },
            focus: {
                color: "{{layout_design_theme_text_color}}"
            },
            valid: {
                color: "{{layout_design_theme_success_button}}"
            },
            invalid: {
                color: "{{layout_design_theme_danger_button}}"
            },
            placeholder: {
                base: {
                    color: "{{layout_design_theme_text_color}}"
                },
                focus: {
                    border: "solid 1px {{ layout_design_theme_primary_button }}"
                }
            }
        }
    })
    {% else %}
    Frames.init("{{module_payment_gateways_checkout_com_api_public_key}}");
    {% endif %}



    var logos = generateLogos();
    function generateLogos() {
        var logos = {};
        logos["card-number"] = {
            src: "card",
            alt: "card number logo"
        };
        logos["expiry-date"] = {
            src: "exp-date",
            alt: "expiry date logo"
        };
        logos["cvv"] = {
            src: "cvv",
            alt: "cvv logo"
        };
        return logos;
    }

    var errors = {};
    errors["card-number"] = "{{ lang('please_enter_valid_credit_card') }}";
    errors["expiry-date"] = "{{ lang('please_enter_valid_expiry_date') }}";
    errors["cvv"] = "{{ lang('please_enter_valid_security_code') }}";

    Frames.addEventHandler(
        Frames.Events.FRAME_VALIDATION_CHANGED,
        onValidationChanged
    );
    function onValidationChanged(event) {
        var e = event.element;

        if (event.isValid || event.isEmpty) {
            if (e == "card-number" && !event.isEmpty) {
                showPaymentMethodIcon();
            }
            setDefaultIcon(e);
            clearErrorIcon(e);
            clearErrorMessage(e);
        } else {
            if (e == "card-number") {
                clearPaymentMethodIcon();
            }
            setDefaultErrorIcon(e);
            setErrorIcon(e);
            setErrorMessage(e);
        }
    }

    function clearErrorMessage(el) {
        var selector = ".error-message__" + el;
        var message = document.querySelector(selector);
        message.textContent = "";
    }

    function clearErrorIcon(el) {
        var logo = document.getElementById("icon-" + el + "-error");
        logo.style.removeProperty("display");
    }

    function showPaymentMethodIcon(parent, pm) {
        if (parent) parent.classList.add("show");

        var logo = document.getElementById("logo-payment-method");
        if (pm) {
            var name = pm.toLowerCase();
            logo.setAttribute("src", "{{base_folder_path}}/images/card-icons/" + name + ".svg");
            logo.setAttribute("alt", pm || "payment method");
        }
        logo.style.removeProperty("display");
    }

    function clearPaymentMethodIcon(parent) {
        if (parent) parent.classList.remove("show");

        var logo = document.getElementById("logo-payment-method");
        logo.style.setProperty("display", "none");
    }

    function setErrorMessage(el) {
        var selector = ".error-message__" + el;
        var message = document.querySelector(selector);
        message.textContent = errors[el];
    }

    function setDefaultIcon(el) {
        var selector = "icon-" + el;
        var logo = document.getElementById(selector);
        logo.setAttribute("src", "{{base_folder_path}}/images/card-icons/" + logos[el].src + ".svg");
        logo.setAttribute("alt", logos[el].alt);
    }

    function setDefaultErrorIcon(el) {
        var selector = "icon-" + el;
        var logo = document.getElementById(selector);
        logo.setAttribute("src", "{{base_folder_path}}/images/card-icons/" + logos[el].src + "-error.svg");
        logo.setAttribute("alt", logos[el].alt);
    }

    function setErrorIcon(el) {
        var logo = document.getElementById("icon-" + el + "-error");
        logo.style.setProperty("display", "block");
    }

    Frames.addEventHandler(
        Frames.Events.CARD_VALIDATION_CHANGED,
        cardValidationChanged
    );
    function cardValidationChanged(event) {
        payButton.disabled = !Frames.isCardValid();
    }

    Frames.addEventHandler(Frames.Events.CARD_TOKENIZED, onCardTokenized);
    function onCardTokenized(event) {

        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'card_token');
        hiddenInput.setAttribute('value', event.token);

        form.appendChild(hiddenInput);

        // Submit the form
        //form.submit();
        submit_payment_form($("#payment-form"));
        $('#payment-confirmation-box').load('{{ ssl_url('checkout/payment/gateway_form/') }}');

        //activate tabs
        $("#step-three-heading").html('<a data-toggle="collapse" data-parent="#accordion" href="#step-three" aria-expanded="true" aria-controls="step-three">{{ lang('payment_information') }} </a> {{ i('fa fa-caret-down') }}');
        $('#step-three').collapse('show');
    }

    Frames.addEventHandler(
        Frames.Events.PAYMENT_METHOD_CHANGED,
        paymentMethodChanged
    );

    function paymentMethodChanged(event) {
        var pm = event.paymentMethod;
        let container = document.querySelector(".icon-container.payment-method");

        if (!pm) {
            clearPaymentMethodIcon(container);
        } else {
            clearErrorIcon("card-number");
            showPaymentMethodIcon(container, pm);
        }
    }

    form.addEventListener("submit", onSubmit);
    function onSubmit(event) {
        event.preventDefault();

        Frames.cardholder = {
            name: "{{ customer.billing_fname }} {{ customer.billing_lname }}"
        };

        Frames.submitCard();
    }

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

    $('#saved-form').submit(function (e) {
        submit_payment_form($("#payment-form"));
        return false;
    });

</script>
{{ include('js/gateway_payment_ajax.tpl') }}
{% endblock content %}