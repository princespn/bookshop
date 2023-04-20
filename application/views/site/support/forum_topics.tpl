{% extends "global/base.tpl" %}
{% block title %}{% if search_term %}{{ lang('search') }}{% else %}{{ category.meta_title }}{% endif %}{% endblock %}
{% block meta_description %}{% if search_term %}{{ search_term }}{% else %}{{ category.meta_description }}{% endif %}
{% endblock meta_description %}{% block meta_keywords %}{% if search_term %}{{ search_term }}{% else %}{{ category.meta_keywords }}{% endif %}
{% endblock meta_keywords %}
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
    <div class="forum-topics">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-7">
                <h2>
                    {% if search_term %}
                        {{ lang('search') }} - {{ search_term|striptags }}
                    {% else %}
                        {{ category.category_name }}
                    {% endif %}
                </h2>
            </div>
            <div class="col-md-5">
                {{ form_open(forum_uri~'/search', 'method="get" id="search-form" class="form-horizontal"') }}
                <div class="input-group">
                    <input type="text" name="search_term" class="form-control"
                           placeholder="{{ lang('search_forum') }}...">
                    <div class="input-group-append">
                        <button class="btn btn-secondary"
                                type="submit">{{ i('fa fa-search') }} {{ lang('search') }}</button>
                    </div>
                </div>
                {{ form_close() }}
            </div>
        </div>
        <br/>
        <div class="row">
            {% if layout_design_forum_sidebar == 'left' %}
                {% include ('support/forum_sidebar.tpl') %}
            {% endif %}
            <div class="col-md-{% if layout_design_forum_sidebar == 'none' %}12{% else %}9{% endif %}">
                <div class="card">
                    <div class="card-header">{{ i('fa fa-folder-open-o') }} {{ lang('latest_discussions') }}</div>
                    <div class="card-body">
                        {% if pinned %}
                            {% for p in pinned %}
                                <div class="row">
                                    <div class="col-md-1 text-center">
                                        {% if p.admin_id %}
                                            {{ image('admin', p.admin_photo, p.admin_fname, 'img-fluid img-forum rounded-circle', FALSE) }}
                                        {% else %}
                                            {% if config_enabled('sts_site_enable_user_profiles') %}
                                                <a href="{{ site_url('profile/'~p.username) }}">
                                                    {{ image('forum', p.profile_photo, p.member_fname, 'img-fluid rounded-forum rounded-circle', FALSE) }}
                                                </a>
                                            {% else %}
                                                {{ image('forum', p.profile_photo, p.member_fname, 'img-fluid rounded-forum rounded-circle', FALSE) }}
                                            {% endif %}
                                        {% endif %}
                                    </div>
                                    <div class="col-md-11">
                                                <span class="topic-title">
                                                    <a href="{{ page_url('forum', p) }}">
                                                       <span class="badge badge-success">{{ i('fa fa-thumb-tack') }}</span> {{ p.title }}</a>
                                                </span>
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
                                                    <li>{{ i('fa fa-eye') }} {{ p.views }} {{ lang('views') }}</li>
                                                    <li> {{ i('fa fa-comments') }} {{ p.replies }}</li>
                                                </ul>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                            {% endfor %}
                        {% endif %}
                        {% if topics %}
                            {% for p in topics %}
                                {% if p.status == '1' %}
                                    <div class="row">
                                        <div class="col-md-1 text-center">
                                            {% if p.admin_id %}
                                                {{ image('admin', p.admin_photo, p.admin_fname, 'img-fluid img-forum rounded-circle', FALSE) }}
                                            {% else %}
                                                {% if config_enabled('sts_site_enable_user_profiles') %}
                                                    <a href="{{ site_url('profile/'~p.username) }}">
                                                        {{ image('forum', p.profile_photo, p.member_fname, 'img-fluid img-forum rounded-circle', FALSE) }}
                                                    </a>
                                                {% else %}
                                                    {{ image('forum', p.profile_photo, p.member_fname, 'img-fluid img-forum rounded-circle', FALSE) }}
                                                {% endif %}
                                            {% endif %}
                                        </div>
                                        <div class="col-md-10">
                                            <br class="d-sm-none"/>
                                                <span class="topic-title">
                                                    <a href="{{ page_url('forum', p) }}">{{ p.title }}</a>
                                                </span>
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
                                                        <li>{{ i('fa fa-eye') }} {{ p.views }} {{ lang('views') }}</li>
                                                        <li> {{ i('fa fa-comments') }} {{ p.replies }}</li>
                                                    </ul>
                                            </div>
                                        </div>
                                    </div>
                                    {% if member_logged_in %}
                                        <div class="text-sm-right">
                                            {% if (check_moderation(p)) %}
                                                {% if p.member_id %}
                                                    <a data-href="{{ site_url(forum_uri~'/delete_topic/'~p.topic_id~'/'~p.member_id) }}"
                                                       title="{{ lang('delete_topic') }}"
                                                       data-toggle="modal" data-target="#confirm-delete"
                                                       href="#"
                                                       class="md-trigger btn btn-sm btn-secondary">{{ i('fa fa-times') }}</a>
                                                {% endif %}
                                            {% endif %}
                                        </div>
                                    {% endif %}
                                    <hr/>
                                {% else %}
                                    {% if member_logged_in == 1 %}
                                        {% if (check_moderation(p)) %}
                                            <div class="pending">
                                                <div class="row">
                                                    <div class="col-md-1">
                                                        {% if p.admin_id %}
                                                            {{ image('admin', p.admin_photo, p.admin_fname, 'img-fluid img-forum rounded-circle', FALSE) }}
                                                        {% else %}
                                                            {% if config_enabled('sts_site_enable_user_profiles') %}
                                                                <a href="{{ site_url('profile/'~p.username) }}">
                                                                    {{ image('forum', p.profile_photo, p.member_fname, 'img-fluid img-forum rounded-circle', FALSE) }}
                                                                </a>
                                                            {% else %}
                                                                {{ image('forum', p.profile_photo, p.member_fname, 'img-fluid img-forum rounded-circle', FALSE) }}
                                                            {% endif %}
                                                        {% endif %}
                                                    </div>
                                                    <div class="col-md-10">
                                                        <span class="topic-title">
                                                            <a href="{{ page_url('forum', p) }}">{{ p.title }}</a>
                                                        </span>
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
                                                                    <li><span class="badge badge-pill badge-info">
                                                                            {{ lang('pending_moderation') }}
                                                                            </span>
                                                                    </li>
                                                                </ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 text-sm-center">
                                                        <small class="badge badge-default badge-replies">{{ i('fa fa-comments') }} {{ p.replies }}</small>
                                                    </div>
                                                </div>
                                                {% if member_logged_in %}
                                                    <div class="text-sm-right">
                                                        {% if (check_moderation(p)) %}
                                                            {% if p.member_id %}
                                                                <a data-href="{{ site_url(forum_uri~'/delete_topic/'~p.topic_id~'/'~p.member_id) }}"
                                                                   title="{{ lang('delete_topic') }}"
                                                                   data-toggle="modal" data-target="#confirm-delete"
                                                                   href="#"
                                                                   class="md-trigger btn btn-sm btn-secondary">{{ i('fa fa-times') }}</a>
                                                            {% endif %}
                                                        {% endif %}
                                                        {% if (check_moderation(p, true)) %}
                                                            <a href="{{ site_url(forum_uri~'/approve_topic/'~p.topic_id) }}"
                                                               class="btn btn-sm btn-secondary btn-approve"
                                                               title="{{ lang('approve_topic') }}">
                                                                {{ i('fa fa-thumbs-up') }}</a>
                                                        {% endif %}
                                                    </div>
                                                {% endif %}
                                                <hr/>
                                            </div>
                                        {% endif %}
                                    {% endif %}
                                {% endif %}
                            {% endfor %}
                            {% include ('global/pagination.tpl') %}
                        {% else %}
                            <div class="alert alert-secondary">{{ lang('no_forum_topics_found') }}</div>
                        {% endif %}
                    </div>
                </div>
            </div>
            {% if layout_design_forum_sidebar == 'right' %}
                {% include ('support/forum_sidebar.tpl') %}
            {% endif %}
        </div>
    </div>
{% endblock content %}