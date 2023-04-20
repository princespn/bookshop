{% extends "global/base.tpl" %}
{% block title %}{{ p.meta_title }} {% endblock %}
{% block meta_description %}{{ p.meta_description }}  {% endblock meta_description %}
{% block meta_keywords %}{{ p.meta_keywords }}  {% endblock meta_keywords %}
{% block css %}
{{ parent() }}
<link href="{{base_url}}js/prism/prism.css" rel="stylesheet" type="text/css"/>
{% endblock css %}
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
            <h3>{{ p.kb_title }}</h3>
            <div class="box-meta">
                <ul class="list-inline">
                    <li>{{ i('fa fa-file-text-o') }} {{ lang('created_on') }} {{ display_date(p.date_modified) }}
                        {{ lang('filed_under') }}
                        <a href="{{ site_url }}{{kb_uri}}/category/{{ p.category_id }}-{{ url_title(p.category_name) }}">
                            {{ p.category_name }}</a>
                    </li>
                </ul>
            </div>
            <hr/>
            <div class="kb-body">
                {{ p.kb_body }}
            </div>
            {% if p.kb_videos %}
            <h3>{{ lang('videos') }}</h3>
            {% for v in p.kb_videos %}
            <div class="embed-responsive embed-responsive-16by9">
                {{ html_entity_decode(v.video_code) }}
            </div>
            <br/>
            {% endfor %}
            {% endif %}
            {% if p.kb_downloads %}
            <hr/>
            <p class="text-md-right">
                {% for k in p.kb_downloads %}
                <span class="badge">
                    <a href="{{ site_url() }}{{kb_uri}}/download/{{ k.file_name }}/{{ id }}"
                       class="name">
                        {{ i('fa fa-download') }} {{ k.download_name }}</a>
                    </span>
                {% endfor %}
            </p>
            {% endif %}
            {% if p.paginate.rows %}
            <div class="row">
                <div class="col-md-12" id="site_pagination">
                    <ul class="text-capitalize pagination justify-content-center">
                        {{ p.paginate.rows }}
                    </ul>
                </div>
            </div>
            {% endif %}
        </div>

    {% if layout_design_kb_sidebar == 'right' %}
    {% include ('support/kb_sidebar.tpl') %}
    {% endif %}

</div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
    <script src="{{base_url}}js/prism/prism.js" async defer></script>
{% endblock javascript_footer %}