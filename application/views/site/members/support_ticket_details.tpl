{% extends "global/base.tpl" %}
{% block title %}{{ lang('ticket_id')|capitalize }} #{{ sts_support_tickets_prefix }}{{ id }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('support_ticket') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="ticket-details" class="content">
        {{ breadcrumb }}
        <div class="d-md-none">
            <h2>{{ lang('ticket_id') }} #{{ sts_support_tickets_prefix }}{{ p.ticket_id }}</h2>
            <hr/>
        </div>
        <div class="row">
            <div class="col-md-3">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>{{ lang('ticket_id') }} #{{ sts_support_tickets_prefix }}{{ p.ticket_id }}
                        </strong>
                    </li>
                    <li class="list-group-item">
                        <strong>{{ lang('status') }}
                            {% if p.closed == 0 %}
                                <span class="float-right badge badge-info badge-ticket-status-{{ p.ticket_status }}">{{ lang(p.ticket_status) }}</span>
                            {% else %}
                                <span class="float-right badge badge-default">{{ lang('closed') }}</span>
                            {% endif %}
                        </strong>
                    </li>
                    <li class="list-group-item">
                        <strong>{{ lang('priority') }}</strong>
                        <span class="float-right badge badge-info badge-ticket-priority-{{ p.priority }}">{{ lang(p.priority) }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>{{ lang('department') }}</strong><br/>{{ p.category_name }}
                    </li>
                    <li class="list-group-item d-none d-md-block">
                        <strong>{{ lang('date_created') }}</strong><br/>{{ display_date(p.date_added, true) }}
                    </li>
                    <li class="list-group-item">
                        <strong>{{ lang('last_updated') }}</strong><br/>{{ display_date(p.date_modified, true) }}
                    </li>

                    <li class="list-group-item">
                        {% if (p.closed == 0) %}
                            <a href="#reply"
                               class="btn btn-primary btn-block">
                                {{ i('fa fa-pencil') }} {{ lang('reply_to_ticket') }}
                            </a>
                        {% endif %}
                        <a href="{{ site_url('members/support/update_status') }}/{{ p.ticket_id }}/{{ p.closed }}"
                           class="btn btn-danger btn-block">
                            {{ i('fa fa-close') }} {{ lang('close_ticket') }}
                        </a>
                        <a href="{{ site_url('members/support') }}"
                           class="btn btn-block btn-secondary">{{ i('fa fa-undo') }} {{ lang('view_tickets') }}</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <h5>{{ p.ticket_subject }}</h5>
                        <hr />
                        {% if p.replies %}
                            {% for s in p.replies %}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-{% if s.reply_type == 'admin' %}info{% endif %}">
                                            <div class="box-meta">
                                                    <ul class="list-inline">
                                                        <li> {{ i('fa fa-user') }}
                                                            {% if s.reply_type == 'admin' %}
                                                                {{ lang('reply_by') }}  {{ s.admin_fname }}
                                                            {% else %}
                                                                {{ lang('your_reply') }}
                                                            {% endif %}
                                                        </li>
                                                        <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ local_date(s.date) }}</li>
                                                    </ul>
                                            </div>
                                            <p class="card-text">
                                                {{ nl2br(s.reply_content|escape) }}
                                            </p>
                                            {% if (s.attachments) %}
                                                <small class="text-muted">
                                                    {{ list_attachments(s.attachments, s.reply_id) }}
                                                </small>
                                            {% endif %}
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                            {% endfor %}
                        {% endif %}
                    </div>
                </div>
                {% if p.closed == 0 %}
                    <div id="reply-box">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="add-reply">
                                    <div class="card">
                                        <div class="card-body">
                                            {{ form_open_multipart('', 'id="reply-form"') }}
                                            <h5 class="card-title">{{ i('fa fa-pencil') }} {{ lang('reply_to_ticket') }}
                                            </h5>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <fieldset class="form-group">
                                                        <textarea name="reply_content" rows="10"
                                                                  class="form-control required"></textarea>
                                                    </fieldset>
                                                </div>
                                            </div>
                                            {% if config_enabled('sts_support_enable_file_uploads') %}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <small class="text-muted float-right d-none d-md-block">{{ lang('allowed_file_types') }}
                                                            : {{ str_replace('|', ',', sts_support_upload_download_types) }}
                                                            {{ sts_support_max_upload_per_reply }} {{ lang('files_max_per_upload') }}
                                                        </small>
                                                        <label>{{ lang('add_attachments') }}</label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <button id="add-file" class="btn btn-secondary btn-block"
                                                                type="button">
                                                            {{ i('fa fa-plus') }} {{ lang('add_more') }}
                                                        </button>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="file" name="files[]" class="form-control"/>

                                                        <div id="add-attachments"></div>
                                                        <div class="text-sm-right">

                                                        </div>
                                                    </div>

                                                </div>
                                                <hr/>
                                            {% endif %}
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-lg btn-primary">
                                                        {{ i('fa fa-refresh') }} {{ lang('submit_reply') }}
                                                    </button>
                                                </div>
                                            </div>
                                            {{ form_hidden('reply_type', 'member') }}
                                            {{ form_hidden('ticket_status', 'client_reply') }}
                                            {{ form_hidden('ticket_id', p.ticket_id) }}
                                            {{ form_hidden('member_id', sess('member_id')) }}
                                            {{ form_close() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a name="reply"></a>
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    <script>
        var next = 2;
        $("#reply-form").validate();
        $('#add-file').click(function () {
            if (next <= {{ sts_support_max_upload_per_reply }}) {
                $("#add-attachments").append('<div id="file-' + next + '"><br /><div class="input-group"><input type="file" name="files[]" class="form-control"/><div class="input-group-addon"><a href="javascript:remove_upload(\'#file-' + next + '\')"><i class="fa fa-trash-o "></i></a></div></div></div>');
                next++;
            }
        });

        function remove_upload(id) {
            $(id).fadeOut(300, function () {
                $(this).remove();
            });
            next--;
        }

    </script>
{% endblock javascript_footer %}
