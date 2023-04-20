{% if tags %}
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><a href="{{ site_url('blog/tags')}}">{{ lang('blog_tags') }}</a></h5>
            <hr/>
            {% for p in tags %}
                <span><a href="{{ site_url }}blog/tag/{{ p.tag }}"
                                     class="badge badge-light">{{ p.tag }}</a>
                </span>
            {% endfor %}
        </div>
    </div>
{% endif %}