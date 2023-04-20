{% extends "global/base.tpl" %}
{% block title %}{{ lang('affiliate_payments')|capitalize }}{% endblock %}
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
    <div id="affiliate-payments" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                {% if payments %}
                    <table id="invoice-table" class="table table-striped table-hover dt-responsive nowrap">
                        <thead>
                        <tr>
                            <th class="text-sm-center" style="width: 10%">{{ lang('date') }}</th>
                            <th style="width: 10%">{{ lang('payment_details') }}</th>
                            <th class="text-sm-center" style="width: 10%">{{ lang('amount') }}</th>
                            <th style="width: 10%"></th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for p in payments %}
                            <tr>
                                <td class="text-sm-center">{{ display_date(p.payment_date) }}</td>
                                <td>
                                   {{ p.payment_details }}
                                </td>
                                <td class="text-sm-center">{{ format_amount(p.payment_amount) }}</td>
                                <td class="text-sm-right">
                                    <a href="{{ page_url('members', 'affiliate_payments/details/'~p.id) }}"
                                       class="btn btn-secondary">
                                        {{ i('fa fa-search') }}
                                    </a>
                                    </a>
                                </td>
                            </tr>
                        {% endfor %}
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="3"></td>
                        </tr>
                        </tfoot>
                    </table>
                {% else %}
                    <div class="alert alert-info" role="alert">
                        {{ lang('no_affiliate_payments_found') }}
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    {{ include('js/datatables.tpl') }}
{% endblock javascript_footer %}
