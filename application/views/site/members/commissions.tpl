{% extends "global/base.tpl" %}
{% block title %}{{ lang('commissions')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('affiliate_commissions') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="commissions" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                {% if commissions %}
                    <table id="invoice-table" class="table table-striped table-hover dt-responsive nowrap">
                        <thead>
                        <tr>
                            <th class="text-sm-center" style="width: 10%">{{ lang('status') }}</th>
                            <th class="text-sm-center" style="width: 10%">{{ lang('date') }}</th>
                            <th class="text-sm-center" style="width: 10%">{{ lang('transaction_id') }}</th>
                            <th class="text-sm-center" style="width: 10%">{{ lang('amount') }}</th>
                            <th style="width: 10%"></th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for p in commissions %}
                            <tr class="text-sm-center">
                                <td><span class="badge badge-info badge-{{ p.comm_status }}">{{ p.comm_status }}</span>
                                </td>
                                <td>{{ display_date(p.date) }}</td>
                                <td>
                                    <a href="{{ page_url('members', 'commissions/details/'~p.comm_id) }}">{{ p.trans_id }}</a>
                                </td>
                                <td>
                                    {{ format_amount(p.commission_amount - p.fee) }}
                                </td>
                                <td class="text-sm-right">
                                    {% if p.payment_id %}
                                        <a href="{{ page_url('members', 'affiliate_payments/details/'~p.payment_id) }}"
                                           class="btn btn-info">
                                            {{ i('fa fa-file-text') }}
                                        </a>
                                    {% endif %}
                                    <a href="{{ page_url('members', 'commissions/details/'~p.comm_id) }}"
                                       class="btn btn-secondary">
                                        {{ i('fa fa-search') }}
                                    </a>
                                </td>
                            </tr>
                        {% endfor %}
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="5"></td>
                        </tr>
                        </tfoot>
                    </table>
                {% else %}
                    <div class="alert alert-info" role="alert">
                        {{ lang('no_commissions_found') }}
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
