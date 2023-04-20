{% extends "global/meta.tpl" %}
{% block title %}{{ lang('website_offline')|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('website_offline') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('website_offline') }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block start_body %}
<body role="document" class="bg-light">
{% endblock start_body %}
{% block body %}
    <div class="body">
        <div role="page" class="page">
            <div role="content" id="main" class="main">
                {% block container %}
                <br />
                    <div class="container">
                        {% block content %}
                            <div class="row">
                                <div class="col-md-8 offset-md-2">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            {% if layout_design_site_logo %}
                                                <p class="card-text">
                                                    <a href="{{ site_url() }}">
                                                        <img src="{{ layout_design_site_logo }}"
                                                             alt="{{ lang('logo') }}"
                                                             class="img-center"/>
                                                    </a>
                                                </p>
                                            {% else %}
                                                <h1>{{ sts_site_name }}</h1>
                                            {% endif %}

                                            <div class="jumbotron">
                                                <h1 class="display-4">{{ lang('offline_mode') }}</h1>
                                                <p class="lead"> {{ lang('offline_mode_description') }}</p>
                                                <hr class="m-y-2">
                                                <p class="lead text-md-right">
                                                    <a href="mailto:{{ config_option('sts_site_email') }}" class="btn btn-primary btn-lg role="button">
                                                        {{ i('fa fa-envelope') }} {{ lang('contact_us') }}</a>
                                                </p>
                                            </div>
                                            <p class="card-text text-md-center">
                                              <small class="text-muted">&copy; {{ 'now'|date('Y') }} {{ sts_site_name }} {{ lang('all_rights_reserved') }}
                                              </small>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-center"><small>{{ poweredby }}</small></div>
                                </div>
                            </div>
                        {% endblock content %}
                    </div>
                {% endblock container %}
            </div>
            <!-- /.content -->
        </div>
        <div id="loading" class="spinner">{{ i('fa fa-spinner fa-pulse') }}</div>
        <noscript>
            <h4 class="text-center">{{ lang('please_enable_javascript') }}</h4>
        </noscript>
    </div>
    <!-- /.body -->
{% endblock body %}

