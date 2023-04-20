{% extends "global/base.tpl" %}
{% block title %}{{ lang('gallery') }}{% endblock %}
{% block meta_description %}{{ lang('gallery_photos') }}{% endblock meta_description %}
{% block meta_keywords %}{{ lang('gallery') }}, {{ lang('photos') }}{% endblock meta_keywords %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('gallery_portfolio') }}</h2>
            </div>
        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="blog-content">
        <div class="container">
            {{ breadcrumb }}
            <div class="row">
                <div class="col-md-12">
                    <div class="page-body">
                        <div class="row">
                            <div class="col-md-12">
                                {% if gallery %}
                                    <div class="items">
                                        {% for p in gallery %}
                                            <div class="item col-md-3">
                                                <div class="mb-3">
                                                    <figure class="gallery-item">

                                                        {{ image('blog', p.gallery_photo, p.gallery_name, 'img-fluid', TRUE) }}

                                                        <figcaption class="hover-box">
                                                            <h5>
                                                               <p>{{ p.gallery_name }}
                                                               </p>
                                                                <a href="{{ p.gallery_photo }}"
                                                                   class="btn btn-light btn-sm image-group">
                                                                    {{ i('fa fa-search') }}
                                                                </a>
                                                                {% if p.gallery_url %}
                                                                    <a href="{{ p.gallery_url }}"
                                                                       class="btn btn-light btn-sm">
                                                                        {{ i('fa fa-external-link') }}
                                                                    </a>
                                                                {% endif %}
                                                            </h5>
                                                        </figcaption>
                                                    </figure>
                                                </div>

                                            </div>
                                        {% endfor %}
                                    </div>
                                {% endif %}
                            </div>
                        </div>
                    </div>
                </div>
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
        //Initiate WOW JS
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

        $(".image-group").colorbox({rel: 'image-group'});
    </script>
{% endblock javascript_footer %}