<div class="categories card mb-3">
    <div class="card-body">
        <h5 class="card-title"><a href="{{site_url('product/categories')}}">
            {% if id %}
            {{ lang('sub_categories') }}
            {% else %}
            {{ lang('categories') }}
            {% endif %}
            </a>
        </h5>
        <hr />
        {% for cat in sub_categories %}
            <div id="menu-{{ cat.category_id }}">
                <a href="#sub-{{ cat.category_id }}" onclick="getSubMenu('{{ cat.category_id }}')"
                   data-toggle="collapse" data-parent="#main-menu"
                   id="cat-{{ cat.category_id }}">{{ i('fa fa-folder-open-o') }}</a>
                <a href="{{ site_url }}product/category/{{ cat.category_id }}-{{ url_title(cat.url_name) }}">
                    {{ cat.category_name }}</a>
                <hr />
            </div>
        {% endfor %}
    </div>
</div>
<script>
    function getSubMenu(id) {
        $.ajax({
            url: '{{ site_url('product/sub_categories/') }}' + id,
            type: 'get',
            dataType: 'json',
            success: function (data) {
                if (data['type'] == 'success') {
                    if (data['sub_categories']) {
                        var html = '  <div class="collapse list-group-submenu" id="sub-' + id + '">';

                        $.each(data['sub_categories'], function (key, val) {
                            html += '<div id="menu-' + val.category_id + '"><div class="">';
                            html += '<a href="#sub-' + val.category_id + '" onclick="getSubMenu(\'' + val.category_id + '\')" data-toggle="collapse" data-parent="#menu-' + id + '" id="cat-{{ cat.category_id }}" data-parent="#menu-' + val.category_id + '">{{ i('fa fa-folder-open-o') }}</a>';
                            html += ' <a href="{{ site_url }}product/category/' + val.category_id + '-' + val.url_title + '">' + val.category_name + '</a></span><hr /></div></div>';
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