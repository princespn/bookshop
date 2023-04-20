{% block content %}
    {% if payment_options %}
        <div class="animated fadeIn">
            {% if sess('cart_charge_shipping') %}
                <h5>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="use_different_billing" value="1" id="show-billing">
                            {{ lang('use_different_address_for_billing') }}
                        </label>
                    </div>
                </h5>
                <hr/>
            {% endif %}
            <div id="billing-box">
                {% if sess('user_logged_in') %}
                    {% if member.addresses %}
                        <h5>{{ lang('select_billing_address') }}</h5>
                        <hr/>
                        <div class="row">
                            <div class="col-md-10 offset-md-1">
                                {{ form_select_address(member.addresses, 'billing') }}
                            </div>
                        </div>
                        <hr/>
                    {% endif %}
                {% endif %}
                <div id="billing-fields"
                     class="row  {% if sess('user_logged_in') %} {% if member.addresses %} collapse {% endif %} {% endif %}">
                    <div class="col-md-12">
                        {% if (fields.billing) %}
                            {% for s in fields.billing %}
                                {% if s.field_type != 'hidden' %}
                                    <div class="form-group row">
                                        <label for="{{ s.form_field }}"
                                               class="col-sm-4 col-form-label text-md-right">
                                            {{ s.field_name }}
                                        </label>

                                        <div class="col-sm-7">{{ s.field }}</div>
                                    </div>
                                {% else %}
                                    {{ s.field }}
                                {% endif %}
                            {% endfor %}
                        {% endif %}
                        <hr/>
                    </div>
                </div>
            </div>
            <h5>{{ lang('select_payment_option') }}</h5>
            <hr/>
            <div class="form-group row">
                <div class="col-md-10 offset-md-1">
                    {% if cart.totals.total_with_shipping == 0 %}
                        <div class="radio">
                            <label>
                                <input type="radio" id="option-free" name="select_payment" class="required"
                                       id="payment-option-free" checked value="free"/>
                                {{ lang('free') }} - {{ lang('no_payment_needed') }}
                            </label>
                        </div>
                    {% else %}
                        <div id="options">
                            {% for k,p in payment_options %}
                                <div class="radio">
                                    <label class="cursor">
                                        {% if p.module_id == sess('checkout_payment_option') %}
                                            <input type="radio" name="select_payment" class="required"
                                                   onclick="showDesc('{{ p.module_id }}')"
                                                   id="payment-option-{{ p.module_id }}" value="{{ p.module_id }}"
                                                   checked/>
                                        {% else %}
                                            <input type="radio" name="select_payment" class="required"
                                                   onclick="showDesc('{{ p.module_id }}')"
                                                   id="payment-option-{{ p.module_id }}" {% if k == 1 %} checked
                                                   {% endif %}value="{{ p.module_id }}"/>
                                        {% endif %}
                                        {% if config_option('module_payment_gateways_'~p.module_folder~'_title') %}
                                            {{ config_option('module_payment_gateways_'~p.module_folder~'_title') }}
                                            <div id="option-{{ p.module_id }}" class="option-description">
                                                {% if config_option('module_payment_gateways_'~p.module_folder~'_checkout_logo') %}
                                                    <p>
                                                        <img src="{{ config_option('module_payment_gateways_'~p.module_folder~'_checkout_logo') }}"
                                                             class="payment-option-image img-fluid"/></p>
                                                {% elseif file_exists(config_option('base_physical_path')~'/images/modules/module_payment_gateways_'~p.module_folder~'.png') %}
                                                    <p>
                                                        <img src="{{ base_url('images/modules/module_payment_gateways_'~p.module_folder~'.png') }}"
                                                             class="payment-option-image img-fluid"/></p>
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
                    {% endif %}
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
    {% else %}
        <div class="alert alert-danger" role="alert">
            <h5>{{ i('fa fa-exclamation-circle') }} {{ lang('no_payment_options_found') }}</h5>
            <a href="{{ site_url('contact')}}" class="alert-link" target="_blank">{{ lang('click_here') }}</a>
            {{ lang('to_contact_us_regarding_this_error') }}
        </div>
    {% endif %}
    <script>

        $('.option-description').hide();
        {% if sess('checkout_payment_option') %}
        $('#option-{{ sess('checkout_payment_option') }}').show();
        {% endif %}

        function showDesc(id) {
            $('.option-description').hide(100);
            $('#option-' + id).show(100);
        }

        {% if sess('cart_charge_shipping') %}
        $("#billing-box").hide();
        {% endif %}

        select_country('#billing_country');

        $('#billing_address_id').on('change', function () {
            if (this.value == 0) {
                $('#billing-fields').collapse('show');
                select_country('#billing_country');
            }
            else {
                $('#billing-fields').collapse('hide');
            }
        });

        $("#show-billing").click(function () {
            if ($(this).is(":checked")) {
                $("#billing-box").show(300);
            } else {
                $("#billing-box").hide(200);
            }
        });
    </script>
{% endblock content %}