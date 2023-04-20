{% extends "global/base.tpl" %}
{% block title %}{{ lang('redirecting')|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('redirect') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('redirect') }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block content %}
    <div class="checkout-redirect">
        <div class="row">
            <div class="col-md-12">
                {{ breadcrumb }}
                <div class="card">
                    <div class="card-header">{{ lang('payment_verification') }}</div>
                    <div class="card-body">
                        <div class="jumbotron white text-sm-center">
                            <p id="spin" class="card-text hide">
                                <img src="{{ base_url('images/ajax-loader.gif') }}" />
                            </p>
                            {{ form_open(submit_url, 'id="auto-form"') }}
                            {% for k,v in fields %}
                                <input type="hidden" name="{{ k }}" value="{{ v }}"/>
                            {% endfor %}
                            <h3>{{ lang('please_wait') }}</h3>
                            <button id="proceed" type="submit" class="hide btn btn-secondary">
                                {{ lang('please_click_here_if_you_are_not_forwarded_automatically') }}
                            </button>
                            {{ form_close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#proceed').delay(5000).show(0);
           $('#auto-form').submit();
            $('#spin').removeClass('hide');
        });
    </script>
{% endblock content %}