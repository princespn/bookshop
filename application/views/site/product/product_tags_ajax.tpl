{% if tags %}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{ site_url('product/tags') }}">{{ lang('product_tags') }}</a>
            </h5>
            <hr />
            {% for p in tags %}
                <span class="badge"><a href="{{ site_url }}product/tag/{{ p.tag }}"
                                     class="name">{{ p.tag }}</a>
                <span class="total">{{ kmbt(p.count) }}</span></span>
            {% endfor %}
        </div>
    </div>
{% endif %}