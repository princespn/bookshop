{% extends "global/base.tpl" %}
{% block title %}{{ lang('support_tickets') }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{  lang('support') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="support" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-6">
                <h2>
                    {% if closed == 1 %}
                        {{ lang('closed_support_tickets') }}
                    {% else %}
                        {{ lang('open_support_tickets') }}
                    {% endif %}
                </h2>
            </div>
            <div class="col-md-6 text-right">
            {% if closed == 1 %}
                <a href="{{ site_url('members/support/view/') }}"
                   class="btn btn-secondary">{{ i('fa fa-search') }} {{ lang('view_open_tickets') }}</a>
            {% else %}
                <a href="{{ site_url('members/support/view/?closed=1') }}"
                   class="btn btn-secondary">{{ i('fa fa-search') }} {{ lang('view_closed_tickets') }}</a>
            {% endif %}
                <a href="{{ site_url('members/support/create') }}"
                   class="btn btn-primary">{{ i('fa fa-plus') }} {{ lang('create_ticket') }}</a>
            </div>
        </div>

        <hr/>
        {% if tickets %}
            <table id="support-table" class="table table-striped table-hover dt-responsive">
                <thead>
                <tr>
                    <th class="text-sm-center"  style="width: 10%">{{ lang('department') }}</th>
                    <th>{{ lang('subject') }}</th>
                    <th class="text-sm-center"  style="width: 10%">{{ lang('status') }}</th>
                    <th style="width: 19%" class="text-sm-center">{{ lang('last_updated') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {% for p in tickets %}
                    <tr>
                        <td class="text-sm-center" style="width: 10%"><span class="badge badge-info badge-support-category-{{ p.category_id }}">{{ p.category_name }}</span></td>
                        <td><a href="{{ site_url('members/support/ticket') }}/{{ p.ticket_id }}">#{{ p.ticket_id }}
                                - {{ character_limiter(p.ticket_subject, 50) }}</a></td>
                        <td class="text-sm-center" style="width: 10%">
                            {% if p.closed == 0 %}
                            <span class="badge badge-info badge-ticket-status-{{ p.ticket_status }}">{{ lang(p.ticket_status) }}</span>
                            {% else %}
                                <span class="badge badge-info">{{ lang('closed') }}</span>
                            {% endif %}
                        </td>
                        <td class="text-sm-center" style="width: 18%">{{ display_date(p.date_modified, TRUE) }}</td>
                        <td class="text-sm-right" style="width: 10%"><a href="{{ site_url('members/support/ticket') }}/{{ p.ticket_id }}"
                                                  class="btn btn-secondary">{{ i('fa fa-search') }}</a></td>
                    </tr>
                {% endfor %}
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="5">

                    </td>
                </tr>
                </tfoot>
            </table>
        {% else %}
            <div class="alert alert-info" role="alert">
                {{ i('fa fa-info-circle') }} {{ lang('no_tickets_found') }}
            </div>
        {% endif %}
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    {{ include('js/datatables.tpl') }}
{% endblock javascript_footer %}
