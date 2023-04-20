{% extends "global/meta.tpl" %}
{% block body %}
    <div id="sidebar" class="d-md-none sidebar-offcanvas">
        {{ format_menu(top_menu, 'class="sidebar-list list-group list-group-flush"', TRUE) }}
        {% if config_enabled('layout_design_show_search_form') %}
        <div class="mt-3 mx-3">
            {{ form_open('search', 'method="get" id="top-search-form" class="form-horizontal"') }}
            <div class="input-group">
                <input type="text" name="search_term" class="form-control"
                       placeholder="{{ lang('search_for') }}...">
                <div class="input-group-append">
                    <button class="btn btn-secondary" type="submit">{{ lang('go') }}</button>
                </div>
            </div>
            {{ form_close() }}

        </div>
        {% endif %}
    </div>
    <div id="{{ page_id }}" class="body row-offcanvas">
        {% block top_nav %}
        {{ include ('global/top_nav.tpl') }}
        {% endblock top_nav %}
        <div class="page">
            <div class="header d-none d-md-block">
                <div class="top-header">
               {{ include ('global/top_menu.tpl') }}
                <div id="{{ page_id }}-block" class="page-header d-none d-md-block">
                    {% block page_header %}{% endblock page_header %}
                </div>
                </div>
            </div>
            <!-- /.header -->

            <div id="main" class="content main">
                <div id="response">
                    {% if (sess('success')) %}
                        {{ alert('success', sess('success')) }}
                    {% elseif (sess('error')) %}
                        {{ alert('error', sess('error')) }}
                    {% elseif error %}
                        {{ alert('error', error) }}
                    {% endif %}
                </div>
                {% block container %}
                    <div class="container">
                        {% block content %}{% endblock content %}
                    </div>
                {% endblock container %}
            </div>
            <!-- /.content -->

            {% include ('global/footer.tpl') %}
        </div>
        <div id="loading" class="spinner">{{ i('fa fa-spinner fa-pulse') }}</div>
    </div>
    {% block confirm_delete %}
        <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="confirm-title"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body capitalize">
                        <h3 id="confirm-title"><i class="fa fa-trash-o"></i> {{ lang('confirm_deletion') }}</h3>
                        {{ lang('are_you_sure_you_want_to_delete') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ lang('cancel') }}</button>
                        <a href="#" class="btn btn-danger danger">{{ lang('delete') }}</a>
                    </div>
                </div>
            </div>
        </div>
    {% endblock confirm_delete %}

    {% include ('form/login_modal.tpl') %}
    {% block javascript_footer %}
        {% include ('js/footer_js.tpl') %}
    {% endblock javascript_footer %}
{% endblock body %}

