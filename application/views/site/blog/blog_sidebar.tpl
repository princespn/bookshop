<div class="col-md-4 sidebar animated fadeIn">
    {% include ('blog/blog_search_sidebar.tpl')%}
    {% include ('blog/blog_categories_sidebar.tpl')%}
    {% if config_enabled('layout_design_blogs_enable_tag_cloud') %}
        <div id="blog-tags"></div>
    {% endif %}
</div>