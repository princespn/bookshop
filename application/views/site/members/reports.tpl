{% extends "global/base.tpl" %}
{% block title %}{{ lang('member_reports')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{  lang('reports') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="reports" class="content">
        {{ breadcrumb }}
        {% if reports %}
            <br />
            {% for p in reports %}
                <div class="row">
                    <div class="col-md-10"><h5>{{ lang(p.module_name) }}</h5>
                        <small>{{ check_desc(p.module_description) }}</small>
                    </div>
                    <div class="col-md-2 text-sm-right">
                        <a href="{{ page_url('members', 'reports/generate/'~p.module_id) }}"
                           class="btn btn-secondary">{{ i('fa fa-search') }}</a>
                    </div>
                </div>
                <hr/>
            {% endfor %}
        {% endif %}
    </div>
{% endblock content %}