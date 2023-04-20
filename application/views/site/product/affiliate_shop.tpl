{% extends "global/base.tpl" %}
{% block container %}
    <div id="slideshow" class="carousel carousel-fade slide" data-ride="carousel">
        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <div class="slide-item w-100 p-3"
                     style="background: url('{% if row.settings.header_image %}{{ row.settings.header_image }}{% else %} {{ config_option('module_affiliate_marketing_affiliate_stores_default_background') }} {% endif %}'); background-position: center">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-7 col-sm-12">
                                <h1 class="animated slideInDown shop-text">
                                    {% if row.settings.welcome_headline %}
                                    {{ row.settings.welcome_headline }}
                                    {% else %}
                                    {{ config_option('module_affiliate_marketing_affiliate_stores_default_welcome_headline') }}
                                    {% endif %}
                                    </h1>
                                <div class="list animated shop-text slideInLeft">
                                    {% if row.settings.welcome_text %}
                                        {{ row.settings.welcome_text }}
                                    {% else %}
                                        {{ config_option('module_affiliate_marketing_affiliate_stores_default_welcome_text') }}
                                    {% endif %}
                                </div>
                            </div>
                            <div class="col-md-5 hidden-sm hidden-xs">
                                <div class="showcase affiliate">
                                    {% if row.settings.avatar_image %}
                                        <img src="{{ row.settings.avatar_image }}" alt="..."
                                             class="avatar animated fadeInDown">
                                    {% elseif config_option('affiliate_data', 'profile_photo') %}
                                        <img src="{{ config_option('affiliate_data', 'profile_photo') }}" alt="..."
                                             class="avatar animated fadeInDown">
                                    {% endif %}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="product-list">
        {% if row.products %}
            <div class="featured_products wow animated fadeInUp">
                <div id="featured-products" class="container section">
                    <h1 class="headline text-sm-center">{{ lang('featured_products') }}</h1>
                    <div class="items">
                        {% for p in  row.products %}
                            <div class="item col-md-4">
                                <div class="card">
                                    <figure class="gallery-item">
                                        {{ image('products', p.photo_file_name, p.product_name, 'img-fluid card-img-top') }}
                                        <figcaption class="hover-box">
                                            <h5><a href="{{ page_url('product', p) }}"
                                                   class="btn btn-primary btn-sm item-details">{{ lang('details') }}</a>
                                            </h5>
                                        </figcaption>
                                    </figure>
                                    <div class="card-body gallery-text">
                                        <h5 class="card-title">{{ p.product_name }}</h5>
                                        <p class="card-text">{{ product_price(p) }}</p>
                                        <p class="card-text">{{ p.product_overview }}</p>
                                        <p class="card-text"><a href="{{ page_url('product', p) }}"
                                                                class="btn btn-primary">{{ i('fa fa-search') }} {{ lang('view') }}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        {% endfor %}
                    </div>
                </div>
            </div>
        {% endif %}
    </div>
{% endblock container %}
{% block javascript_footer %}
    {{ parent() }}
    <script src="{{ base_url('js/masonry/masonry.js') }}"></script>
    <script src="{{ base_url('js/masonry/imagesloaded.js') }}"></script>
    <script>
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
{% endblock javascript_footer %}