{% extends "global/base.tpl" %}
{% block title %}{{ lang('add_discussion_topic') }}{% endblock %}
{% block meta_description %}{{ lang('discussion_topic') }} {% endblock meta_description %}
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
            {% if layout_design_forum_sidebar == 'left' %}
                {% include ('support/forum_add_topic_sidebar.tpl') %}
            {% endif %}
            <div class="col-md-{% if layout_design_forum_sidebar == 'none' %}12{% else %}9{% endif %}">
                <div class="topic-reply row">
                    <div class="col-md-12">
                        <div class="add-comment card">
                            <div class="card-body">
                                <h5>{{ i('fa fa-pencil') }} {{ lang('new_discussion_topic') }}</h5>
                                <hr/>
                                {% if member_logged_in == 1 %}
                                    {{ form_open(forum_uri~'/add_topic/', 'id="form"') }}
                                    <div class="row">
                                        <div class="col-md-8">
                                            <fieldset class="form-group">
                                                <input type="text" name="title"
                                                       class="form-control required"
                                                       placeholder="{{ lang('your_topic_subject') }}">
                                            </fieldset>
                                        </div>
                                        <div class="col-md-4">
                                            <fieldset class="form-group">
                                                {{ form_dropdown('category_id', options('forum_categories'), category.category_id, 'id="category_id" class="form-control required"') }}
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <fieldset class="form-group">
                                                            <textarea name="topic" rows="10" id="topic"
                                                                      class="form-control required"
                                                                      placeholder="{{ lang('type_your_issue') }}"></textarea>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-7">
                                    {% if config_enabled('sts_form_enable_forum_captcha') %}
                                            <div class="g-recaptcha" data-sitekey="{{ sts_form_captcha_key }}"></div>
                                    {% endif %}
                                        </div>
                                        <div class="col-md-5">
                                            {% if config_enabled('sts_form_enable_forum_captcha') %}
                                            {{ form_dropdown('', options('bbcode_tags'), category.category_id, 'class="form-control"') }}
                                            {% endif %}
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-lg btn-primary">{{ i('fa fa-refresh') }} {{ lang('submit') }}</button>
                                        </div>
                                    </div>
                                    {{ form_hidden('member_id', sess('member_id')) }}
                                    {{ form_close() }}
                                {% else %}
                                    <div class="alert alert-info">
                                        <p>
                                            {{ i('fa fa-info-circle') }} {{ lang('account_login_required_to_comment') }}
                                        </p>

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
                {% include ('support/forum_add_topic_sidebar.tpl') %}
            {% endif %}
        </div>
    </div>


{% endblock content %}
 {% block javascript_footer %}
     {{ parent() }}
     {% if member_logged_in == 1 %}
         {{ include('js/default_form_js.tpl') }}
     {% endif %}
 {% endblock javascript_footer %}