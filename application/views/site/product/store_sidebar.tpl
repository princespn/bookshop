{% if config_enabled('sts_products_filters_enable') %}
<div class="col-md-{% if (layout_design_product_page_layout == 'grid') %}3{% else %}4{% endif %}">
<div id="filter-sidebar" class="d-none d-lg-block"></div>
<script>
    $('#filter-sidebar').load('{{ site_url('product/load_filters/?'~url) }}');
</script>
</div>
{% else %}
   {% include ('product/default_sidebar.tpl') %}
{% endif %}
</div>