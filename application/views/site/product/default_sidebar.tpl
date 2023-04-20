<div class="col-md-{% if (layout_design_product_page_layout == 'grid') %}3{% else %}4{% endif %}">
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">{{ lang('search_products') }}</h5>
        <hr />
        {{ form_open('product/search', 'method="get" id="search-form" class="form-horizontal"') }}
        <div class="input-group">
            <input type="text" name="search_term" class="form-control" placeholder="{{ lang('search_for') }}...">
            <div class="input-group-append">
                <button class="btn btn-secondary" type="submit">{{ lang('go') }}</button>
            </div>
        </div>
        {{ form_close() }}
        {% if config_enabled('layout_design_products_enable_infinite_scroll') == false %}
        <div class="dropdown mt-3">
            <button class="btn btn-outline-secondary btn-block btn-sm dropdown-toggle" type="button"
                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false">
                {{ i('fa fa-sort') }} {{ lang('sort_products_by') }}
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item"
                   href="{{ current_url() }}?search_term={{ search_term }}&column=product_name&order=ASC">{{ lang('product_name_ascending') }}</a>
                <a class="dropdown-item"
                   href="{{ current_url() }}?search_term={{ search_term }}&column=product_name&order=DESC">{{ lang('product_name_descending') }}</a>
                <a class="dropdown-item"
                   href="{{ current_url() }}?search_term={{ search_term }}&column=product_price&order=ASC">{{ lang('price_ascending') }}</a>
                <a class="dropdown-item"
                   href="{{ current_url() }}?search_term={{ search_term }}&column=product_price&order=DESC">{{ lang('price_descending') }}</a>
                <a class="dropdown-item"
                   href="{{ current_url() }}?search_term={{ search_term }}&column=product_views&order=DESC">{{ lang('most_product_views') }}</a>
            </div>
        </div>
        {% endif %}
    </div>
</div>
{% if config_enabled('layout_design_product_enable_tag_cloud') %}
<div id="product-tags"></div>
{% endif %}
{% if sub_categories %}
{% include ('product/categories_sidebar.tpl') %}
{% endif %}
<div id="brands" class="d-none d-lg-block"></div>
{% include ('global/referred_by.tpl') %}
<script>
    $('#brands').load('{{ site_url('brands/view/?q=ajax') }}');
</script>
</div>