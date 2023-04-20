{% extends "global/base.tpl" %}
{% block title %}{{ lang('gift_certificates')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('gift_certificates') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="downloads" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12 table-responsive ">
                {% if certificates.rows %}
                    <table id="gift-certificates-table" class="table table-striped table-hover dt-responsive nowrap">
                        <thead>
                        <tr>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('from_name') }}</th>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('from_email') }}</th>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('to_name') }}</th>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('to_email') }}</th>
                            <th class="text-sm-center"  style="width: 20%">{{ lang('code') }}</th>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('value') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for p in certificates.rows %}
                            <tr>
                                <td class="text-sm-center">{{ p.from_name }}</td>
                                <td class="text-sm-center">{{ p.from_email }}</td>
                                <td class="text-sm-center">{{ p.to_name }}</td>
                                <td class="text-sm-center">{{ p.to_email }}</td>
                                <td class="text-sm-center">{{ p.code }}</td>
                                <td class="text-sm-center">{{ format_amount(p.amount + p.redeemed) }}</td>
                                <td class="text-sm-right">
                                    <a href="{{ page_url('members', 'gift_certificates/details/'~p.cert_id) }}" class="btn btn-secondary">
                                        {{ i('fa fa-search') }}
                                    </a>
                                </td>
                            </tr>
                        {% endfor %}
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="7"></td>
                        </tr>
                        </tfoot>
                    </table>
                {% else %}
                    <div class="alert alert-info" role="alert">
                        {{ lang('no_gift_certificates_found') }}
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
