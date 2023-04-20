{{ form_open(site_url('product/set_filters'), 'method="post" id="filter-form"') }}
<div class="card mb-3 animated fadeIn">
    <div class="card-body">
        <!-- categories -->
        <h5>{{ i ('fa fa-sort-amount-asc') }} {{ lang('sort_by') }}</h5>
        <hr/>
        <div class="form-check">
            {{ form_radio('sort', set_value('sort', 'product_name-ASC'), filter_c('sort', 'product_name-ASC', opt), 'class="form-check-input"') }}
            <label class="form-check-label">
                {{ lang('product_name_ascending') }}
            </label>
        </div>
        <div class="form-check">
            {{ form_radio('sort', set_value('sort', 'product_name-DESC'), filter_c('sort', 'product_name-DESC', opt), 'class="form-check-input"') }}
            <label class="form-check-label">
                {{ lang('product_name_descending') }}
            </label>
        </div>
        <div class="form-check">
            {{ form_radio('sort', set_value('sort', 'product_price-ASC'), filter_c('sort', 'product_price-ASC', opt), 'class="form-check-input"') }}
            <label class="form-check-label">
                {{ lang('price_ascending') }}
            </label>
        </div>
        <div class="form-check">
            {{ form_radio('sort', set_value('sort', 'product_price-DESC'), filter_c('sort', 'product_price-DESC', opt), 'class="form-check-input"') }}
            <label class="form-check-label">
                {{ lang('price_descending') }}
            </label>
        </div>
        <div class="form-check">
            {{ form_radio('sort', set_value('sort', 'product_views-DESC'), filter_c('sort', 'product_views-DESC', opt), 'class="form-check-input"') }}
            <label class="form-check-label">
                {{ lang('most_product_views') }}
            </label>
        </div>
    </div>
</div>
{% if filters %}
{% for p in filters %}
{% if p.values %}
<div class="card mb-3 animated fadeIn">
    <div class="card-body">
        {% if (p.filter_id == 1) %}
        <h5>{{ i ('fa fa-angle-double-right') }} {{ lang('price') }}</h5>
        <hr/>
        <div class="product-filter">
            {% for v in p.values %}
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="price[]" {{ filter_c('price', v.id, opt) }} value="{{ v.id }}"/>
                <label class="form-check-label">
                    {{ format_amount(v.initial_value) }} - {{ format_amount( v.secondary_value ) }}
                </label>
            </div>
            {% endfor %}
        </div>
        {% elseif (p.filter_id == 2) %}
        <h5>{{ i ('fa fa-angle-double-right') }} <a href="{{site_url('product/categories')}}">{{ lang('categories') }}</a></h5>
        <hr/>
        <div class="product-filter">
            {% for v in p.values %}
            <div class="form-check" id="menu-{{ v.category_id }}">
                <input class="form-check-input" type="checkbox" name="categories[]" {{ filter_c('categories', v.category_id, opt) }} value="{{ v.category_id }}"/>
                <a href="#sub-{{ v.category_id }}" onclick="getSubMenu('{{ v.category_id }}')"
                   data-toggle="collapse" data-parent="#main-menu"
                   id="v-{{ v.category_id }}">
                    {{ v.category_name }}
                    {% if (v.products > 0) %}
                    <small class="badge badge-light text-muted rounded-circle">{{ v.products }}
                    </small>
                    {% endif %}
                </a>
            </div>
            {% endfor %}
        </div>
        {% elseif (p.filter_id == 3) %}
        <h5>{{ i ('fa fa-angle-double-right') }} <a href="{{site_url('brands')}}">{{ lang('brands') }}</a></h5>
        <hr/>
        <div class="product-filter">
            {% for v in p.values %}
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="brands[]" {{ filter_c('brands', v.brand_id, opt) }} value="{{ v.brand_id }}"/>
                <label class="form-check-label">
                    {{  v.brand_name }}
                    {% if (v.products > 0) %}
                    <small class="badge badge-light text-muted rounded-circle">{{ v.products }}
                    </small>
                    {% endif %}
                </label>
            </div>
            {% endfor %}
        </div>
        {% elseif (p.filter_id == 4) %}
        <h5>{{ i ('fa fa-angle-double-right') }} {{ lang('ratings') }}</h5>
        <hr/>
        <div class="product-filter">
            {% for i in 5..1 %}
            <div class="form-check">
                <h5>
                <input class="form-check-input" type="checkbox" name="ratings[]" {{ filter_c('ratings', i,  opt) }} value="{{ i }}"/>
                <label class="form-check-label">
                   {{ format_ratings (i) }}
                </label>
                </h5>
            </div>
            {% endfor %}
        </div>
        {% elseif (p.filter_id == 5) %}
        <h5>{{ i ('fa fa-angle-double-right') }} <a href="{{site_url('product/tags')}}">{{ lang('tags') }}</a></h5>
        <hr/>
        <div class="product-filter">
            {% for v in p.values %}
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="tags[]" {{ filter_c('tags', v.tag_id, opt) }} value="{{ v.tag_id }}"/>
                <label class="form-check-label">
                    {{  v.tag }}
                    {% if (v.products > 0) %}
                    <small class="badge badge-light text-muted rounded-circle">{{ v.products }}
                    </small>
                    {% endif %}
                </label>
            </div>
            {% endfor %}
        </div>
        {% endif %}
    </div>
</div>
{% endif %}
{% endfor %}
{% endif %}

<button class="btn btn-primary btn-block">{{ lang('apply_filters') }}</button>
<a href="{{ site_url('store') }}" class="btn btn-secondary btn-block">{{ lang('reset_filters') }}</a>
{{ form_close() }}
<script>
    function getSubMenu(id) {
        $.ajax({
            url: '{{ site_url('product/sub_categories/') }}' + id,
            type: 'get',
            dataType: 'json',
            success: function (data) {
                if (data['type'] == 'success') {
                    if (data['sub_categories']) {
                        var html = '  <div class="collapse" id="sub-' + id + '">';

                        $.each(data['sub_categories'], function (key, val) {

                            html += '<div class="form-check" id="menu-' + val.category_id  + '">';
                            html += '    <input class="form-check-input" type="checkbox" name="categories[]" value="' + val.category_id  + '"/>';
                            html += ' <a href="#sub-' + val.category_id  + '" onclick="getSubMenu(\'' + val.category_id  + '\')"';
                            html += ' data-toggle="collapse" data-parent="#main-menu"';
                            html += ' id="v-' + val.category_id  + '">';
                            html += ' ' + val.category_name  + '</a>';
                            html += '  </div>';
                        });

                        html += '</div>';

                        $('#menu-' + id).append(html);
                        $('#sub-' + id).collapse();
                    }


                }
                else {

                }

            },
            error: function (xhr, ajaxOptions, thrownError) {
                {{ js_debug() }}(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
    });
    }
</script>