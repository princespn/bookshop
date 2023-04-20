{% if posts %}
<div class="card">
    <div class="card-body">
        <h5 class="card-title"><a href="{{ site_url('blog')}}">{{ lang('recent_articles') }}</a></h5>
        <hr/>
        {% for p in posts %}
        <div>
            <a href="{{ page_url('blog', p) }}">{{ i ('fa fa-angle-right') }} {{ p.title }}</a>
            {% if loop.last == false %}
            <hr/>
            {% endif %}
        </div>
        {% endfor %}
    </div>
</div>
{% endif %}