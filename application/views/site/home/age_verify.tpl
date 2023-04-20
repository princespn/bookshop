{% extends "global/meta.tpl" %}
{% block title %}{{ lang('verify_your_age')|capitalize }}{% endblock %}
{% block meta_description %}{{ lang('verify_your_age') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('verify_your_age') }}{% endblock meta_keywords %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block start_body %}
<body role="document" class="bg-light">
{% endblock start_body %}
{% block body %}
<div class="body">
    <div class="page">
        <div role="content" id="main" class="main">
            {% block container %}
            <br/>
            <div class="container">
                {% block content %}
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-body text-md-center">
                                {{ form_open('', 'id="contact-form"') }}
                                {% if layout_design_site_logo %}
                                <p class="card-text text-md-center">
                                    <a href="{{ site_url() }}">
                                        <img src="{{ layout_design_site_logo }}"
                                             alt="{{ lang('logo') }}"
                                             class="img-center"/>
                                    </a>
                                </p>
                                {% else %}
                                <h1>{{ sts_site_name }}</h1>
                                {% endif %}

                                <div class="jumbotron white">
                                    <h1 class="display-4">{{ lang('age_verification_required') }}</h1>
                                    <hr class="m-y-2">
                                    <p class="lead"> {{ lang('website_is_age_restricted') }} {{ lang('please_verify_your_age') }}</p>

                                    <p class="lead">
                                        {{ form_hidden('agree', TRUE) }}
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            {{ i('fa fa-chevron-right') }} {{ lang('i_am_over') }} {{ config_option('sts_site_restrict_age_limit') }}
                                        </button>
                                        <a href="http://www.google.com" class="btn btn-secondary btn-lg">
                                            {{ i('fa fa-remove') }} {{ lang('i_am_under') }} {{ config_option('sts_site_restrict_age_limit') }}
                                        </a>
                                    </p>
                                </div>
                                <p class="card-text">
                                    <small class="text-muted">&copy; {{ 'now'|date('Y') }} {{ sts_site_name }} {{ lang('all_rights_reserved') }}
                                    </small>
                                </p>
                                {{ form_close() }}
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

