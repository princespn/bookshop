{% extends "global/base.tpl" %}
{% block title %}{{ parent() }} {{ lang('community_forum') }}{% endblock %}
{% block meta_description %}{{ parent() }} {{ lang('community_forum') }} {% endblock meta_description %}
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
    <div class="forum-home">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-7">
                <h2>{{ lang('latest_community_topics') }}</h2>
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
                {% if topics %}
                    {% for p in topics %}
                        <div class="card">
                            <div class="card-header">{{ i('fa fa-folder-open-o') }} {{ p.category_name }}</div>
                            <div class="card-body">
                                {% if p.latest_topics %}
                                    {% for s in p.latest_topics %}
                                        <div class="row">
                                            <div class="col-sm-1 text-center">
                                                {% if s.admin_id %}
                                                    {{ image('admin', s.admin_photo, s.admin_fname, 'img-fluid img-forum rounded-circle', FALSE) }}
                                                {% else %}
                                                    {% if config_enabled('sts_site_enable_user_profiles') %}
                                                        <a href="{{ site_url('profile/'~s.username) }}">
                                                            {{ image('forum', s.profile_photo, s.member_fname, 'img-fluid img-forum rounded-circle', FALSE) }}
                                                        </a>
                                                    {% else %}
                                                        {{ image('forum', s.profile_photo, s.member_fname, 'img-fluid img-forum rounded-circle', FALSE) }}
                                                    {% endif %}
                                                {% endif %}
                                            </div>
                                            <div class="col-sm-11">
                                                <br class="d-sm-none"/>
                                                <span class="topic-title">
                                                    <a href="{{ page_url('forum', s) }}">{{ s.title }}</a>
                                                </span>
                                                <div class="box-meta">
                                                        <ul class="list-inline">
                                                            <li>{{ i('fa fa-user') }} {{ lang('by') }}
                                                                {% if s.admin_id %}
                                                                    {{ s.admin_fname }}
                                                                {% else %}
                                                                    {{ s.member_fname }}
                                                                {% endif %}
                                                            </li>
                                                            <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(p.date_added) }}</li>
                                                            <li>{{ i('fa fa-eye') }} {{ check_plural(p.views, lang('view')) }}</li>
                                                            <li> {{ i('fa fa-comments') }} {{ s.replies }}</li>
                                                        </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <hr/>
                                    {% endfor %}
                                    <div class="row">
                                        <div class="col-md-12 text-sm-right">
                                            <a href="{{ site_url(forum_uri~'/topics/'~p.category_url) }}"
                                               class="btn btn-sm btn-secondary">
                                                {{ i('fa fa-angle-double-right') }} {{ lang('view_more_discussions') }}</a>
                                        </div>
                                    </div>
                                {% endif %}
                            </div>
                        </div>
                    {% endfor %}
                {% else %}
                    <div class="alert alert-secondary">
                        {{ lang('no_topics_found') }}
                    </div>
                {% endif %}
            </div>
            {% if layout_design_forum_sidebar == 'right' %}
                {% include ('support/forum_sidebar.tpl') %}
            {% endif %}
        </div>
    </div>
{% endblock content %}