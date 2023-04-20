<!DOCTYPE html>
{% block open_html_tag %}
<html lang="{{ default_language_code }}">
{% endblock %}
<head>
    {{ sts_site_google_analytics }}
    {% block head %}
        <title>{% block title %}{{ layout_design_site_name }}{% endblock %}</title>
        {% block meta %}
            <meta charset="{{ sts_content_document_charset }}">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="apple-mobile-web-app-capable" content="yes"/>

            <meta name="description" content="{% block meta_description %}{{ layout_design_default_meta_description }}{% endblock meta_description %}"/>
            <meta name="keywords" content="{% block meta_keywords %}{{ layout_design_default_meta_keywords }}{% endblock meta_keywords %}"/>
            <meta name="robots" content="{% block meta_robots %}index, follow{% endblock meta_robots %}"/>
            <meta name="generator" content="JROX.COM" />
            <meta name="publisher" content="JROX.COM {{ system_name }}" />
            <meta name="author" content="{{ sts_site_name }}" />
            <meta name="theme-color" content="#343a40" />
            <meta name="msapplication-navbutton-color" content="#343a40">
            <meta name="apple-mobile-web-app-status-bar-style" content="#343a40">

            <noscript><meta http-equiv="refresh" content="0; url={{ base_folder_path('javascript_required') }}"></noscript>
        {% endblock meta %}

        {% block meta_property %}
        <meta property="og:url" content="{{ current_url() }}"/>
        <meta property="og:type" content="website"/>
        <meta property="og:title" content="{{ sts_site_name }}"/>
        <meta property="og:description" content="{{ layout_design_default_meta_description }}"/>
        {% endblock meta_property %}
        <link rel="canonical" href="{{ current_url() }}" />
        <link rel="alternate" type="application/rss+xml" title="{{ sts_site_name }} {{ lang('feed') }}"
              href="{{ base_url }}rss"/>

        {% block css %}
            {% include 'js/fonts.tpl' %}
            {% include 'js/css.tpl' %}
                <style>
                {% block meta_data %}{% endblock meta_data %}
                {{ layout_design_custom_css }}
            </style>
        {% endblock css %}

        {% block favicon %}
            <link rel="icon" href="{{ layout_design_site_favicon }}" />
        {% endblock favicon %}

        {% block javascript_header %}
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        {{ layout_design_meta_header_info }}
        {% endblock javascript_header %}
    {% endblock head %}
</head>
{% block start_body %}
{% block body_tag %}<body class="main_body">{% endblock body_tag %}
{% endblock start_body %}
    {% block body %}{% endblock body %}
{% block end_body %}
</body>
{% endblock end_body %}
{{ enable_debug() }}
</html>

<!-- {{ app_revision }} -->
<!-- eCommerce Shopping Cart Powered By JROX.COM -->