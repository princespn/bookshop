{% extends "global/base.tpl" %}
{% block title %}{{ parent() }} {{ lang('brands') }}{% endblock %}
{% block meta_description %}{{ parent() }} {{ lang('brands') }} {% endblock meta_description %}
{% block meta_keywords %}{{ parent() }} {{ lang('brands') }} {% endblock meta_keywords %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('brands') }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="brand-list">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-md-12">
                {% if brands %}
                    <div class="items">
                        {% for p in brands %}
                            <div class="item col-md-3">
                                <figure class="gallery-item">
                                    {% if p.brand_image == false %}
                                    <div class="row generic-brand py-3">
                                        <div class="col">
                                            <h1 class="text-sm-center">
                                                <a href="{{ site_url }}product/brand/{{ p.brand_id }}-{{ url_title(p.url_name) }}">
                                                    {{ p.brand_name }}
                                                </a>
                                                </h1>
                                        </div>
                                        </div>
                                    {% else %}
                                    {{ image('product_brand', p.brand_image, p.brand_name, 'img-fluid card-img-top mx-auto d-block') }}
                                    {% endif %}
                                    <figcaption class="hover-box">
                                        <h5>
                                            <a href="{{ site_url }}product/brand/{{ p.brand_id }}-{{ url_title(p.url_name) }}"
                                               class="btn btn-primary btn-sm item-details">{{ p.brand_name }}</a>
                                        </h5>
                                    </figcaption>
                                </figure>
                            </div>
                        {% endfor %}
                    </div>
                {% else %}
                    <div role="alert" class="alert alert-info">
                        <h4>{{ lang('no_brands_found') }}</h4>
                    </div>
                {% endif %}
            </div>
        </div>
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
                    columnWidth: '.col-md-3',
                    transitionDuration: 0
                });
            });
        });
    </script>
{% endblock javascript_footer %}
