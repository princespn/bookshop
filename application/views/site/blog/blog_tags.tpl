{% extends "global/base.tpl" %}
{% block title %} {{ parent() }} {{ lang('blog_tags') }}{% endblock %}
{% block meta_description %}{{ parent() }} blog{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }}, blog{% endblock meta_keywords %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">
                {{ lang('blog_tags') }}
            </h2>
        </div>
    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="blog-list">
    {{ breadcrumb }}
    {% if tags %}
    <div class="card">
        <div class="card-body">
            <h5>{{ lang('tags') }}</h5>
            <hr/>
            {% for p in tags %}
            <span class="badge"><a href="{{ site_url }}blog/tag/{{ p.tag }}"
                                   class="name">{{i('fa fa-tag')}} {{ p.tag }}</a>
                <span class="total">{{ kmbt(p.count) }}</span></span>
            {% endfor %}
        </div>
    </div>
    {% endif %}
</div>
{% endblock content %}

