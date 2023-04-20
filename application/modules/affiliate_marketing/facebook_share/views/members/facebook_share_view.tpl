{% if config_option('module_affiliate_marketing_facebook_share_show_product_link') %}
    <div class="alert alert-info">
        <div class="row">
            <div class="col-10">
                <small>{{ i('fa fa-info-circle') }} {{ lang('share_facebook_product_links_description') }}</small>
            </div>
            <div class="col-md-2 text-md-right">
                <a href="{{ site_url('store') }}" class="btn btn-sm btn-primary">
                    {{ i('fa fa-shopping-'~layout_design_shopping_cart_or_bag) }} {{ lang('store') }}</a></div>
        </div>
    </div>
{% endif %}
{% if rows.values %}
    <div class="card-columns">
        {% for s in rows.values %}
            <div class="card panel panel-default">
                <div class="card-header">{{ s.name }}</div>
                <div class="card-body panel-body">
                    <p> {{ s.link_text }}</p>
                    <div class="text-sm-right">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(aff_tools_url('tools/facebook_share/'~s.id)) }}"
                           class="popup btn btn-info btn-sm btn-facebook">{{ i('fa fa-facebook') }} {{ lang('share') }}</a>
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
    <div class="text-sm-center">
        {{ rows.page.paginate.rows }}
    </div>
{% endif %}