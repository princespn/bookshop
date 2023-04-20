{% extends "global/base.tpl" %}
{% block title %}{{ lang('affiliate_coupons')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('affiliate_coupons') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="coupons" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                {% if coupons.rows %}
                    <table id="coupon-table" class="table table-striped table-hover dt-responsive nowrap">
                        <thead>
                        <tr>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('coupon_code') }}</th>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('amount') }}</th>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('starts_on') }}</th>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('expires_on') }}</th>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('redemption_limits') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for p in coupons.rows %}
                            <tr class="text-sm-center">
                                <td><strong>{{ p.coupon_code }}</strong></td>
                                <td>
                                    {% if p.coupon_type == 'percent' %}
                                    {{ p.coupon_amount|number_format }} {{ lang('percent') }}
                                    {% else %}
                                        {{ format_amount(p.coupon_amount) }} {{ lang('flat') }}
                                    {% endif %}
                                </td>
                                <td><span class="badge badge-primary">{{ display_date(p.start_date) }}</span></td>
                                <td><span class="badge badge-primary">{{ display_date(p.end_date) }}</span></td>
                                <td>
                                    {{ coupon_redemptions(p) }}
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
                        {{ lang('no_coupons_found') }}
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
