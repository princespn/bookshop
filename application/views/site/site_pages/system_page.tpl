{% extends "global/base.tpl" %}
{% block title %}{% if row.meta_title %}{{ row.meta_title }}{% else %}{{ layout_design_site_name }}{% endif %}{% endblock %}
{% block meta_description %}{% if row.meta_description %}{{ row.meta_description }}{% else %}{{ layout_design_default_meta_description }}{% endif %}{% endblock meta_description %}
{% block meta_keywords %}{{ row.meta_keywords }}{% endblock meta_keywords %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{% if row.meta_title %}{{ row.meta_title }}{% else %}{{ title }}{% endif %}</h2>
            </div>
        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="blog-content">
        <div class="container">
            {{ breadcrumb }}
            <div class="row">
                <div class="col-md-12">
                    <div class="page-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{ row.page_content }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock content %}