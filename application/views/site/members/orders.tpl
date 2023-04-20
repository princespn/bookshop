{% extends "global/base.tpl" %}
{% block title %}{{ lang('orders')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('orders') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="orders" class="content">
        {{ breadcrumb }}
        {% if orders.rows %}
            <table id="order-table" class="table table-striped table-hover">
                <thead>
                <tr>
                    <th class="text-sm-center" style="width: 20%">{{ lang('order_number') }}</th>
                    <th class="text-sm-center" style="width: 20%">{{ lang('order_date') }}</th>
                    <th class="text-sm-center" style="width: 10%">{{ lang('status') }}</th>
                    <th style="width: 10%"></th>
                </tr>
                </thead>
                <tbody>
                {% for p in orders.rows %}
                    <tr class="text-sm-center">
                        <td>
                            <a href="{{ page_url('members', 'orders/details/'~p.order_id) }}">
                                {{ p.order_number }}
                            </a>
                        </td>
                        <td>{{ display_date(p.date_ordered, TRUE) }}</td>
                        <td><span class="badge badge-info"
                                  style="background-color: {{ p.color }}">{{ lang(p.order_status) }}</span></td>
                        <td class="text-sm-right">
                            <a href="{{ page_url('members', 'orders/details/'~p.order_id) }}"
                               class="btn btn-secondary">
                                {{ i('fa fa-search') }}
                            </a>
                            <a href="{{ page_url('members', 'orders/details/'~p.order_id~'/print') }}"
                               class="btn btn-secondary">
                                {{ i('fa fa-print') }}
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
                {{ lang('no_orders_found') }}
            </div>
        {% endif %}

    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    {{ include('js/datatables.tpl') }}
{% endblock javascript_footer %}
