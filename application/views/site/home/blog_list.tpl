{% extends "global/base.tpl" %}
{% block container %}
    {% if row.slide_shows %}
        {% include ('global/slideshows.tpl') %}
    {% endif %}
    <div class="blog-list container">
        <div class="row">
            <div class="col-md-12">
                {% if row.blogs %}
                    <div class="widget" data-type="widget" data-function="latest_blog_posts">
                        <div id="latest-blog-posts" class="container section">
                            <h1 class="headline text-sm-center">{{ lang('latest_blog_posts') }}</h1>
                            <div class="items">
                                {% for p in row.blogs %}
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="thumbnail">
                                                {{ image('blogs', p.overview_image, p.title, 'img-fluid', TRUE) }}
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <h5>
                                                <a href="{{ page_url('blog', p) }}">{{ p.title }}</a>
                                            </h5>
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
                                            <p>
                                                <a href="{{ page_url('blog', p) }}" class="btn btn-outline-secondary">
                                                    {{ lang('read_more') }} {{ i('fa fa-angle-double-right') }}
                                                </a>
                                            </p>
                                            {% if p.tags %}
                                            <small>{{ lang('tags') }}:</small> {{ format_tags(p.tags, 'badge badge-light') }}
                                            {% endif %}
                                        </div>
                                    </div>
                                    <hr/>
                                {% endfor %}
                            </div>
                        </div>
                    </div>
                {% endif %}
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-12 text-sm-center">
                <p class="lead">
                    <a href="blog" class="btn btn-lg btn-secondary">
                        {{ i('fa fa-caret-right') }} {{ lang('go_to_blog') }}</a>
                </p>
            </div>
        </div>
    </div>
{% endblock container %}
{% block javascript_footer %}
    {{ parent() }}
    <script src="{{ base_url('js/wow/wow.min.js') }}"></script>
    <script src="{{ base_url('js/owl-carousel/owl.carousel.min.js') }}"></script>
    {{ row.footer_data }}
{% endblock javascript_footer %}