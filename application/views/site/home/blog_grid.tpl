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
                        <div class="item col-md-4">
                            <div class="card">
                                <figure class="gallery-item">
                                    {{ image('blogs', p.overview_image, p.title, 'img-fluid', TRUE) }}
                                    <figcaption class="hover-box">
                                        <h5>
                                            {% if sts_content_disqus_shortname %}
                                            <a href="{{ page_url('blog', p) }}#disqus_thread"
                                               class="btn btn-sm btn-secondary">{{ i('fa fa-comment-o') }}</a>
                                            {% else %}
                                            {% if p.comments %}
                                            <a href="{{ page_url('blog', p) }}#comments"
                                               class="btn btn-sm btn-secondary">{{ p.comments }} {{ i('fa fa-comment-o') }}</a>
                                            {% endif %}
                                            {% endif %}
                                            <a href="{{ page_url('blog', p) }}"
                                               class="btn btn-primary btn-sm item-details">{{ i('fa fa-search') }}</a>
                                        </h5>
                                    </figcaption>
                                </figure>
                                <div class="card-body gallery-text">
                                    <h5><a href="{{ page_url('blog', p) }}">{{ p.title }}</a></h5>
                                    <div class="box-meta">
                                        <ul class="list-inline">
                                            <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(p.date_published) }}
                                            </li>
                                            {% if p.category_id %}
                                            <li>{{ i('fa fa-folder-open-o') }}
                                                {{ lang('in') }} <a
                                                        href="{{ site_url }}blog/category/{{ p.category_id }}/{{ url_title(p.category_name) }}">{{ p.category_name }}</a>
                                            </li>
                                            {% endif %}
                                        </ul>
                                    </div>
                                    <p>{{ parse_text(p.overview) }}</p>
                                </div>
                            </div>
                        </div>
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
                <a href="blog"
                   class="btn btn-lg btn-secondary">{{ i('fa fa-caret-right') }} {{ lang('go_to_blog') }}</a>
            </p>
        </div>
    </div>
</div>
{% endblock container %}
{% block javascript_footer %}
{{ parent() }}
<script src="{{ base_url('js/wow/wow.min.js') }}"></script>
<script src="{{ base_url('js/owl-carousel/owl.carousel.min.js') }}"></script>
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
</script>
{{ row.footer_data }}
{% endblock javascript_footer %}