{% if config_option('module_affiliate_marketing_pinterest_share_show_product_link') %}
    <div class="alert alert-info">
        <div class="row">
            <div class="col-md-10">
                <small>{{ i('fa fa-info-circle') }} {{ lang('share_pinterest_product_links_description') }}</small>
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
                    <p> {{ s.description }}</p>
                    <div class="text-sm-right">
                        <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(aff_tools_url('tools/pinterest_share/'~s.id)) }}&media={{ urlencode(s.image_file_name) }}&description={{ s.link_text }}"
                           count-layout="horizontal"
                           class="popup btn btn-danger btn-sm btn-pinterest pin-it-button">
                            {{ i('fa fa-pinterest') }} {{ lang('share') }}
                        </a>
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
    <div class="text-sm-center">
        {{ rows.page.paginate.rows }}
    </div>
{% endif %}
