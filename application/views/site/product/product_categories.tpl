{% extends "global/base.tpl" %}
{% block title %}{% if c.category_name %}{{ c.category_name }}{% else %}{{ lang('product_categories') }}{% endif %}{% endblock %}
{% block meta_description %}{% if c.category_name %}{{ c.meta_description }}{% else %}{{ lang('product_categories') }}{% endif %}{% endblock meta_description %}
{% block meta_keywords %}{% if c.category_name %}{{ c.meta_keywords }}{% else %}{{ lang('product_categories') }}{% endif %} {% endblock meta_keywords %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang('categories') }}
                {% if c.category_name %}
                <span class="float-right">{{ c.category_name }}</span>
                {% endif %}
            </h2>
        </div>

    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="product-list">
    {{ breadcrumb }}
    <div class="row">
        <div class="col-md-12">
            {% if c.category_banner %}
            <div class="category-banner thumbnail mb-4">
                {{ image('product_category', c.category_banner, c.category_name, 'img-fluid') }}
            </div>
            {% endif %}
            {% if categories %}
            <div class="items">
                {% for p in categories %}
                <div class="item col-md-3" id="product-{{ p.category_id }}">
                    <figure class="gallery-item">
                        {{ image('product_category', p.category_image, p.category_name, 'img-thumbnail img-fluid mx-auto d-block') }}
                        <figcaption class="hover-box">
                            <h5>
                                <p>{{ p.category_name }}<br/>
                                    <small>{{ p.description }}</small>
                                </p>
                                <p>
                                    <a href="{{ site_url }}product/category/{{ p.category_id }}-{{ p.url_name }}"
                                       class="btn btn-sm btn-primary"
                                       role="button">{{ lang('products') }}</a>
                                    <a href="{{ site_url }}product/categories/{{ p.category_id }}"
                                       class="btn btn-sm btn-secondary"
                                       role="button">{{ lang('sub_categories') }}</a>
                                </p>
                            </h5>
                        </figcaption>
                    </figure>
                </div>
                {% endfor %}
            </div>
            {% else %}
            <div role="alert" class="alert alert-info">
                <h4>{{ lang('no_categories_found') }}</h4>
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