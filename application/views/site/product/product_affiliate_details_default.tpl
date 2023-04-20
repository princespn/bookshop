{% extends "global/base.tpl" %}
{% block title %}{{ p.name.meta_title }}{% endblock %}
{% block meta_description %}{{ p.name.meta_description }}{% endblock meta_description %}
{% block meta_keywords %}{{ p.name.meta_keywords }}{% endblock meta_keywords %}
{% block meta_property %}
{% if p.photo_file_name %}
<meta property="og:image" content="{{ photo_path(p.photo_file_name) }}"/>
{% endif %}
<meta property="og:url" content="{{ page_url('product', p) }}"/>
<meta property="og:title" content="{{ p.name.product_name }}"/>
<meta property="og:description" content="{{ p.product_overview }}"/>

{% endblock meta_property %}
{% block css %}
{{ parent() }}
<link href="{{ base_url }}js/slider/jquery.bxslider.css" rel="stylesheet" type="text/css"/>
{% endblock css %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang('store') }}</h2>
        </div>

    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="product-details">
    {{ form_open_multipart('cart/add/'~p.product_id, 'id="form" class="form-horizontal"') }}
    {{ form_hidden('product_id', p.product_id) }}
    <div class="row">
        <div class="col-md-12">
            {{ breadcrumb }}
            <div class="row">
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="image-container" class="invisible">
                                {% if p.photos %}
                                <div class="slider">
                                    {% if p.default_video_code %}
                                    <div class="embed-responsive embed-responsive-16by9" id="zoom-0">
                                        {{ html_decode(p.default_video_code) }}
                                    </div>
                                    {% endif %}
                                    {% for photo in p.photos %}
                                    <div id="zoom-{{ photo.photo_id }}">
                                        <a href="{{ photo_path(photo.photo_file_name) }}"
                                           class="image-group" rel="image-group"
                                           id="imagelink-{% if p.default_video_code %}{{ loop.index }}{% else %}{{ loop.index0 }}{% endif %}">
                                            <img src="{{ photo_path(photo.photo_file_name) }}"
                                                 alt="photo" class="img-fluid photo-slide mx-auto"
                                                 id="image-{% if p.default_video_code %}{{ loop.index }}{% else %}{{ loop.index0 }}{% endif %}"/>
                                        </a>
                                    </div>
                                    {% endfor %}
                                </div>
                                <div class="wish-list-button">
                                    {% if config_enabled('sts_site_enable_wish_lists') %}
                                    {% if sess('user_logged_in') %}
                                    {% if check_product_wish_list(id) %}
                                    <a href="{{ site_url('wish_list/delete/'~id) }}"
                                       data-toggle="tooltip" data-placement="right"
                                       title="{{ lang('remove_from_wish_list') }}"
                                       class="btn btn-secondary btn-icon">{{ i('fa fa-minus') }}</a>
                                    {% else %}
                                    <a href="{{ site_url('wish_list/add/'~id) }}"
                                       data-toggle="tooltip" data-placement="right"
                                       title="{{ lang('add_to_wish_list') }}"
                                       class="btn btn-pinterest btn-icon">{{ i('fa fa-heart') }}</a>
                                    {% endif %}
                                    {% endif %}
                                    {% endif %}
                                </div>
                                <div id="thumbs" class="row">
                                    {% if p.default_video_code %}
                                    <div class="col-2 text-sm-center"><a data-slide-index="0">
                                            <span class="btn btn-danger">{{ i('fa fa-play fa-2x') }}</span></a>
                                    </div>
                                    {% endif %}
                                    {% for photo in p.photos %}
                                    <div class="col-2">
                                        <a href="" id="thumb-{% if p.default_video_code %}{{ loop.index }}{% else %}{{ loop.index0 }}{% endif %}" data-slide-index="{% if p.default_video_code %}{{ loop.index }}{% else %}{{ loop.index0 }}{% endif %}">
                                            <img src="{{ photo_path(photo.thumb) }}" alt="photo" class="img-thumbnail mb-1" id="{{ photo.thumb }}"/></a>
                                    </div>

                                    {% endfor %}
                                </div>
                                {% else %}
                                <img src="{{ base_url }}images/no-photo.jpg" alt="photo" class="img-fluid"/>
                                {% endif %}

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 product-side">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h3>{{ p.name.product_name }}</h3>
                            {% include ('product/product_social_buttons.tpl') %}
                            {% if p.avg_ratings %}
                            <div class="star-rating">{{ format_ratings(p.avg_ratings)}}</div>
                            {% endif %}
                            <ul class="list-unstyled">
                                {% if sts_products_enable_inventory == 1 %}
                                {% if p.enable_inventory == 1 %}
                                {% if p.inventory_amount > 0 %}
                                {% if config_enabled('sts_products_alert_inventory') %}
                                <li class="inventory-alert">
                                    <h5>
                                        {% if (p.inventory_amount < sts_products_alert_inventory_level) %}
                                        <span class="badge badge-danger">
                                                                {{ p.inventory_amount }} {{ lang('left_in_stock') }}
                                                                    </span>
                                        {% else %}
                                        <span class="badge badge-success">
                                                               {{ lang('product_in_stock') }}
                                                                    </span>
                                        {% endif %}
                                    </h5>
                                </li>
                                {% endif %}
                                {% else %}
                                <li class="inventory out-of-stock">
                                    <h4><span class="badge badge-danger">{{ lang('out_of_stock') }} {{ i('fa fa-exclamation-circle') }}
                                                    </span></h4>
                                </li>

                                {% endif %}
                                {% endif %}
                                {% endif %}
                            </ul>
                            <h2 class="price">{{ product_price(p) }}</h2>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            {% if p.attributes %}
                            {{ lang('select_options') }}
                            {% else %}
                            {{ lang('buy_now') }}
                            {% endif %}
                        </div>
                        <div class="card-body">
                            <a href="{{ p.affiliate_redirect }}" class="btn btn-lg btn-primary btn-block">
                                {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }}
                                {{ lang('buy_now') }}
                            </a>
                        </div>
                    </div>
                    {% include ('product/direct_referral.tpl') %}
                    {% if p.tags %}
                    <div class="card">
                        <div class="card-body">
                            <small>{{ lang('tags') }}</small>
                            {% for t in p.tags %}
                            <a href="{{ site_url }}product/tag/{{ t.tag }}">
                                <span class="badge badge-info rounded">{{ t.tag }}</span>
                            </a>
                            {% endfor %}
                        </div>

                    </div>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
    <br/>

    <div class="description row">
        <div class="col-lg-12">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs resp-tabs responsive" role="tablist">
                <li class="nav-item">
                    <a href="#description" id="description-tab" class="nav-link active" role="tab" data-toggle="tab"
                       aria-controls="description" aria-selected="true">{{ lang('description') }}</a>
                </li>
                {% if p.videos %}
                <li class="nav-item">
                    <a href="#videos" id="videos-tab" class="nav-link" role="tab" data-toggle="tab"
                       aria-controls="videos" aria-selected="false">{{ lang('videos') }}</a>
                </li>
                {% endif %}
                {% if p.product_specs %}
                <li class="nav-item">
                    <a href="#specifications" id="specifications-tab" class="nav-link" role="tab"
                       data-toggle="tab" aria-controls="specifications"
                       aria-selected="false">{{ lang('specifications') }}</a>
                </li>
                {% endif %}
                {% if (config_enabled('sts_products_enable_reviews')) %}
                <li class="nav-item">
                    <a href="#reviews" id="reviews-tab" class="ajax-link nav-link" role="tab"
                       data-remote-div="#load_reviews"
                       data-tab-remote="{{ base_url }}product_reviews/view/{{ id }}?q=ajax" data-toggle="tab"
                       aria-controls="reviews" aria-selected="false">{{ lang('reviews') }}</a>
                </li>
                {% endif %}
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="description">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="tab-content">
                                {% if p.product_description %}
                                {{ parse_text(p.product_description) }}
                                {% else %}
                                {{ parse_text(p.product_overview) }}
                                {% endif %}
                            </div>
                        </div>
                    </div>
                </div>
                {% if p.videos %}
                <div role="tabpanel" class="tab-pane fade" id="videos">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="tab-content">
                                <h2>{{ lang('product_videos') }}</h2>
                                <hr/>
                                {% for v in p.videos %}
                                <div class="product-videos text-center">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        {{ html_decode(v.video_code) }}
                                    </div>
                                    <h4 class="title">{{ v.video_name }}</h4>

                                    <p class="description">{{ v.video_description }}</p>
                                </div>
                                <hr/>
                                {% endfor %}
                            </div>
                        </div>
                    </div>
                </div>
                {% endif %}
                {% if p.product_specs %}
                <div role="tabpanel" class="tab-pane fade" id="specifications">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="tab-content">
                                <h2>{{ lang('product_specifications') }}</h2>
                                <hr/>
                                {% for spec in p.product_specs %}
                                <div class="row">
                                    <div class="col-md-6">{{ spec.specification_name }}</div>
                                    <div class="col-md-6">{{ spec.spec_value }}</div>
                                </div>
                                <hr/>
                                {% endfor %}
                            </div>
                        </div>
                    </div>
                </div>
                {% endif %}
                {% if (config_enabled('sts_products_enable_reviews')) %}
                <div role="tabpanel" class="tab-pane fade" id="reviews">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="tab-content">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h2>{{ lang('product_reviews') }}</h2>
                                    </div>
                                    <div class="col-md-4 text-md-right">
                                        <a href="{{ site_url }}product_reviews/add/{{ id }}"
                                           class="btn btn-primary btn-block-sm">
                                            {{ i('fa fa-plus') }} {{ lang('add_your_review') }}</a>
                                    </div>
                                </div>
                                <hr/>
                                <div id="load_reviews" class="ajaxFade"></div>
                            </div>
                        </div>
                    </div>
                </div>
                {% endif %}
            </div>
        </div>
    </div>
    {{ form_close() }}
</div>
<div id="similar-products"></div>
{% endblock content %}
{% block javascript_footer %}
{{ parent() }}
{% include ('js/product_page_js.tpl') %}
{{ include('js/add_cart_js.tpl') }}
{% endblock javascript_footer %}