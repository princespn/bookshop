{% extends "global/base.tpl" %}
{% block title %} {{ parent() }} {{ lang('product_tags') }}{% endblock %}
{% block meta_description %}{{ parent() }} product{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }}, product{% endblock meta_keywords %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">
                    {{ lang('product_tags') }}
                </h2>
            </div>
        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="product-tag-list">
        {{ breadcrumb }}
        {% if tags %}
            <div class="card">
                <div class="card-body">
                    <h5>{{ lang('product_tags') }}</h5>
                    <hr/>
                    {% for p in tags %}
                        <span class="badge"><a href="{{ site_url }}product/tag/{{ p.tag }}"
                                               class="name">{{ p.tag }}</a>
                <span class="total">{{ kmbt(p.count) }}</span></span>
                    {% endfor %}
                </div>
            </div>
        {% endif %}
    </div>
{% endblock content %}

