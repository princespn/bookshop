{% block content %}

<div class="row">
    <div class="col-md-12 mx-auto d-block">
        <p class="text-sm-center">{{ lang('click_the_link_below_to_continue') }}</p>
        <p class="text-sm-center"><a href="{{ submit_url }}">
                {% if config_option('module_payment_gateways_paypal_standard_checkout_logo') %}
                <img src="{{ config_option('module_payment_gateways_paypal_standard_checkout_logo') }}"
                     class="gateway-image"/>
                {% elseif file_exists(config_option('base_physical_path')~'/images/modules/module_payment_gateways_paypal_standard.png') %}
                <img src="{{ base_url('images/modules/module_payment_gateways_paypal_standard.png') }}"
                     class="gateway-image"/>
                {% else %}
                <strong>{{ lang('click_here_to_proceed_with_payment') }}</strong>
                {% endif %}
            </a>
        </p>
    </div>
</div>
{% endblock content %}