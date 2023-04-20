{% if brands %}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ lang('brands') }}</h5>
            <ul>
                {% for b in brands %}
                    <li><a href="{{ base_url }}product/brand/{{ b.brand_id }}/{{ url_title(b.url_name) }}">{{ b.brand_name }}</a></li>
                {% endfor %}
            </ul>
        </div>
    </div>
{% endif %}