{% if blog_categories %}
{% if blog_categories|length > 1 %}
<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            <a href="{{ site_url('blog/categories') }}">{{ lang ('blog_categories') }}</a></h5>
        <hr />
        {% for c in blog_categories %}
        <div class="row">
        <div class="col-10">
            <a href="{{ site_url }}blog/category/{{ c.category_id }}-{{ url_title(c.category_name) }}">
                {{ i('fa fa-folder-o') }} {{ c.category_name }}
            </a>
            </div>
        <div class="col-1 text-sm-center">
            {% if c.total > 0 %}<h4 class="badge badge-light">{{c.total}}</h4>{%endif%}
        </div>
        </div>
        {% if loop.last == false %}
        <hr/>
        {% endif %}
        {% endfor %}
    </div>
</div>
{% endif %}
{% endif %}