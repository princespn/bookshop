{% extends "global/base.tpl" %}
{% block title %}{{ p.title }}{% endblock %}
{% block meta_description %}{{ p.title }} {% endblock meta_description %}
{% block meta_keywords %}{{ parent() }} {{ lang('community_forum') }} {% endblock meta_keywords %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('community_forum') }}</h2>
            </div>
        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="forum-topic">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                <h3 id="title-{{ p.topic_id }}">{{ p.title }}</h3>
            </div>
        </div>
        <br/>
        <div class="row">
            {% if layout_design_forum_sidebar == 'left' %}
                {% include ('support/forum_sidebar.tpl') %}
            {% endif %}
            <div class="col-md-{% if layout_design_forum_sidebar == 'none' %}12{% else %}9{% endif %}">
                <div class="topic-replies row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                {{ i('fa fa-folder-open-o') }} {{ lang('discussion') }}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-1 text-center">
                                        {% if p.admin_id %}
                                            {{ image('admin', p.admin_photo, p.admin_fname, 'img-fluid img-forum rounded-circle mx-auto d-block', FALSE) }}
                                        {% else %}
                                            {% if config_enabled('sts_site_enable_user_profiles') %}
                                                <a href="{{ site_url('profile/'~p.username) }}">
                                                    {{ image('forum', p.profile_photo, p.member_fname, 'img-fluid img-forum rounded-circle mx-auto d-block', FALSE) }}
                                                </a>
                                            {% else %}
                                                {{ image('forum', p.profile_photo, p.member_fname, 'img-fluid img-forum rounded-circle mx-auto d-block', FALSE) }}
                                            {% endif %}
                                        {% endif %}
                                        <small class="fname">
                                            {% if p.admin_id %}
                                                {{ p.admin_fname }}
                                            {% else %}
                                                {{ p.member_fname }}
                                            {% endif %}
                                        </small>
                                    </div>
                                    <div class="col-md-11">
                                        <br class="d-sm-none"/>
                                        <div class="box-meta">
                                                <ul class="list-inline">
                                                    <li>{{ i('fa fa-user') }} {{ lang('by') }}
                                                        {% if p.admin_id %}
                                                            {{ p.admin_fname }}
                                                        {% else %}
                                                            {{ p.member_fname }}
                                                        {% endif %}
                                                    </li>
                                                    <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(p.date_added) }}</li>
                                                </ul>
                                        </div>
                                        <div class="reply-content"
                                             id="topic-{{ p.topic_id }}">{{ format_response(p.topic|trim) }}</div>
                                        <div class="text-sm-right">
                                            {% if member_logged_in %}
                                            {% if (check_moderation(p)) %}
                                                <a href="#" onclick="edit_topic('{{ p.topic_id }}')"
                                                   title="{{ lang('edit_topic') }}"
                                                   class="btn btn-sm btn-secondary">
                                                    {{ i('fa fa-pencil') }}</a>
                                                <a data-href="{{ site_url(forum_uri~'/delete_topic/'~p.topic_id~'/'~p.member_id) }}"
                                                   title="{{ lang('delete_topic') }}"
                                                   data-toggle="modal" data-target="#confirm-delete" href="#"
                                                   class="md-trigger btn btn-sm btn-secondary">{{ i('fa fa-times') }}</a>
                                            {% endif %}
                                            {% endif %}
                                            {% if member_logged_in %}
                                                {% if p.admin_id %}
                                                    <a href="#reply_content" class="btn btn-sm btn-secondary btn-quote"
                                                       title="{{ lang('quote_topic') }}"
                                                       onclick="quote_text('{{ p.admin_fname }}', 'topic-{{ p.topic_id }}')">{{ i('fa fa-quote-right') }}</a>
                                                {% else %}
                                                    <a href="#reply_content" class="btn btn-sm btn-secondary btn-quote"
                                                       title="{{ lang('quote_topic') }}"
                                                       onclick="quote_text('{{ p.member_username }}', 'topic-{{ p.topic_id }}')">{{ i('fa fa-quote-right') }}</a>
                                                {% endif %}
                                                {% if p.status == '0' %}
                                                    {% if (check_moderation(p, true)) %}
                                                        <a href="{{ site_url(forum_uri~'/approve_topic/'~p.topic_id) }}"
                                                           class="btn btn-sm btn-secondary btn-approve"
                                                           title="{{ lang('approve_topic') }}">
                                                            {{ i('fa fa-thumbs-up') }}</a>
                                                    {% endif %}
                                                {% endif %}
                                            {% endif %}
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                {% if p.topic_replies %}
                                    {% for s in p.topic_replies %}
                                        {% if s.status == '1' %}
                                            <div class="row">
                                                <div class="col-md-1 text-sm-center">
                                                    {% if s.admin_id %}
                                                        {{ image('admin', s.admin_photo, s.admin_fname, 'img-fluid img-forum rounded-circle mx-auto d-block', FALSE) }}
                                                    {% else %}
                                                        {% if config_enabled('sts_site_enable_user_profiles') %}
                                                            <a href="{{ site_url('profile/'~s.username) }}">
                                                                {{ image('forum', s.profile_photo, s.member_fname, 'img-fluid img-forum rounded-circle mx-auto d-block', FALSE) }}
                                                            </a>
                                                        {% else %}
                                                            {{ image('forum', s.profile_photo, s.member_fname, 'img-fluid img-forum rounded-circle mx-auto d-block', FALSE) }}
                                                        {% endif %}
                                                    {% endif %}
                                                    <small class="fname">
                                                        {% if s.admin_id %}
                                                            {{ s.admin_fname }}
                                                        {% else %}
                                                            {{ s.member_fname }}
                                                        {% endif %}
                                                    </small>
                                                </div>
                                                <div class="col-md-11">
                                                    <div class="box-meta">
                                                            <ul class="list-inline">
                                                                <li>{{ i('fa fa-user') }} {{ lang('by') }}
                                                                    {% if s.admin_id %}
                                                                        {{ s.admin_fname }}
                                                                    {% else %}
                                                                        {{ s.member_fname }}
                                                                    {% endif %}
                                                                </li>
                                                                <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(s.date) }}</li>
                                                            </ul>
                                                    </div>
                                                    <div class="reply-content"
                                                         id="reply-{{ s.reply_id }}">{{ format_response(s.reply_content|trim) }}</div>
                                                    <div class="text-sm-right">
                                                        {% if member_logged_in %}
                                                            {% if (check_moderation(s)) %}
                                                                <a href="#"
                                                                   onclick="edit_reply('{{ s.reply_id }}')"
                                                                   title="{{ lang('edit_reply') }}"
                                                                   class="btn btn-sm btn-secondary">
                                                                    {{ i('fa fa-pencil') }}</a>
                                                                <a data-href="{{ site_url(forum_uri~'/delete_reply/'~s.reply_id~'/'~s.member_id) }}"
                                                                   data-toggle="modal" data-target="#confirm-delete"
                                                                   title="{{ lang('delete_reply') }}"
                                                                   href="#"
                                                                   class="md-trigger btn btn-sm btn-secondary">{{ i('fa fa-times') }}</a>
                                                            {% endif %}
                                                            {% if s.admin_id %}
                                                                <a href="#reply_content"
                                                                   class="btn btn-sm btn-secondary quote-reply"
                                                                   title="{{ lang('quote_reply') }}"
                                                                   onclick="quote_text('{{ s.admin_fname }}', 'reply-{{ s.reply_id }}')">{{ i('fa fa-quote-right') }}</a>
                                                            {% else %}
                                                                <a href="#reply_content"
                                                                   class="btn btn-sm btn-secondary quote-reply"
                                                                   title="{{ lang('quote_reply') }}"
                                                                   onclick="quote_text('{{ s.username }}', 'reply-{{ s.reply_id }}')">{{ i('fa fa-quote-right') }}</a>
                                                            {% endif %}
                                                        {% endif %}
                                                    </div>
                                                </div>
                                            </div>
                                            <hr/>
                                        {% else %}
                                            {% if member_logged_in == 1 %}
                                                {% if (check_moderation(s)) %}
                                                    <div class="pending">
                                                        <div class="row">
                                                            <div class="col-md-1 text-sm-center">
                                                                {% if s.admin_id %}
                                                                    {{ image('admin', s.admin_photo, s.admin_fname, 'img-fluid img-forum rounded-circle mx-auto d-block', FALSE) }}
                                                                {% else %}
                                                                    {% if config_enabled('sts_site_enable_user_profiles') %}
                                                                        <a href="{{ site_url('profile/'~s.username) }}">
                                                                            {{ image('forum', s.profile_photo, s.username, 'img-fluid img-forum rounded-circle mx-auto d-block', FALSE) }}
                                                                        </a>
                                                                    {% else %}
                                                                        {{ image('forum', s.profile_photo, s.username, 'img-fluid img-forum rounded-circle mx-auto d-block', FALSE) }}
                                                                    {% endif %}
                                                                {% endif %}
                                                                <small class="fname">
                                                                    {% if s.admin_id %}
                                                                        {{ s.admin_fname }}
                                                                    {% else %}
                                                                        {{ s.member_fname }}
                                                                    {% endif %}
                                                                </small>
                                                            </div>
                                                            <div class="col-md-11">
                                                                <div class="box-meta">
                                                                        <ul class="list-inline">
                                                                            <li>{{ i('fa fa-user') }} {{ lang('by') }}
                                                                                {% if s.admin_id %}
                                                                                    {{ s.admin_fname }}
                                                                                {% else %}
                                                                                    {{ s.username }}
                                                                                {% endif %}
                                                                            </li>
                                                                            <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(s.date) }}</li>
                                                                            <li><span class="badge badge-pill badge-info">
                                                                            {{ lang('pending_moderation') }}
                                                                            </span>
                                                                            </li>
                                                                        </ul>
                                                                </div>
                                                                <div class="reply-content overflow"
                                                                     id="reply-{{ s.reply_id }}">{{ format_response(s.reply_content|trim) }}</div>
                                                                <div class="text-sm-right">
                                                                    <a href="#"
                                                                       onclick="edit_reply('{{ s.reply_id }}')"
                                                                       title="{{ lang('edit_reply') }}"
                                                                       class="btn btn-sm btn-secondary">
                                                                        {{ i('fa fa-pencil') }}</a>
                                                                    <a data-href="{{ site_url(forum_uri~'/delete_reply/'~s.reply_id~'/'~s.member_id) }}"
                                                                       data-toggle="modal"
                                                                       data-target="#confirm-delete"
                                                                       href="#" title="{{ lang('delete_reply') }}"
                                                                       class="md-trigger btn btn-sm btn-secondary">{{ i('fa fa-times') }}</a>
                                                                    {% if (check_moderation(p, true)) %}
                                                                        <a href="{{ site_url(forum_uri~'/approve_reply/'~s.reply_id) }}"
                                                                           class="btn btn-sm btn-secondary btn-approve"
                                                                           title="{{ lang('approve_reply') }}">
                                                                            {{ i('fa fa-thumbs-up') }}</a>
                                                                    {% endif %}
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <hr/>
                                                {% endif %}
                                            {% endif %}
                                        {% endif %}
                                    {% endfor %}
                                {% endif %}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="topic-reply row">
                    <div class="col-md-12">
                        <div class="add-comment card">
                            <div class="card-body">
                                <h5>{{ i('fa fa-pencil') }} {{ lang('add_reply') }}</h5>
                                <hr/>
                                <a name="add_reply"></a>
                                {% if member_logged_in == 1 %}
                                    {{ form_open(forum_uri~'/add_reply/'~p.topic_id, 'id="form"') }}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <fieldset class="form-group">
                                            <textarea name="reply_content" rows="8" id="reply_content"
                                                      class="form-control required editor"
                                                      placeholder="{{ lang('type_your_reply') }}"></textarea>
                                            </fieldset>
                                        </div>
                                    </div>
                                    {% if config_enabled('sts_form_enable_forum_captcha') %}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="g-recaptcha"
                                                     data-sitekey="{{ sts_form_captcha_key }}"></div>
                                            </div>
                                        </div>
                                    {% endif %}
                                    <hr/>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-lg btn-primary">{{ i('fa fa-refresh') }} {{ lang('submit') }}</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="topic_id" value="{{ p.topic_id }}"/>
                                    {{ form_hidden('member_id', sess('member_id')) }}
                                    {{ form_close() }}
                                {% else %}
                                    <div class="alert alert-info">
                                            {{ i('fa fa-info-circle') }} {{ lang('account_login_required_to_comment') }}
                                    </div>
                                    <div>
                                        <p class="text-sm-right">
                                            <a href="{{ site_url('login') }}?redirect={{ site_url(forum_uri~'/topic/'~p.url) }}"
                                               class="btn btn-primary">
                                                {{ i('fa fa-lock') }} {{ lang('login_to_reply') }}
                                            </a>
                                            <a href="{{ site_url('register') }}" class="btn btn-secondary">
                                                {{ i('fa fa-user') }} {{ lang('create_account') }}
                                            </a>
                                        </p>
                                    </div>
                                {% endif %}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {% if layout_design_forum_sidebar == 'right' %}
                {% include ('support/forum_sidebar.tpl') %}
            {% endif %}
        </div>
    </div>
    {% if member_logged_in == 1 %}
        <div class="modal fade" id="reply-box">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{ form_open(forum_uri~'/update_reply', 'id="update-form"') }}
                    <a name="reply_content"></a>
                    <div class="modal-body capitalize">
                        <h5>{{ i('fa fa-edit') }} {{ lang('update_reply') }}</h5>
                        <br/>
                        <textarea name="reply_content" id="update-reply-content" rows="8"
                                  class="form-control required"></textarea>
                        <input type="hidden" name="reply_id" id="reply_id"/>
                        {{ form_hidden('member_id', sess('member_id')) }}
                        {% if config_enabled('sts_form_enable_forum_captcha') %}
                        <br />
                        <div class="g-recaptcha"
                             data-sitekey="{{ sts_form_captcha_key }}"></div>
                        {% endif %}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ lang('cancel') }}</button>
                        <button type="submit"
                                class="btn btn-primary">{{ i('fa fa-refresh') }} {{ lang('save_changes') }}</button>
                    </div>
                    {{ form_close() }}
                </div>
            </div>
        </div>
        <div class="modal fade" id="topic-box">
            <div class="modal-dialog">
                <div class="modal-content">
                    {{ form_open(forum_uri~'/update_topic/'~p.topic_id, 'id="update-form"') }}
                    <a name="reply_content"></a>
                    <div class="modal-body capitalize">
                        <h5>{{ i('fa fa-edit') }} {{ lang('update_topic') }}</h5>
                        {{ form_input('title', '', 'id="topic-title" class="form-control required"') }}
                        <br/>
                        <textarea name="topic" id="update-topic-content" rows="10"
                                  class="form-control required"></textarea>
                        {% if config_enabled('sts_form_enable_forum_captcha') %}
                        <br />
                        <div class="g-recaptcha"
                             data-sitekey="{{ sts_form_captcha_key }}"></div>
                        {% endif %}
                        <input type="hidden" name="topic_id" value="{{ p.topic_id }}"/>
                        {{ form_hidden('member_id', sess('member_id')) }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ lang('cancel') }}</button>
                        <button type="submit"
                                class="btn btn-primary">{{ i('fa fa-refresh') }} {{ lang('save_changes') }}</button>
                    </div>
                    {{ form_close() }}
                </div>
            </div>
        </div>
    {% endif %}

