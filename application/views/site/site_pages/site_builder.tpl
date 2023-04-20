{% extends "global/meta.tpl" %}
{% block title %}{{ row.meta_title }}{% endblock %}
{% block meta_description %}{{ row.meta_description }}{% endblock %}
{% block meta_keywords %}{{ row.meta_keywords }}{% endblock %}
{% block css %}
<link href="{{base_folder_path('site_builder/assets/minimalist-blocks/content.css')}}" rel="stylesheet" type="text/css" />
{% include 'js/fonts.tpl' %}
{% include 'js/css.tpl' %}
<link href="{{base_folder_path('site_builder/box/box.css')}}" rel="stylesheet" type="text/css" />
<style>
    {% block meta_data %}{% endblock meta_data %}
    {{ layout_design_custom_css }}
</style>
{% endblock css %}
{% block body %}
{% block body_tag %}<body class="{% if (row.enable_header) %}main_body{% endif %}">{% endblock body_tag %}
{% if (row.enable_header) %}
<div id="sidebar" class="d-md-none sidebar-offcanvas">
    {{ format_menu(top_menu, 'class="sidebar-list list-group list-group-flush"', TRUE) }}
    {% if config_enabled('layout_design_show_search_form') %}
    <div class="mt-3 mx-3">
        {{ form_open('search', 'method="get" id="top-search-form" class="form-horizontal"') }}
        <div class="input-group">
            <input type="text" name="search_term" class="form-control"
                   placeholder="{{ lang('search_for') }}...">
            <div class="input-group-append">
                <button class="btn btn-secondary"
                        type="submit">{{ lang('go') }}
                </button>
            </div>
        </div>
        {{ form_close() }}

    </div>
    {% endif %}
</div>
{% block top_nav %}
{{ include ('global/top_nav.tpl') }}
{% endblock top_nav %}
<div class="header d-none d-md-block">
    <div class="top-header">
        {% include ('global/top_menu.tpl') %}
        <div class="page-header d-none d-md-block">
            {% block page_header %}{% endblock page_header %}
        </div>
    </div>
</div>
<!-- /.header -->
{% endif %}

<div id="main" class="is-wrapper content main">
    <div id="response">
        {% if (sess('success')) %}
        {{ alert('success', sess('success')) }}
        {% elseif (sess('error')) %}
        {{ alert('error', sess('error')) }}
        {% elseif error %}
        {{ alert('error', error) }}
        {% endif %}
    </div>
    {% autoescape false %}
    {{ row.template_data }}
    {% endautoescape %}
</div>
<!-- /.content -->

<div class="clearfix"></div>
<div class="footer">
    {% if (row.enable_footer) %}
    <footer>
        {% include ('global/footer_menu.tpl') %}
    </footer>
    {% endif %}
    {% include ('global/privacy.tpl') %}
</div>
{% include ('form/login_modal.tpl') %}
{% block javascript_footer %}
{% include ('js/footer_js.tpl') %}
<script src="{{ base_url('js/wow/wow.min.js') }}"></script>
<script src="{{ base_url('js/owl-carousel/owl.carousel.min.js') }}"></script>
<script src="{{ base_url('js/masonry/masonry.js') }}"></script>
<script src="{{ base_url('js/masonry/imagesloaded.js') }}"></script>
<script src="{{ base_folder_path(site_builder_path~'/box/box.js') }}" type="text/javascript"></script>
<script>
    //Initiat WOW JS
    new WOW().init();

    $('.carousel-five').owlCarousel({
        loop: true,
        margin: 10,
        items: 5,
        responsiveClass: true,
        slideSpeed : 400,
        paginationSpeed : 800,
        rewindSpeed : 1000,
        autoPlay : true,
        stopOnHover : true
    })

    $('.carousel-four').owlCarousel({
        loop: true,
        margin: 10,
        items: 4,
        responsiveClass: true,
        slideSpeed : 200,
        paginationSpeed : 800,
        rewindSpeed : 1000,
        autoPlay : true,
        stopOnHover : true
    })

    $('.carousel-three').owlCarousel({
        loop: true,
        margin: 10,
        items: 3,
        responsiveClass: true,
        slideSpeed : 300,
        paginationSpeed : 400,
        rewindSpeed : 500,
        autoPlay : true,
        stopOnHover : true
    })

    $("#top-menu-bar ul.nav li a[href^='{{ site_url }}#']").on('click', function(e) {

        // prevent default anchor click behavior
        e.preventDefault();

        // animate
        $('html, body').animate({
            scrollTop: $(this.hash).offset().top
        }, 300);

    });
</script>
{{row.footer_data}}
{% endblock javascript_footer %}

{% endblock body %}

