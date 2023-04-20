{% if config_enabled('sts_site_enable_wish_lists') %}
    {% if sess('user_logged_in') %}
        <div class="card">
            <div class="card-header">{{ lang('wish_list') }}</div>
            <div class="card-body text-md-center">
                {% if check_product_wish_list(id) %}
                    <a href="{{ site_url('wish_list/delete/'~id) }}"
                       class="btn btn-secondary">{{ i('fa fa-magic') }} {{ lang('remove_from_wish_list') }}</a>
                {% else %}
                    <a href="{{ site_url('wish_list/add/'~id) }}"
                       class="btn btn-secondary">{{ i('fa fa-magic') }} {{ lang('add_to_wish_list') }}</a>
                {% endif %}
            </div>
        </div>
    {% endif %}
{% endif %}