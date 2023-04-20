<div class="col-md-4 sidebar animated fadeIn">
    {% include ('blog/blog_search_sidebar.tpl')%}
    {% include ('blog/blog_categories_sidebar.tpl')%}
    <div id="recent_articles"></div>
    {% if p.tags %}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><a href="{{ site_url('blog/tags')}}">{{ lang('blog_tags') }}</a></h5>
                <hr />
                {{ format_tags(p.tags, 'badge badge-light') }}
            </div>
        </div>
    {% endif %}
</div>