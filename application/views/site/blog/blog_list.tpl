{% extends "global/base.tpl" %}
{% block title %}{% if category_name %}{{ category_name }}{% else %}{{ lang('blog') }}{% endif %}{% endblock %}
{% block meta_description %}{{ parent() }} {{ lang('blog') }}{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }},{% if category_name %}{{ category_name }}{% endif %}{% endblock meta_keywords %}
{% block page_header %}
    <div id="blog-list-header" class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">
                    {% if search_term %}
                    {{ lang('search') }} - {{ search_term }}
                    {% else %}
                    {{ lang('blog') }}
                    {% endif %}
                </h2>
            </div>
        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="blog-list">
        {{ breadcrumb }}
        <div class="row">
            {% if layout_design_blog_page_sidebar == 'left' %}
                {% include ('blog/blog_sidebar.tpl') %}
            {% endif %}
            <div class="col-md-{% if layout_design_blog_page_sidebar == 'none' %}12{% else %}8{% endif %}">
                {% if posts %}
                    <div class="posts">
                        <div class="scroll">
                            {% for p in posts %}
                                {% if check_drip_feed(p) %}
                                    <div class="post row">
                                        <div class="col-md-3">
                                            {% if p.overview_image %}
                                                {{ image('blog', p.overview_image, p.title, 'blog-header-image img-fluid img-thumbnail', TRUE) }}
                                            {% else %}
                                                <img src="{{ base_url }}images/no-photo.jpg"
                                                     alt="photo" class="d-none d-sm-none d-md-block blog-header-image img-fluid img-thumbnail"/>
                                            {% endif %}
                                        </div>
                                        <div class="col-md-9">
                                            <h2 class="blog-title">
                                                <a href="{{ page_url('blog', p) }}">{{ p.title }}</a>
                                            </h2>
                                            <hr class="d-block d-md-none"/>
                                            <div class="box-meta">
                                                <ul class="list-inline">
                                                    <li>{{ i('fa fa-user') }} {{ lang('by') }} {{ p.author }}</li>
                                                    <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(p.date_published) }}</li>
                                                    {% if p.category_id %}
                                                        <li>{{ i('fa fa-folder-open-o') }}
                                                            {{ lang('in') }}
                                                            <a href="{{ site_url }}blog/category/{{ p.category_id }}-{{ url_title(p.category_name) }}">{{ p.category_name }}</a>
                                                        </li>
                                                    {% endif %}
                                                    {% if sts_content_enable_comments  != '0' %}
                                                        <li>
                                                            <a href="{{ page_url('blog', p) }}#{% if sts_content_enable_comments  == '1' %}comments{% else %}disqus_thread{% endif %}">
                                                                {{ i('fa fa-comment-o') }}  {% if sts_content_enable_comments  == '1' %} {{ p.comments }} {{ lang('comments') }}{% endif %}</a>
                                                        </li>
                                                    {% endif %}
                                                </ul>
                                            </div>
                                            <p>{{ parse_text(p.overview) }}</p>
                                            <p class="text-right">
                                                <a href="{{ page_url('blog', p) }}" class="btn btn-sm btn-outline-secondary">
                                                    {{ lang('read_more') }} {{ i('fa fa-angle-double-right') }}
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <hr/>
                                {% endif %}
                            {% endfor %}
                            {% include ('global/pagination.tpl') %}
                        </div>
                    </div>
                {% else %}
                    <div role="alert" class="alert alert-secondary">
                        <p>{{ lang('no_blog_posts_found') }}</p>
                    </div>
                {% endif %}
            </div>
            {% if layout_design_blog_page_sidebar == 'right' %}
                {% include ('blog/blog_sidebar.tpl') %}
            {% endif %}
        </div>
    </div>
{% endblock content %}
{% block javascript_footer %}
    {{ parent() }}
{% if sts_content_enable_comments == 2 %}
<script id="dsq-count-scr" src="//{{ sts_content_disqus_shortname }}.disqus.com/count.js" async></script>
{% endif %}
    {% if config_enabled('layout_design_blogs_enable_tag_cloud') %}
        <script>
            $(document).ready(function () {
                $('#blog-tags').fadeOut('slow', function () {
                    $('#blog-tags').load('{{ base_url }}blog/tags');
                    $('#blog-tags').fadeIn('300');
                });
            });
        </script>
    {% endif %}
{% endblock javascript_footer %}