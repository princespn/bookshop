{% extends "global/meta.tpl" %}
{% block body %}
<div id="{{ page_id }}" class="body body-no-header">
    <div role="page" class="page">
        <div role="header" class="header_print">
            <div class="container">
                {% block header %}
                {% block logo %}
                <div class="row">
                    <div class="col-md-3">
                        <div class="logo">
                            {% if layout_design_site_logo %}
                            <a href="{{site_url()}}">
                                <img src="{{ layout_design_site_logo }}" alt="{{ lang('logo') }}"
                                     class="img-fluid"/>
                            </a>
                            {% endif %}
                        </div>
                    </div>
                    <div class="col-md-9">
                    </div>
                </div>
                {% endblock logo %}
                {% endblock header %}
            </div>
        </div>
        <!-- /.header -->

        <div role="content" id="main" class="main">
            <div id="response">
                {% if (sess('success')) %}
                {{ alert('success', sess('success')) }}
                {% elseif (sess('error')) %}
                {{ alert('error', sess('error')) }}
                {% endif %}
            </div>
            {% block container %}
                <div class="page-no-header">
                    {% block page_header %}{% endblock page_header %}
                </div>
            <div class="container">
                {% block content %}{% endblock content %}
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

