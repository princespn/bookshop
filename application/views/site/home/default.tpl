{% extends "global/base.tpl" %}
{% block container %}
    {% if row.slide_shows %}
        {% include ('global/slideshows.tpl') %}
        <div class="tag-line default d-none d-md-block">
            <div class="container">
                <div class="row text-sm-center">
                    <div class="col-md-4">
                        <h3><a href="store">{{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }} {{ lang('shop_our_store') }}</a></h3>
                    </div>
                    <div class="col-md-4">
                        <h3><a href="blog">{{ i('fa fa-rss') }} {{ lang('view_the_blog') }}</a></h3>
                    </div>
                    <div class="col-md-4">
                        <h3><a href="contact">{{ i('fa fa-phone') }} {{ lang('contact_us') }}</a></h3>
                    </div>
                </div>
            </div>
        </div>
    {% endif %}
    <div class="product-list">
        {% if row.products.featured_products %}
            <div id="featured-products" class="container section">
                <div class="title">
                    <div class="inner">
                        <h2 class="text-sm-center"><span>{{ lang('featured_products') }}</span></h2>
                    </div>
                </div>
                <div class="carousel-four">
                    {% for p in row.products.featured_products %}
                        <div class="item">
                            <div class="card">
                                <figure class="gallery-item">
                                    {{ image('products', p.photo_file_name, p.product_name, 'featured-product-image img-fluid card-img-top') }}
                                    <figcaption class="hover-box">
                                        <h5><a href="{{ page_url('product', p) }}"
                                               class="btn btn-primary btn-sm item-details">{{ lang('details') }}</a>
                                        </h5>
                                    </figcaption>
                                </figure>
                                <div class="card-body gallery-text">
                                    <h5 class="card-title">{{ p.product_name }}</h5>
                                    <p class="card-text">{{ product_price(p) }}</p>
                                    <p class="card-text">
                                        <a href="{{ page_url('product', p) }}"
                                           class="btn btn-primary">{{ i('fa fa-search') }} {{ lang('view') }}</a>
                                        {% if config_enabled('sts_site_enable_wish_lists') %}
                                            {% if check_product_wish_list(p.product_id) %}
                                                <a href="{{ site_url('wish_list/delete/'~p.product_id) }}"
                                                   class="btn btn-pinterest">{{ i('fa fa-minus') }}</a>
                                            {% else %}
                                                <a href="{{ site_url('wish_list/add/'~p.product_id) }}"
                                                   class="btn btn-pinterest">{{ i('fa fa-heart') }}</a>
                                            {% endif %}
                                        {% endif %}
                                    </p>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </div>
        {% endif %}
        {% if row.products.latest_products %}
            <div id="latest-products" class="container section">
                <div class="title">
                    <div class="inner">
                        <h2 class="text-sm-center"><span>{{ lang('latest_products') }}</span></h2>
                    </div>
                </div>
                <div class="carousel-four">
                    {% for p in row.products.latest_products %}
                        <div class="item">
                            <div class="card">
                                <figure class="gallery-item">
                                    {{ image('products', p.photo_file_name, p.product_name, 'latest-product-image img-fluid card-img-top') }}
                                    <figcaption class="hover-box">
                                        <h5><a href="{{ page_url('product', p) }}"
                                               class="btn btn-primary btn-sm item-details">{{ lang('details') }}</a>
                                        </h5>
                                    </figcaption>
                                </figure>
                                <div class="card-body gallery-text">
                                    <h5 class="card-title">{{ p.product_name }}</h5>
                                    <p class="card-text">{{ product_price(p) }}</p>
                                    <p class="card-text">
                                        <a href="{{ page_url('product', p) }}"
                                                            class="btn btn-primary">{{ i('fa fa-search') }} {{ lang('view') }}</a>
                                        {% if config_enabled('sts_site_enable_wish_lists') %}
                                            {% if check_product_wish_list(p.product_id) %}
                                                <a href="{{ site_url('wish_list/delete/'~p.product_id) }}"
                                                   class="btn btn-pinterest">{{ i('fa fa-minus') }}</a>
                                            {% else %}
                                                <a href="{{ site_url('wish_list/add/'~p.product_id) }}"
                                                   class="btn btn-pinterest">{{ i('fa fa-heart') }}</a>
                                            {% endif %}
                                        {% endif %}
                                    </p>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </div>
        {% endif %}
        {% if row.blogs %}
            <div id="latest-blog-posts" class="container section">
                <div class="title">
                    <div class="inner">
                        <h2 class="text-sm-center"><span>{{ lang('latest_blog_posts') }}</span></h2>
                    </div>
                </div>
                <div class="carousel-three">
                    {% for p in row.blogs %}
                        <div class="item">
                            <div class="card">
                                <figure class="gallery-item">
                                    {% if p.overview_image %}
                                        <img src="{{ p.overview_image }}" alt="{{ p.title }}" class="img-fluid"/>
                                    {% else %}
                                        <img src="{{ base_url }}images/no-photo.jpg" alt="{{ p.title }}" class="img-fluid home-blog-image"/>
                                    {% endif %}
                                    <figcaption class="hover-box">
                                        <h5><a href="{{ page_url('blog', p) }}"
                                               class="btn btn-primary btn-sm item-details">{{ lang('read_more') }}</a>
                                        </h5>
                                    </figcaption>
                                </figure>
                                <div class="card-body">
                                    <h5><a href="{{ page_url('blog', p) }}">{{ p.title }}</a></h5>
                                    <p>{{ parse_text(p.overview) }}</p>
                                    <div>
                                        <small>{{ display_date(p.date_published) }}</small>
                                        <span class="float-right">
                                        {% if sts_content_disqus_shortname %}
                                            <a href="{{ page_url('blog', p) }}#disqus_thread"
                                               class="label label-default"></a>
                                            {% else %}
                                            {% if p.comments %}
                                                <a href="{{ page_url('blog', p) }}#comments"
                                                   class="label label-default">{{ i('fa fa-comments-o') }} {{ p.comments }}</a>
                                            {% endif %}
                                        {% endif %}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </div>
        {% endif %}
        {% if row.brands %}
            <div id="show-brands" class="container section pad-bottom-40">
                <div class="title">
                    <div class="inner">
                        <h2 class="text-sm-center"><span>{{ lang('our_brands') }}</span></h2>
                    </div>
                </div>
                <div class="carousel-five text-sm-center">
                    {% for p in row.brands %}
                        <a href="{{ site_url }}product/brand/{{ p.brand_id }}-{{ url_title(p.url_name) }}">
                            {{ image('home_page_brands', p.brand_image, p.brand_name, 'home-logo mx-auto d-block') }}
                        </a>
                    {% endfor %}
                </div>
            </div>
        {% endif %}
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
        $('.carousel-five').owlCarousel({
            loop: true,
            margin: 10,
            items: 5,
            responsiveClass: true,
            slideSpeed: 400,
            paginationSpeed: 1000,
            rewindSpeed: 1000,
            autoPlay: true,
            stopOnHover: true
        });
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
        $('.carousel-three').owlCarousel({
            loop: true,
            margin: 10,
            items: 3,
            responsiveClass: true,
            slideSpeed: 300,
            paginationSpeed: 400,
            rewindSpeed: 500,
            autoPlay: true,
            stopOnHover: true
        })
    </script>
    {{ row.footer_data }}
{% endblock javascript_footer %}