{% extends "global/base.tpl" %}
{% block title %}{{ lang('order_processing')|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('order_processing') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('order_processing') }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block content %}
    <div class="checkout-cart">
        <div class="row">
            <div class="col-md-12">
                {{ breadcrumb }}
                <div class="card">
                    <div class="card-header">{{ lang('order_processing') }}</div>
                    <div class="card-body text-md-center">
                        <h5>{{ lang('your_order_is_being_processed') }}</h5>
                        <p class="card-text">{{ i('fa fa-spinner fa-spin fa-3x fa-fw') }}</p>
                        <h5 class="card-text">{{ lang('please_wait') }}</h5>
                        <p class="continue"><a href="{{ site_url('thank_you') }}" class="btn btn-primary">{{ lang('if_you_are_not_forwarded_click_here') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock content %}
{% block javascript_footer %}
    parent()
    <script>
        setTimeout(function(){
            $('#continue').fadeIn(500);
        }, 5000);
        $( document ).ready(function() {
            $.ajax({
                url: '{{ site_url('checkout/order/send') }}',
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (response.redirect) {
                        location.href = response.redirect;
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });



    </script>
{% endblock javascript_footer %}



