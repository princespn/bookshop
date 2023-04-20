{% extends "global/base.tpl" %}
{% block open_html_tag %}
<html lang="{{ default_language_code }}" itemscope itemtype="http://schema.org/Article">
{% endblock %}
{% block title %}{{ p.meta_title }}{% endblock %}
{% block meta_description %}{{ p.meta_description }}{% endblock meta_description %}
{% block meta_keywords %}{{ p.meta_keywords }}{% endblock meta_keywords %}
{% block meta_property %}
<!-- Google+ -->
<meta itemprop="name" content="{{ p.meta_title }}">
<meta itemprop="description" content="{{ p.meta_description }}">
<meta itemprop="image" content="{{ p.overview_image }}">

<!-- Open Graph -->
<meta property="og:url" content="{{ current_url() }}"/>
<meta property="og:type" content="article"/>
<meta property="og:title" content="{{ p.title }}"/>
<meta property="og:description" content="{{ p.overview }}"/>
<meta property="og:image" content="{{ p.overview_image }}"/>
<meta property="og:site_name" content="{{ sts_site_name }}"/>

{% endblock meta_property %}
{% block css %}
{{ parent() }}
<link href="{{base_url}}js/prism/prism.css" rel="stylesheet" type="text/css"/>
{% endblock css %}
{% block start_body %}
{{ parent() }}
<div id="fb-root"></div>
{% if sts_content_enable_comments == '3' %}
<script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v4.0&appId={{sts_content_facebook_comments_app_id}}&autoLogAppEvents=1"></script>
{% else %}
<script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
{% endif %}
{% endblock start_body %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang('blog') }}</h2>
        </div>

    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="blog-post">
    {{ breadcrumb }}
    <div class="row">
        {% if layout_design_blog_page_sidebar == 'left' %}
        {% include ('blog/blog_post_sidebar.tpl') %}
        {% endif %}
        <div class="col-md-{% if layout_design_blog_page_sidebar == 'none' %}12{% else %}8{% endif %}">
            <div class="row">
            <div class="col-md-12 text-sm-center">
                {% if p.video_code %}
                <div class="blog-header-image embed-responsive embed-responsive-16by9">
                    {{ html_decode(p.video_code) }}
                </div>
                {% else %}
                {% if p.blog_header %}
                {{ image('blog', p.blog_header, p.title, 'blog-header-image img-fluid img-thumbnail', FALSE) }}
                {% endif %}
                {% endif %}
            </div>
            </div>
            <h2 class="blog-title">{{ p.title }}</h2>
            <hr/>
            <div class="box-meta">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-meta">
                            <ul class="list-inline">
                                <li>
                                    <div class="fb-like" data-href="{{ current_url() }}"
                                         data-layout="button_count" data-action="like"
                                         data-show-faces="false">
                                    </div>
                                </li>
                                <li>{{ i('fa fa-user') }} {{ lang('by') }} {{ p.author }}</li>
                                <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(p.date_published) }}
                                </li>
                                {% if p.category_id %}
                                <li>{{ i('fa fa-folder-open-o') }}
                                    {{ lang('in') }}
                                    <a href="{{ site_url }}blog/category/{{ p.category_id }}/{{ url_title(p.category_name) }}">
                                        {{ p.category_name }}</a>
                                </li>
                                {% endif %}
                                {% if sts_content_enable_comments  == '1' %}
                                <li>
                                    {{ i('fa fa-comment-o') }}
                                    <a href="{{ page_url('blog', p) }}#comments">
                                        {{ p.comments }} {{ lang('comments') }}</a>
                                </li>
                                {% else %}
                                {% if p.comments %}
                                <li>{{ i('fa fa-comment-o') }}
                                    <a href="{{ page_url('blog', p) }}#disqus_thread"
                                       class="label label-default"></a></li>
                                {% endif %}
                                {% endif %}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            {% if check_blog_permissions(p) == false %}
            <p>{{ p.overview }}</p>
            <hr/>
            <div class="alert alert-info" role="alert">
                <h5 class="alert-heading">{{ i('fa fa-info-circle') }} {{ lang('login_required') }}</h5>
                <p>{{ lang('user_login_permissions_required') }}. {{ lang('please_login') }}</p>
            </div>
            {% else %}
            <div class="blog-body">
                <div class="row">
                    <div class="col-md-12">
                        <article id="blog-text" style="overflow: hidden">
                            {{ parse_text(p.body) }}
                        </article>
                    </div>
                </div>
                {% if p.paginate.rows %}
                <div class="row">
                    <div class="col-md-12" id="site_pagination">
                        <ul class="text-capitalize pagination justify-content-center">
                            {{ p.paginate.rows }}
                        </ul>
                    </div>
                </div>
                {% endif %}
                {% if (p.blog_downloads) %}
                <hr/>
                <p class="text-md-right">
                    {% for s in  p.blog_downloads %}
                    <span class="badge">
                                        <a href="{{ site_url() }}/blog/download/{{ s.file_name }}/{{ id }}"
                                           class="name">
                                            {{ i('fa fa-download') }} {{ s.download_name }}</a>
                                        </span>
                    {% endfor %}
                </p>
                {% endif %}
            </div>
            <div class="next-posts">
                <div class="row">
                    <div class="col-6">
                        {% if p.previous %}
                        <a href="{{ site_url('blog/post/'~p.previous.url) }}"
                           class="btn btn-sm btn-outline-secondary">
                            {{ i('fa fa-angle-double-left') }} {{ lang('previous_post') }}
                        </a>
                        {% endif %}
                    </div>
                    <div class="col-6 text-right">
                        {% if p.next %}
                        <a href="{{ site_url('blog/post/'~p.next.url) }}" class="btn btn-sm btn-outline-secondary">
                            {{ lang('next_post') }} {{ i('fa fa-angle-double-right') }}
                        </a>
                        {% endif %}
                    </div>
                </div>
            </div>
            <div class="share-post min-pad-bottom">
                <div class="row">
                    <div class="col-md-12 text-sm-right">
                        <small>{{ lang('share_this_post') }}</small>
                        {{ html_decode(config_option('sts_site_refer_friend_code')) }}
                    </div>
                </div>
            </div>
            <hr/>
            {% include('blog/blog_related_articles.tpl') %}
            {% include('blog/blog_comments.tpl') %}
            {% endif %}
        </div>
        {% if layout_design_blog_page_sidebar == 'right' %}
        {% include ('blog/blog_post_sidebar.tpl') %}
        {% endif %}
    </div>
