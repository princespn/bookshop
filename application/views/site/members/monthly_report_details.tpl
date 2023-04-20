{% extends "global/base.tpl" %}
{% block title %}{{ lang('member_reports')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{  lang(report.title) }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="monthly-report" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-9"></div>
            <div class="col-md-3">
                {% if report.dates %}
                    <span class="float-right">{{ report.dates }}</span>
                {% endif %}
            </div>
        </div>
        <hr/>
        <div id="chart_report"></div>
        <hr/>
        {% if report.rows %}
            <div class="text-md-right">
                <a class="btn btn-secondary show-table">
                    {{ i('fa fa-search') }} <span class="show-text"> {{ lang('show') }}</span> {{ lang('daily_stats') }}
                </a>
            </div>
            <br/>
            <div class="collapse" id="table-stats">
                {% if calendar %}
                {{ calendar }}
                <br />
                {% else %}
                <table id="reports-table" class="table table-striped table-hover dt-responsive nowrap">
                    <thead>
                    <tr>
                        <th>{{ lang('date') }}</th>
                        <th class="text-md-center">{{ lang('amount') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {% for key,value in report.rows %}
                        <tr>
                            <td>{{ current_date('M') }} {{ key }} {{ current_date('Y') }}</td>
                            <td class="text-md-center" style="width: 10%">
                                {% if report.currency %}
                                    {{ format_amount(value) }}
                                {% else %}
                                    {{ value }}
                                {% endif %}
                            </td>
                        </tr>
                    {% endfor %}
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                    </tfoot>
                </table>
                {% endif %}
            </div>
        {% endif %}

    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    <script src="{{ base_url('js/highcharts/highcharts.js') }}"></script>
    <script>
        $('a.show-table').click(function () {
            $('#table-stats').collapse('toggle');
        });
        $('#table-stats').on('hidden.bs.collapse', function () {
            $('.show-text').html('{{ lang('show') }}');
        });
        $('#table-stats').on('shown.bs.collapse', function () {
            $('.show-text').html('{{ lang('hide') }}');
        });
    </script>
    {% if report.rendered_html %}
        {{ report.rendered_html|raw }}
    {% endif %}
{% endblock %}