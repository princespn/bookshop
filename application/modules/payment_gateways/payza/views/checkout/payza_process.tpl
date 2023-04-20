{% extends "global/base.tpl" %}
{% block title %}{{ lang('thank_you_for_your_order')|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('thank_you') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('thank_you') }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block content %}
    <div class="checkout-thankyou">
        <div class="row">
            <div class="col-md-12">
                {{ breadcrumb }}
                <div class="card content">
                    <div class="card-body">
                        <h2>{{ lang(config_option('module_payment_gateways_payza_title')) }}</h2>
                        <hr/>
                        <div class="jumbotron white text-sm-center">
                            <p id="spin" class="card-text hide">{{ i('fa fa-spinner fa-spin fa-3x fa-fw') }}</p>
                            {{ redirect_link }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#auto-form').submit();
            $('#spin').removeClass('hide');
        });
    </script>
{% endblock content %}