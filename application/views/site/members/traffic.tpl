{% extends "global/base.tpl" %}
{% block title %}{{ lang('affiliate_traffic')|capitalize }}{% endblock %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{  lang('affiliate_traffic') }}</h2>
        </div>

    </div>
</div>
{% endblock page_header %}
{% block content %}
<div id="traffic" class="content">
    {{ breadcrumb }}
    <div class="row">
        <div class="col-md-12">
            {% if traffic.rows %}
            <table id="traffic-table"
                   class="table table-striped table-hover table-responsive dt-responsive nowrap dataTable">
                <thead>
                <tr>
                    <th class="text-sm-center" style="width: 10%">{{ lang('date') }}</th>
                    <th class="text-sm-center" style="width: 10%">{{ lang('ip_address') }}</th>
                    <th class="text-sm-center" style="width: 10%">{{ lang('os') }}</th>
                    <th class="text-sm-center" style="width: 10%">{{ lang('browser') }}</th>
                </tr>
                </thead>
                <tbody>
                {% for p in traffic.rows %}
                <tr class="text-sm-center">
                    <td>{{ display_date(p.date, TRUE) }}</td>
                    <td>{{ p.ip_address }}</td>
                    <td>{{ p.os }}</td>
                    <td><i class="fa fa-{{ url_title(p.browser)|lower }}"></i> {{ p.browser }}</td>
                </tr>
                {% endfor %}
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                </tr>
                </tfoot>
            </table>
            <div class="col-md-7 offset-5">
                <div id="traffic_pagination">
                    <ul class="pagination">
                        {{ paginate['rows'] }}
                    </ul>
                </div>
            </div>
            {% else %}
            <div class="alert alert-info" role="alert">
                {{ lang('no_traffic_found') }}
            </div>
            {% endif %}
        </div>
    </div>
</div>
{% endblock content %}
{% block javascript_footer %}
{{ parent() }}

{% endblock javascript_footer %}
