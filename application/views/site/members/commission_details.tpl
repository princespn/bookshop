{% extends "global/base.tpl" %}
{% block title %}{{ lang('commission_id')|capitalize }} {{ p.comm_id }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('commission_details') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="commission-details" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ lang('commission_details') }}
                            <span class="badge badge-info badge-{{ p.comm_status }} float-right">{{ lang(p.comm_status) }}</span>
                        </h5>
                        <hr/>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>{{ lang('date') }}</strong> {{ display_date(p.date) }}
                                {% if (p.comm_status == 'paid') %}
                                    <br/> <strong>{{ lang('date_paid') }}</strong> {{ display_date(p.date_paid) }}
                                {% endif %}
                            </div>
                            <div class="col-md-4">
                                <strong>{{ lang('commission_amount') }}</strong> {{ format_amount(p.commission_amount) }}
                                {% if p.fee > 0 %}
                                    <br/><strong>{{ lang('fee') }}</strong> {{ format_amount(p.fee, FALSE) }}
                                {% endif %}
                                {% if p.commission_level > 1 %}
                                    <br/><strong>{{ lang('commission_level') }}</strong> {{ p.commission_level }}
                                {% endif %}
                            </div>
                            <div class="col-md-4">
                                <strong>{{ lang('transaction_id') }}</strong><br/>{{ p.trans_id }}
                            </div>
                        </div>
                        <br/>
                        {% if config_enabled('sts_affiliate_show_customer_commission_details') %}
                            <h5>{{ lang('customer_information') }}</h5>
                            <hr/>
                            <div class="row">
                                <div class="col-md-6">
                                    <div><strong>{{ lang('address') }}</strong></div>
                                    <div>{{ p.customer_name }}</div>
                                    <div>{{ p.customer_address_1 }} {{ p.customer_address_2 }}</div>
                                    <div>{{ p.customer_city }} {{ p.customer_region_name }} {{ p.customer_postal_code }}</div>
                                    <div>{{ p.customer_country_name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div><strong>{{ lang('contact') }}</strong></div>
                                    <div>{{ p.customer_telephone }}</div>
                                    <div>{{ p.customer_primary_email }}</div>
                                </div>
                            </div>
                        {% endif %}
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock content %}