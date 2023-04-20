{% extends "global/base.tpl" %}
{% block title %}{{ parent() }} {{ lang('brands') }}{% endblock %}
{% block meta_description %}{{ parent() }} {{ lang('brands') }} {% endblock meta_description %}
{% block meta_keywords %}{{ parent() }} {{ lang('brands') }} {% endblock meta_keywords %}
{% block content %}
<div class="product-list">
    {% if row.products.latest_products %}
    <div class="latest_products">
        <div id="latest-products" class="container section">
            <div class="title">
                <div class="inner">
                    <h2 class="text-sm-center"><span>{{lang('latest_products')}}</span></h2>
                </div>
            </div>
            <div class="carousel-three">
                {% for p in  row.products.latest_products %}
                <div class="item">
                    <div class="card">
                        <figure class="gallery-item">
                            {{ image('products', p.photo_file_name, p.product_name, 'img-fluid card-img-top', TRUE) }}
                            <figcaption class="hover-box">
                                <h5><a href="{{ page_url('product', p) }}" class="btn btn-primary btn-sm item-details">{{lang('details')}}</a></h5>
                            </figcaption>
                        </figure>
                        <div class="card-body gallery-text">
                            <h5 class="card-title">{{ p.product_name }}</h5>
                            <p class="card-text">{{ product_price(p) }}</p>
                            <p class="card-text">{{parse_text(p.product_overview)}}</p>
                            <p class="card-text"><a href="{{ page_url('product', p) }}" class="btn btn-primary">{{i('fa fa-search')}} {{lang('view')}}</a></p>
                        </div>
                    </div>
                </div>
                {% endfor %}
            </div>
        </div>
        {% endif %}
    </div>
    {% block javascript_footer %}
    {{ parent() }}
    <script src="{{ base_url('js/wow/wow.min.js') }}"></script>
    <script src="{{ base_url('js/owl-carousel/owl.carousel.min.js') }}"></script>
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


        $(document).ready(function () {
            $('.gallery-item').hover(function () {
                $(this).find('.hover-box').fadeIn(300);
            }, function () {
                $(this).find('.hover-box').fadeOut(100);
            });
        });
    </script>
    {%endblock javascript_footer %}
    {% endblock content %}
