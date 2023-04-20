{% extends "global/base.tpl" %}
{% block title %}{{ lang('subscription_id')|capitalize }} {{ p.sub_id }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('subscription_details') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="subscription-details" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ lang('subscription_details') }}
                            {% if config_enabled('sts_products_enable_subscription_cancellations') %}
                                {% if p.status == 1 %}
                                    <a data-href="{{ page_url('members', 'subscriptions/cancel/'~p.sub_id) }}"
                                       data-toggle="modal" data-target="#confirm-delete" href="#"
                                       class="btn btn-sm btn-warning float-right">{{ lang('cancel') }}</a>
                                    </a>
                                {% else %}
                                    <span class="btn btn-sm btn-danger float-right">{{ lang('cancelled') }}</span>
                                {% endif %}
                            {% endif %}
                        </h5>
                        <hr/>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>{{ lang('start_date') }}</strong> {{ display_date(p.start_date) }}<br/>
                                <strong>{{ lang('next_due_date') }}</strong>
                                {% if p.status == 1 %}
                                    {{ display_date(p.next_due_date) }}
                                {% else %}
                                    <span class="badge badge-danger">{{ lang('cancelled') }}</span>
                                {% endif %}
                            </div>
                            <div class="col-md-4">
                                {% if p.order_id %}
                                    <strong>{{ lang('original_order') }}</strong>
                                    <a href="{{ page_url('members', 'orders/details/'~p.order_id) }}">#{{ p.order_number }}</a>
                                {% endif %}
                                {% if p.invoice_id %}
                                    <br/><strong>{{ lang('latest_invoice') }}</strong>
                                    <a href="{{ page_url('members', 'invoices/details/'~p.invoice_id) }}">#{{ p.invoice_number }}</a>
                                {% endif %}
                            </div>
                            <div class="col-md-4">
                                <strong>{{ lang('subscription_amount') }}</strong> {{ format_amount(p.product_price, FALSE) }}
                                <br/>
                                <strong>{{ lang('interval') }}</strong>
                                {{ check_plural(p.interval_amount, p.interval_type) }}
                            </div>
                        </div>
                        <br/>
                        <h5>{{ lang('product_details') }} - {{ p.product_name }}</h5>
                        <hr/>
                        <div id="id-{{ p.sub_id }}" class="attribute">
                            {% if p.attribute_data %}
                                {{ format_attribute_data(p) }}
                            {% endif %}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock content %}
 {% block confirm_delete %}
     <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-body capitalize">
                     <h3><i class="fa fa-trash-o"></i> {{ lang('confirm_cancellation') }}</h3>
                     {{ lang('are_you_sure_you_want_to_cancel') }}
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary"
                             data-dismiss="modal">{{ lang('cancel') }}</button>
                     <a href="#" class="btn btn-danger danger">{{ lang('proceed') }}</a>
                 </div>
             </div>
         </div>
     </div>
 {% endblock confirm_delete %}