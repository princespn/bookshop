{% if config_enabled('sts_affiliate_enable_direct_product_code') %}
    <div class="card mb-3">
        <div class="card-header">{{ lang('share_this_product') }}</div>
        <div class="card-body">
            {% if config_enabled('module_affiliate_marketing_facebook_share_show_product_link') %}
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ page_url('product', p)|url_encode }}"
                   onclick="centeredPopup(this.href,'myWindow','700','300','yes');return false" class="btn btn-facebook" target="_blank">{{ i('fa fa-facebook') }} {{ lang('share') }}</a>
            {% endif %}
            {% if config_enabled('module_affiliate_marketing_twitter_share_show_product_link') %}
                <a href="https://twitter.com/intent/tweet?text={{ p.product_name }}............&url={{ page_url('product', p)|url_encode }}"
                   onclick="centeredPopup(this.href,'myWindow','700','300','yes');return false"  class="btn btn-twitter" target="_blank">{{ i('fa fa-twitter') }} {{ lang('tweet') }}</a>
            {% endif %}
            {% if config_enabled('module_affiliate_marketing_pinterest_share_show_product_link') %}
                <a href="https://pinterest.com/pin/create/button/?url={{ page_url('product', p)|url_encode }}&media={{ base_url('images/uploads/products/'~p.photo_file_name) }}&description={{ p.product_overview|url_encode }}"
                   onclick="centeredPopup(this.href,'myWindow','700','300','yes');return false" class="btn btn-pinterest" target="_blank">{{ i('fa fa-pinterest') }} {{ lang('pin') }}</a>
            {% endif %}
            <a id="show-link" class="btn btn-secondary">{{ lang('copy_paste_links') }}</a>
            {{ affiliate_store_button(p.product_id) }}
            <p class="cp_links hide"><textarea class="form-control text-sm" readonly onclick="this.select()">{{ page_url('product', p) }}</textarea>
            </p>
        </div>
    </div>
{% endif %}
