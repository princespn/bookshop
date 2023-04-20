{% extends "global/base.tpl" %}
{% block title %}{{ lang('invoices')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('invoices') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="invoices" class="content table-responsive">
        {{ breadcrumb }}
        {% if invoices.rows %}
            <table id="invoice-table" class="table table-striped table-hover" width="100%">
                <thead>
                <tr>
                    <th class="text-sm-center" style="width: 20%">{{ lang('invoice_number') }}</th>
                    <th class="text-sm-center" style="width: 15%">{{ lang('invoice_date') }}</th>
                    <th class="text-sm-center" style="width: 20%">{{ lang('due_date') }}</th>
                    <th class="text-sm-center" style="width: 15%">{{ lang('amount') }}</th>
                    <th class="text-sm-center" style="width: 10%">{{ lang('status') }}</th>
                    <th style="width: 20%"></th>
                </tr>
                </thead>
                <tbody>
                {% for p in invoices.rows %}
                    <tr class="text-sm-center">
                        <td>
                            <a href="{{ page_url('members', 'invoices/details/'~p.invoice_id ) }}">
                                {{ p.invoice_number }}
                            </a>
                        </td>
                        <td>{{ display_date(p.date_purchased) }}</td>
                        <td>{{ display_date(p.due_date) }}</td>
                        <td>{{ format_amount(p.total) }}</td>
                        <td><span class="badge badge-info"
                                  style="background-color: {{ p.color }}">{{ p.payment_status }}</span></td>
                        <td class="text-sm-right">
                            {% if p.payment_status_id == 1 %}
                                <a href="{{ page_url('checkout', 'invoice/payment/'~p.invoice_id ) }}"
                                   class="btn btn-info"
                                   title="{{ lang('pay') }}">
                                    {{ i('fa fa-credit-card') }}
                                </a>
                            {% endif %}
                            <a href="{{ page_url('members', 'invoices/details/'~p.invoice_id ) }}"
                               class="btn btn-secondary"
                               title="{{ lang('view') }}">
                                {{ i('fa fa-search') }}
                            </a>
                            <a href="{{ page_url('members', 'invoices/details/'~p.invoice_id~'/print' ) }}"
                               target="_blank" class="btn btn-secondary"
                               title="{{ lang('print') }}">
                                {{ i('fa fa-print') }}
                            </a>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="6"></td>
                </tr>
                </tfoot>
            </table>
        {% else %}
            <div class="alert alert-info" role="alert">
                {{ lang('no_invoices_found') }}
            </div>
        {% endif %}
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    {{ include('js/datatables.tpl') }}
{% endblock javascript_footer %}