</div>
{% endblock content %}
{% block javascript_footer %}
{{ parent() }}
{% if config_enabled('layout_design_continue_reading_button') %}
<script src="{{ base_url('js/readmore/readmore.min.js') }}"></script>
<script>
    $('#blog-text').readmore({
        speed: 75,
        maxHeight: 200,
        moreLink:'<p class="text-center"><a href="#" class="btn btn-sm btn-light">{{lang ('continue_reading') }}</a></p>',
        lessLink:'',
    });
</script>
{% endif %}
<script src="{{ base_url('js/wow/wow.min.js') }}"></script>
<script src="{{ base_url('js/owl-carousel/owl.carousel.min.js') }}"></script>

{% if sts_content_enable_comments == 2 %}
<script id="dsq-count-scr" src="//{{ sts_content_disqus_shortname }}.disqus.com/count.js" async></script>
{% else %}
<script>
    $('#recent_articles').load('{{ base_url }}blog/recent');

    function load_comments() {
        var id = '#comments';
        $(id).fadeOut('slow', function () {
            $(id).load('{{ base_url }}blog/comments/{{ p.blog_id }}');
            $(id).fadeIn('slow');
        });
    }

    load_comments();

    $("#form").validate({
        ignore: "",
        submitHandler: function (form) {
            $.ajax({
                url: '{{ site_url('blog/add_comment/'~p.blog_id) }}',
                type: 'POST',
                dataType: 'json',
                data: $('#form').serialize(),
                success: function (response) {
                    if (response.type == 'success') {
                        $('.alert-danger').remove();
                        $('.form-control').removeClass('error');

                        setTimeout(function () {
                            $('.alert-msg').fadeOut('slow');
                        }, 5000);
                        {% if config_enabled('sts_form_enable_blog_captcha') %}
                        grecaptcha.reset();
                        {% endif %}
                        $('#comment').val('');
                        $('#response').html('{{ alert('success') }}');
                    } else {
                        $('#response').html('{{ alert('error') }}');
                    }

                    $('#msg-details').html(response.msg);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
        });
    }
    })
    ;
</script>
{% endif %}
<script>
    //Initiat WOW JS
    new WOW().init();

    $('.carousel-three').owlCarousel({
        loop: true,
        margin: 10,
        items: {% if config_option('layout_design_blog_page_sidebar') == 'none' %} 4 {% else %} 3 {% endif %},
        responsiveClass: true,
        slideSpeed: 200,
        paginationSpeed: 800,
        rewindSpeed: 1000,
        autoPlay: true,
        stopOnHover: true
    })
</script>
<script src="{{base_url}}js/prism/prism.js" async defer></script>
{% endblock javascript_footer %}