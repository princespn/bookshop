{% if brands %}
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">
                <a href="{{site_url('brands')}}">{{ lang('brands') }}</a>
            </h5>
            <hr />
            {% for p in brands %}
                <div class="row">
                    <div class="col-md-10">
                            <span>
                                <a href="{{ base_url }}product/brand/{{ p.brand_id }}-{{ url_title(p.url_name) }}">{{ i('fa fa-folder-o') }} {{ p.brand_name }}</a>
                            </span>
                    </div>
                </div>
                {% if loop.last == false %}
                    <hr/>
                {% endif %}
            {% endfor %}
        </div>
    </div>
{% endif %}