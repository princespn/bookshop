{% extends "global/base.tpl" %}
{% block title %}{{ lang(title) }}{% endblock %}
{% block meta_description %}{{ lang(title) }}{% endblock meta_description %}
{% block meta_robots %}noindex, nofollow{% endblock meta_robots %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang(header) }}</h2>
        </div>
    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="message">
    {{ breadcrumb }}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ lang(title) }}</h2>
                    <hr/>
                    {% if message %}
                    <p class="card-text">
                    <div class="alert alert-info">
                        {{ i('fa fa-info-circle') }}
                        {{ lang(message) }}
                    </div>
                    </p>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock content %}