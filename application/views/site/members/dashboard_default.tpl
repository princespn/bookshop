{% extends "global/base.tpl" %}
{% block title %}{{ lang('members_dashboard') }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('members') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="dashboard" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-8">
                {% if config_enabled('affiliate_marketing') %}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-3">
                                {% if sess('is_affiliate') == 1 %}
                                    <div class="card-body">
                                        <h4 class="card-title">{{ lang('your_referral_link') }}</h4>
                                        <hr />
                                        <h5 class="text-center">
                                            <a href="{{ affiliate_url() }}" target="_blank">{{ affiliate_url() }}</a>
                                        </h5>
                                    </div>
                                {% else %}
                                    <div class="card-header">{{ lang('activate_your_account') }}</div>
                                    <div class="card-body">
                                        <a href="{{ site_url('members/dashboard/activate_affiliate') }}"
                                           class="btn btn-secondary">
                                            {{ i('fa fa-angle-double-right') }}
                                            {{ lang('click_here_to_activate_affiliate_account') }}</a>
                                    </div>
                                {% endif %}
                            </div>
                        </div>
                    </div>
                {% endif %}
                <div id="dashboard-icons" class="row">
                    {% if icons %}
                        {% for p in icons %}
                            <div class="col-md-4 col-6">
                                <a href="{{ p.url }}">
                                    <div class="card mb-3 mx-auto icons">
                                        <div class="card-body text-center text-dark">
                                            <h5 class="card-title">{{ lang(p.title) }}</h5>
                                            {{ i('fa fa-5x '~p.icon) }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        {% endfor %}
                    {% endif %}
                </div>
            </div>
            <div class="col-md-4">
                {% if config_enabled('sts_support_enable') %}
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ i('fa fa-support') }} {{ lang('help_and_support') }}</h5>
                                <hr />
                                {% if config_option('sts_support_url_redirect') %}
                                <a href="{{ config_option('sts_support_url_redirect') }}"
                                   class="btn btn-lg btn-primary btn-block" target="_blank">
                                    {{ i('fa fa-support') }} {{ lang('click_for_support') }}</a>
                                {% else %}
                                {% if tickets %}
                                    {% for p in tickets %}
                                    <div>
                                          <span class="badge badge-light">{{ display_date(p.date_added) }}</span>
                                          <span class="badge badge-info badge-ticket-status-{{ p.ticket_status }}">{{ lang(p.ticket_status) }}</span>
                                          <br />
                                          <a href="{{ site_url('members/support/ticket/'~p.ticket_id) }}"><small>
                                                  {{ p.ticket_subject }}
                                              </small>
                                          </a>
                                    </div>
                                <hr />
                                    {% endfor %}
                                {% endif %}
                                <a href="{{ site_url('members/support/create') }}"
                                   class="btn btn-lg btn-primary btn-block">
                                    {{ lang('submit_ticket') }}</a>
                                {% endif %}
                            </div>
                        </div>
                {% endif %}
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ i('fa fa-file-o') }} {{ lang('recent_invoices') }}</h5>
                        <hr />
                        {% if invoices %}
                        {% for p in invoices %}
                        <a href="{{ site_url('members/invoices/details/'~p.invoice_id) }}">
                            <span class="float-right">{{ format_amount(p.total) }}</span>
                            {{ p.invoice_number }}</a>
                        <hr />
                        {% endfor %}
                        {% else %}
                        <div>{{ i('fa fa-info-circle') }} {{ lang('no_invoices') }}</div>
                        {% endif %}
                    </div>
                    <div>

    
    <div id="subscription-details" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ 'YOUR SUBSCRIPTION' }}
                            {% if config_enabled('sts_products_enable_subscription_cancellations') %}
                                {% if p.status == 1 %}
                                    <a data-href="{{ page_url('members', 'subscriptions/cancel/'~p.sub_id) }}"
                                       data-toggle="modal" data-target="#confirm-delete" href="#"
                                       class="btn btn-sm btn-warning float-right">{{ lang('cancel') }}</a>
                                    </a>
                                {% else %}
                                    <!--<span class="btn btn-sm btn-danger float-right">{{ lang('cancelled') }}</span>-->
                                {% endif %}
                            {% endif %}
                        </h5>
                         <div class="col-md-12">
                        <h4 class="headline"><center>{{ 'Membership Level: ' }}
                        {{ 'Default' }}</center></h4>

                                <strong>{{ lang('Subscription Activated: ') }}</strong> {{ display_date(p.start_date) }}<br/>
                                <strong>{{ lang('Subscription Valid Until') }}</strong>
                                    {{ display_date(p.next_due_date)}}
                                    <!--span class="badge badge-danger">{{ lang('cancelled') }}</span>--></br>
                            
                                {% if p.order_id %}
                                    <strong>{{ lang('original_order') }}</strong>
                                    <a href="{{ page_url('members', 'orders/details/'~p.order_id) }}">#{{ p.order_number }}</a>
                                {% endif %}
                                {% if p.invoice_id %}
                                    <br/><strong>{{ lang('latest_invoice') }}</strong>
                                    <a href="{{ page_url('members', 'invoices/details/'~p.invoice_id) }}">#{{ p.invoice_number }}</a>
                                {% endif %}
                            
                        
                                <strong>{{ lang('Get Your Monthly eBook Today To Keep Your Account Active To Always Earn Commission On Your Downline') }}</strong>
                                <br/>
                                <strong>{{ lang('Click to renew now') }}</strong><br/>

                                <strong>{{ p.product_name }}</strong>
                            </div>
                        
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
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






                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock content %}