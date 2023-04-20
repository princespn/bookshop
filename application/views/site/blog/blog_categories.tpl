{% extends "global/base.tpl" %}
{% block title %} {{ parent() }} {{ lang('blog_categories') }}{% endblock %}
{% block meta_description %}{{ parent() }} blog{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }}, blog{% endblock meta_keywords %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">
                {{ lang('blog_categories') }}
            </h2>
        </div>
    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="blog-list">
    {{ breadcrumb }}
    {% if blog_categories %}
    {% if blog_categories|length > 1 %}
    <div class="card">
        <div class="card-body">
            <h5>{{ lang('categories') }}</h5>
            <hr />
            {% for c in blog_categories %}
            <span class="pr-3"><a href="{{ site_url }}blog/category/{{ c.category_id }}-{{ url_title(c.category_name) }}">
                                    {{ i('fa fa-folder-o') }} {{ c.category_name }} {% if c.total > 0 %}({{c.total}}){%endif%}</a></span>
            {% endfor %}
        </div>
    </div>
    {% endif %}
    {% endif %}
</div>
{% endblock content %}

