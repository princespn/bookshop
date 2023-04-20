{% extends "global/base.tpl" %}
{% block title %} {{ parent() }} {{ lang('blog') }}{% endblock %}
{% block meta_description %}{{ parent() }} blog{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }}, blog{% endblock meta_keywords %}
{% block page_header %}
<div id="blog-grid-header" class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">
                {{ lang('blog') }}
                {% if category_name %}
                <span class="float-right">{{ category_name }}</span>
                {% endif %}
            </h2>
        </div>
    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="blog-grid">
    {{ breadcrumb }}
    {% if posts %}
    <div class="row">
        <div class="col-md-12">
            <div class="items">
                <div class="scroll">
                    {% for p in posts %}
                    {% if check_drip_feed(p) %}
                    <div class="item col-md-4">
                        <div class="card mb-3">
                            <figure class="gallery-item">
                                {{ image('blogs', p.overview_image, p.title, 'img-fluid', TRUE) }}
                                <figcaption class="hover-box">
                                    <h5>
                                        {% if sts_content_disqus_shortname %}
                                        <a href="{{ page_url('blog', p) }}#disqus_thread"
                                           class="btn btn-sm btn-secondary">
                                            {{ i('fa fa-comment-o') }}</a>
                                        {% else %}
                                        {% if p.comments %}
                                        <a href="{{ page_url('blog', p) }}#comments"
                                           class="btn btn-sm btn-secondary">
                                            {{ p.comments }} {{ i('fa fa-comment-o') }}</a>
                                        {% endif %}
                                        {% endif %}
                                        <a href="{{ page_url('blog', p) }}"
                                           class="btn btn-primary btn-sm item-details">
                                            {{ i('fa fa-search') }}
                                        </a>
                                    </h5>
                                </figcaption>
                            </figure>
                            <div class="card-body gallery-text">
                                <h5><a href="{{ page_url('blog', p) }}">{{ p.title }}</a></h5>
                                <div class="box-meta">
                                    <ul class="list-inline">
                                        <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }}
                                             {{ display_date(p.date_published) }}
                                        </li>
                                        {% if p.category_id %}
                                        <li>{{ i('fa fa-folder-open-o') }}
                                            {{ lang('in') }} <a
                                                    href="{{ site_url }}blog/category/{{ p.category_id }}-{{ url_title(p.category_name) }}">{{ p.category_name }}</a>
                                        </li>
                                        {% endif %}
                                    </ul>
                                </div>
                                <p>{{ parse_text(p.overview) }}</p>
                                <p class="text-right">
                                    <a href="{{ page_url('blog', p) }}" class="btn btn-outline-secondary">
                                        {{ lang('read_more') }} {{ i('fa fa-angle-double-right') }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                    {% endif %}
                    {% endfor %}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">{% include ('global/pagination.tpl') %}</div>
    </div>
    {% else %}
    <div role="alert" class="alert alert-info">
        <h4>{{ lang('no_posts_found') }}</h4>
        <p>{{ lang('no_blog_posts_found') }}</p>
    </div>
    {% endif %}
</div>
{% endblock content %}
{% block javascript_footer %}
{{ parent() }}
<script src="{{ base_url('js/wow/wow.min.js') }}"></script>
<script src="{{ base_url('js/masonry/masonry.js') }}"></script>
<script src="{{ base_url('js/masonry/imagesloaded.js') }}"></script>
<script>
    //Initiat WOW JS
    new WOW().init();
    $(document).ready(function () {
        var $container = $('.items');

        $container.imagesLoaded(function () {
            $container.masonry({
                itemSelector: '.item',
                columnWidth: '.col-md-4',
                transitionDuration: 0
            });
        });
    });

    {% if config_enabled('layout_design_blogs_enable_tag_cloud') %}
    $(document).ready(function () {
        $('#blog-tags').fadeOut('slow', function () {
            $('#blog-tags').load('{{ base_url }}blog/tags/?q=ajax');
            $('#blog-tags').fadeIn('300');
        });
    });
    {% endif %}
</script>
{% if sts_content_enable_comments == 2 %}
<script id="dsq-count-scr" src="//{{ sts_content_disqus_shortname }}.disqus.com/count.js" async></script>
{% endif %}
{% endblock javascript_footer %}