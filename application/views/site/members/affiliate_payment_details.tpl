{% extends "global/base.tpl" %}
{% block title %}{{ lang('affiliate_payment_id')|capitalize }} {{ p.id }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('affiliate_payments') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="affiliate-payment-details" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ lang('affiliate_payment_id') }} {{ p.id }}
                        </h3>
                        <hr/>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>{{ lang('date') }}: </strong> {{ display_date(p.payment_date) }}
                            </div>
                            <div class="col-md-4">
                                <strong>{{ lang('payment_amount') }}
                                    :</strong> {{ format_amount(p.payment_amount) }}
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text">
                                            <strong>{{ lang('payment_details') }}</strong><br/>
                                            {{ p.payment_details }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock content %}