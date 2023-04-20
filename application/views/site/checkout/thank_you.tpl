{% extends "global/base.tpl" %}
{% block title %}{{ lang('thank_you_for_your_order')|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('thank_you') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('thank_you') }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block javascript_header %}
{{ parent() }}
{{layout_design_meta_thank_you_info}}
{% endblock javascript_header %}
{% block content %}
    <div class="checkout-thankyou">
        <div class="row">
            <div class="col-md-12">
                {{ breadcrumb }}
                <div class="card content">
                    <div class="card-body">
                        <h2>{{ i('fa fa-check') }} {{ lang('thank_you_for_order') }}</h2>
                        <hr/>

                        <h4>{{ lang('order_number') }} #
                            {% if order_data.order.order_number %}
                                {{ order_data.order.order_number }}
                            {% else %}
                                {{ order_data.order_number }}
                            {% endif %}</h4>
                        <br/>
                        <p>{{ lang('thank_you_for_order') }}</p>
                        <p>{{ lang('order_details_sent_via_email') }}</p>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12 text-sm-right">
                                <a href="{{ site_url('members') }}"
                                   class="btn btn-primary">{{ i('fa fa-caret-right') }} {{ lang('continue_to_members_area') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock content %}