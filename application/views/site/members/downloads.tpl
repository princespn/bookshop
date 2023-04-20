{% extends "global/base.tpl" %}
{% block title %}{{ lang('downloads')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('downloads') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="downloads" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                {% if downloads.rows %}
                    <table id="download-table" class="table table-striped table-hover dt-responsive nowrap">
                        <thead>
                        <tr>
                            <th style="width:20%">{{ lang('file_name') }}</th>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('downloads') }}</th>
                            <th class="text-sm-center"  style="width: 10%">{{ lang('expires') }}</th>
                            <th style="width: 5%"></th>
                        </tr>
                        </thead>
                        <tbody>
                        {% for p in downloads.rows %}
                            <tr>
                                <td> {{ p.product_name }} - {{ p.filename }}</td>
                                <td class="text-sm-center">{{ p.downloads }}
                                    {% if p.max_downloads_user %}
                                        / {{ p.max_downloads_user }}
                                    {% endif %}
                                </td>
                                <td class="text-sm-center">{{ display_date(p.expires) }}</td>
                                <td class="text-sm-right">
                                    {% if (check_download_limits(p)) %}
                                        <span class="btn btn-danger">{{ i('fa fa-exclamation-circle') }}</span>
                                    {% else %}
                                        <a href="{{ page_url('members', 'downloads/get/'~p.d_id) }}"
                                           class="btn btn-secondary">
                                            {{ i('fa fa-download') }}
                                        </a>
                                    {% endif %}
                                </td>
                            </tr>
                        {% endfor %}
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                        </tfoot>
                    </table>
                {% else %}
                    <div class="alert alert-info" role="alert">
                        {{ lang('no_downloads_found') }}
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
