{% block content %}
    <h5 class="text-capitalize">
        <span class="pull-right">{{ i('fa fa-lock') }}</span>
        {{ lang('enter_payment_information') }}</h5>
    <hr/>
    <div class="row">
        <div class="col-md-12">
            {% if form_data %}
            {% if module_payment_gateways_authorize_net_environment == 'production' %}
                {{ form_open(config_option('module_gateway_production_url'), 'role="form" id="payment-form"') }}
            {% else %}
                {{ form_open(config_option('module_gateway_test_url'), 'role="form" id="payment-form"') }}
            {% endif %}
            {% for k,v in form_data %}
                <input type="hidden" name="{{ k }}" value="{{ v }}"/>
            {% endfor %}
            <span class="payment-errors"></span>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('card_number') }}</label>
                <div class="col-md-9">
                    <input type="text" size="20" autocomplete="off" name="x_card_num"
                           class="cc-number form-control required"/>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('exp_date') }}</label>

                <div class="col-md-4">
                    {{ form_dropdown('', options('cc_months'), '', 'id="exp-month" class="form-control"') }}
                </div>
                <div class="col-md-5">
                    {{ form_dropdown('', options('cc_years', TRUE), '', 'id="exp-year" class="form-control"') }}
                </div>
            </div>
            {% if config_enabled('module_payment_gateways_authorize_net_enable_cvv') %}
            <div class="form-group row">
                <label class="col-md-3 form-control-label text-right">{{ lang('cvc') }}</label>
                <div class="col-md-4">
                    <input type="text" size="4" name="x_card_code﻿" autocomplete="off" placeholder="123"
                           class="cc-number form-control required"/>
                </div>
            </div>
            {% endif %}
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
                {% else %}
                    <div class="alert alert-danger">{{ lang('invalid_gateway_configuration') }}</div>
                {% endif %}
            </div>
        </div>
    </div>
    <script>

        $("#payment-form").validate({
            rules: {
                x_card_num: {
                    required: true,
                },
                x_exp_date: {
                    required: true,
                    minlength: 4
                }
            },
            submitHandler: function (form) { // <- pass 'form' argument in
                var form$ = $("#payment-form");

                $('.submit-button i ').addClass('fa-spin');
                $('.submit-button').addClass('disabled');
                $('.submit-button span').html('{{ lang('processing') }}');

                var exp_date =  $('#exp-month').val() +  $('#exp-year').val();
                form$.append("<input type='hidden' name='x_exp_date' value='" + exp_date + "' />");
                form.submit();
                //submit_payment_form(form);
            }
        });

        function submit_payment_form(form)
        {
            $.ajax({
                url: '{% if module_payment_gateways_authorize_net_environment == 'production' %}{{ config_option('module_gateway_production_url')}}{% else %}{{ config_option('module_gateway_test_url') }}{% endif %}',
                type: 'post',
                data: $('#payment-form').serialize(),
                dataType: 'json',
                beforeSend: function () {
                    $('.submit-button i ').addClass('fa-spin');
                    $('.submit-button').addClass('disabled');
                    $('.submit-button span').html('{{ lang('processing') }}');
                },
                complete: function () {
                    // re-enable the submit button
                    $('.submit-button').removeAttr('disabled');
                    $('.submit-button').removeClass('disabled');
                    $('.submit-button i ').removeClass('fa-spin');
                    $('.submit-button span').html('{{ lang('submit') }}');
                },
                success: function (data) {
                    if (data['type'] == 'error') {
                        $('#response').html('{{ alert('error') }}');
                        $('#msg-details').html(data['msg']);
                    }
                    else if (data['type'] == 'success') {
                        if (data['redirect']) {
                            location.href = data['redirect'];
                        }
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }
    </script>
{% endblock content %}