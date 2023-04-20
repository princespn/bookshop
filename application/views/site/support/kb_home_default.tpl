{% extends "global/base.tpl" %}
{% block title %}{{ parent() }} {{ lang('knowledgebase') }}{% endblock %}
{% block meta_description %}{{ parent() }} {{ lang('knowledgebase') }} {% endblock meta_description %}
{% block meta_keywords %}{{ parent() }} {{ lang('knowledgebase') }} {% endblock meta_keywords %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('knowledgebase') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="kb-list">
        {{ breadcrumb }}
        <div class="row">
            {% if layout_design_kb_sidebar == 'left' %}
                {% include ('support/kb_sidebar.tpl') %}
            {% endif %}
            <div class="col-md-{% if layout_design_kb_sidebar == 'none' %}12{% else %}9{% endif %}">
                <div class="row">
                    <div class="col-md-7">
                    <h2>{{ lang('featured_articles') }}</h2>
                    </div>
                    <div class="col-md-5">
                        {% if layout_design_kb_sidebar == 'none' %}
                        {{ form_open(kb_uri~'/search', 'method="get" id="search-form" class="form-horizontal"') }}
                        <div class="input-group">
                            <input type="text" name="search_term" class="form-control" placeholder="{{ lang('search_for') }}...">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="submit">{{ lang('go') }}</button>
                            </div>
                        </div>
                        {{ form_close() }}
                        {% endif %}
                    </div>
                </div>
                <hr/>
                {% if articles %}
                    <div class="kb-articles">
                        {% for p in articles %}
                            <h5><a href="{{ site_url }}{{kb_uri}}/article/{{ p.url }}">{{ p.kb_title }}</a></h5>
                            <div class="box-meta">
                                <ul class="list-inline">
                                    <li>
                                        {{ i('fa fa-file-text-o') }} {{ lang('created_on') }} {{ display_date(p.date_modified) }} {{ lang('filed_under') }}
                                            <a href="{{ site_url }}{{kb_uri}}/category/{{ p.category_id }}-{{ url_title(p.category_name) }}">{{ p.category_name }}</a>

                                    </li>
                                </ul>
                            </div>
                            <p>{{ p.overview }}</p>
                            <hr/>
                        {% endfor %}
                    </div>
                {% else %}
                    <div role="alert" class="alert alert-info">
                        {{ lang('no_knowledgebase_articles_found') }}
                    </div>
                {% endif %}
            </div>
            {% if layout_design_kb_sidebar == 'right' %}
                {% include ('support/kb_sidebar.tpl') %}
            {% endif %}
        </div>
    </div>
{% endblock content %}