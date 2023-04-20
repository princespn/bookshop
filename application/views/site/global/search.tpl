{% extends "global/base.tpl" %}
{% block title %}{{ lang('search') }}{% endblock %}
{% block meta_description %}{{ parent() }}{% endblock meta_description %}
{% block meta_keywords %}{{ parent() }}, {{ lang('search') }}{% endblock meta_keywords %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12 mb-5">
            <h2 class="headline">{{ lang('search_results') }}</h2>
            {{ form_open('search', 'method="get" id="main-search-form" class="form-horizontal"') }}
            <div class="input-group">
                <input type="text" name="search_term" class="form-control form-control-lg"
                       placeholder="{% if term %}{{ term }}{% else %} {{ lang('search_for') }}... {% endif %}">
                <div class="input-group-append">
                    <button class="btn btn-lg btn-primary" type="submit">{{ lang('go') }}</button>
                </div>
            </div>
            {{ form_close() }}
        </div>

    </div>
</div>
{% endblock page_header %}
{% block content %}
<div class="search-list animated fadeIn">
    {% if rows %}
    {{ breadcrumb }}
    {% for p in rows %}
    <div class="row">
        <div class="col-11">
            <h3>
                {% if p.table == 'products' %}
                <a href="{{ site_url }}product/details/{{ p.id }}-{{ url_title(p.url|lower) }}/{{ config_option('affiliate_data','username') }}">
                    {{ p.title }}</a></h3>
            <small class="d-none d-md-block">{{ site_url }}product/details/{{ p.id }}-{{ url_title(p.url|lower) }}/{{ config_option('affiliate_data','username') }}</small>
            {% elseif p.table == 'site_pages' %}
            <a href="{{ site_url }}{{ p.url }}/{{ config_option('affiliate_data','username') }}">
                {{ p.title }}</a> </h3>
            <small class="d-none d-md-block">{{ site_url }}{{ p.url }}/{{ config_option('affiliate_data','username') }}</small>
            {% elseif p.table == 'kb' %}
            <a href="{{ site_url }}kb/article/{{ p.url }}/{{ config_option('affiliate_data','username') }}">
                {{ p.title }}</a> </h3>
            <small class="d-none d-md-block">{{ site_url }}kb/article/{{ p.url }}/{{ config_option('affiliate_data','username') }}</small>
            {% else %}
            <a href="{{ page_url('blog', p) }}">
                {{ p.title }}</a> </h3>
            <small class="d-none d-md-block">{{ page_url('blog', p) }}</small>
            {% endif %}
            <div class="box-meta">
                <ul class="list-inline">
                    <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(p.date) }}</li>
                    {% if p.table == 'blog' %}
                    {% if sts_content_enable_comments  == '1' %}
                    <li>
                        {{ i('fa fa-comment-o') }}
                        <a href="{{ page_url('blog', p) }}#comments">
                            {{ p.reviews|number_format }} {{ lang('comments') }}</a>
                    </li>
                    {% else %}
                    {% if p.reviews %}
                    <li>{{ i('fa fa-comment-o') }}
                        <a href="{{ page_url('blog', p) }}#disqus_thread"
                           class="label label-default"></a></li>
                    {% endif %}
                    {% endif %}
                    {% endif %}
                    <li class="search-views">{{ i('fa fa-search') }} {{ lang('views') }} {{ kmbt(p.views) }}</li>
                </ul>
            </div>
        </div>
        <div class="col-1 text-md-right">
                    <span class="badge badge-dark">
                    {% if p.table == 'products' %}
                        {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }}
                        {% elseif p.table == 'site_pages' %}
                        {{ i('fa fa-file-text-o') }}
                        {% elseif p.table == 'kb' %}
                        {{ i('fa fa-support') }}
                        {% else %}
                        {{ i('fa fa-rss') }}
                    {% endif %}
                   </span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                {% if p.image %}
                <div class="col-md-1">
                    <div class="thumbnail">
                        {{ image(p.table, p.image, p.title, 'img-fluid img-thumbnail mx-auto d-block', TRUE) }}
                        <hr class="d-block d-sm-none"/>
                    </div>
                </div>
                <div class="col-md-11">
                    {% else %}
                    <div class="col-md-12">
                        {% endif %}
                        <p>
                            {% if p.table == 'site_pages' %}
                            {{ substr(p.overview|striptags, 0,250) }}
                            {% elseif p.table == 'kb' %}
                            {{ substr(p.overview|striptags, 0,250) }}
                            {% else %}
                            {{ p.overview }}
                            {% endif %}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <hr/>
    {% endfor %}
    {% include ('global/pagination.tpl') %}
    {% else %}
    <div class="row">
        <div class="col-md-8 offset-md-2">
            {{ form_open('search', 'method="get" id="search-form" class="form-horizontal"') }}
            <div class="jumbotron white">
                <h1>{{ lang('search_our_site') }}</h1>
                <div class="input-group">
                    <input type="text" name="search_term" class="form-control form-control-lg"
                           placeholder="{{ lang('enter_an_item_to_search_for') }}...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">{{ lang('go') }}</button>
                    </div>
                </div>
                {% if term %}
                <br/>
                <p class="alert alert-info">{{ i('fa fa-exclamation-circle') }} {{ lang('no_results_found') }}
                    - {{ term }}</p>
                {% endif %}
            </div>
            {{ form_close() }}
        </div>
    </div>
    {% endif %}
</div>
{% endblock content %}
{% block javascript_footer %}
{{ parent() }}
<script>

    function kmbt(value) {
        var suffixes = ["", "k", "m", "b", "t"];
        var suffixNum = Math.floor(("" + value).length / 3);
        var shortValue = parseFloat((suffixNum != 0 ? (value / Math.pow(1000, suffixNum)) : value).toPrecision(2));
        if (shortValue % 1 != 0) {
            var shortNum = shortValue.toFixed(1);
        }
        return shortValue + suffixes[suffixNum];
    }
</script>
{% endblock javascript_footer %}