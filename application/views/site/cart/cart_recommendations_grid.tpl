{% extends "global/base.tpl" %}
{% block title %} {{ parent() }} {{ lang('store') }}{% endblock %}
{% block meta_description %}{{ parent() }} store{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }}, store{% endblock meta_keywords %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang('some_products_you_might_also_like') }}</h2>
        </div>
    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="recommend-grid">
    {{ breadcrumb }}
    <div class="row">
        <div class="carousel-four">
            {% if products %}
            {% for p in products %}
            <div class="item">
                <div class="card">
                    <figure class="gallery-item">
                        {{ image('products', p.photo_file_name, p.product_name, 'img-fluid card-img-top') }}
                        <figcaption class="hover-box">
                            <h5><a href="{{ page_url('product', p) }}"
                                   class="btn btn-primary btn-sm item-details">{{lang('details')}}</a></h5>
                        </figcaption>
                    </figure>
                    <div class="card-body gallery-text">
                        <h5 class="name"><a href="{{ page_url('product', p) }}">{{ p.product_name }}</a></h5>
                        {% if p.avg_ratings %}
                        <div class="star-rating">{{ format_ratings(p.avg_ratings)}}</div>
                        {% endif %}
                        <div>
                            {% if p.product_type == 'subscription' %}
                            <p class="price">
                                <a href="{{ page_url('product', p) }}"
                                   class="btn btn-info  btn-block subscription">{{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }}
                                    {{ lang('view_options') }}
                                </a>
                            </p>
                            {% else %}
                            <p class="price">{{ product_price(p) }}</p>
                            <a href="{{ site_url }}cart/add/{{ p.product_id }}"
                               class="btn btn-primary btn-block buy-now">
                                {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }} {{ lang('add_to_'~layout_design_shopping_cart_or_bag) }}</a>
                            {% endif %}
                        </div>
                        <p class="hide">
                            <a name="{{ p.product_id }}"></a>
                        </p>

                    </div>
                </div>
            </div>
            {% endfor %}
            {% else %}
            <br/>
            <div class="col-md-12">
                <div role="alert" class="alert alert-info">
                    <strong>{{ lang('sorry') }}... </strong>{{ lang('no_products_found') }}
                </div>
            </div>
            {% endif %}
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-12 text-sm-right">
            <a href="{{ site_url('cart') }}" class="submit-button btn btn-lg btn-secondary">
                {{ i('fa fa-caret-right') }} <span>{{ lang('no_thanks') }}, {{ lang('proceed_to_cart') }}</span></a>
        </div>
    </div>
</div>
{% endblock content %}
{% block javascript_footer %}
{{ parent() }}
<script src="{{ base_url('js/owl-carousel/owl.carousel.min.js') }}"></script>
<script>
    $('.carousel-four').owlCarousel({
        loop: true,
        margin: 10,
        items: 4,
        responsiveClass: true,
        slideSpeed: 200,
        paginationSpeed: 800,
        rewindSpeed: 1000,
        autoPlay: true,
        stopOnHover: true
    });
</script>
{% endblock javascript_footer %}