{% endblock content %}
    {% block javascript_footer %}
        {{ parent() }}
        <script>
            function quote_text(user, id) {
                id = '[quote=' + user + ']' + $('#' + id).text() + '[/quote]';
                $('#reply_content').val($('#reply_content').val() + id);
            }
            {% if member_logged_in == 1 %}

            function edit_reply(id) {
                $('.form-control').removeClass('error');

                $.ajax({
                    url: '{{ site_url(forum_uri~'/get_reply') }}',
                    type: 'get',
                    data: 'reply_id=' + id,
                    dataType: 'json',
                    success: function (data) {
                        if (data['type'] == 'error') {
                            $('#response').html('{{ alert('error') }}');
                            $('#msg-details').html(data['msg']);
                        }
                        else if (data['type'] == 'success') {
                            content = data['msg'];
                            $('#update-reply-content').val(content);
                            $('#reply_id').val(id);
                            $('#reply-box').modal();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });

            }

            function edit_topic(id) {
                $('.form-control').removeClass('error');

                $.ajax({
                    url: '{{ site_url(forum_uri~'/get_topic') }}',
                    type: 'get',
                    data: 'topic_id=' + id,
                    dataType: 'json',
                    success: function (data) {
                        if (data['type'] == 'error') {
                            $('#response').html('{{ alert('error') }}');
                            $('#msg-details').html(data['msg']);
                        }
                        else if (data['type'] == 'success') {
                            content = data['msg'];
                            $('#update-topic-content').val(content);
                            $('#topic_id').val(id);
                            $('#topic-box').modal();

                            title = $('#title-' + id).text();
                            $('#topic-title').val(title);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
            }

            $("#update-form").validate();
            $("#form").validate({
                ignore: "",
                submitHandler: function (form) {
                    $.ajax({
                        url: '{{ site_url(forum_uri~'/add_reply/'~p.topic_id) }}',
                        type: 'POST',
                        dataType: 'json',
                        data: $('#form').serialize(),
                        success: function (response) {
                            if (response.type == 'success') {
                                $('.alert-danger').remove();
                                $('.form-control').removeClass('error');

                                if (response.redirect) {
                                    location.href = response.redirect;
                                }
                            }
                            else {
                                $('#response').html('{{ alert('error') }}');
                            }

                            $('#msg-details').html(response.msg);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                }
            });
            {% endif %}

        </script>
    {% endblock javascript_footer %}