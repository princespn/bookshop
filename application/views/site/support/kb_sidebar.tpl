<div class="col-md-3">
    {% if config_enabled('sts_site_refer_friend_enable') %}
        <div class="card mb-3">
            <div class="card-header">{{ lang('share_with_your_friends') }}</div>
            <div class="card-body text-md-center">
                {{ html_decode(config_option('sts_site_refer_friend_code')) }}
            </div>
        </div>
    {% endif %}
    <div class="card mb-3">
        <div class="card-header">
            {{ lang('search_kb') }}
        </div>
        <div class="card-body">
            {{ form_open(kb_uri~'/search', 'method="get" id="search-form" class="form-horizontal"') }}
            <div class="input-group">
                <input type="text" name="search_term" class="form-control" placeholder="{{ lang('search_for') }}...">
                <div class="input-group-append">
                    <button class="btn btn-secondary" type="submit">{{ lang('go') }}</button>
                </div>
            </div>
            {{ form_close() }}
        </div>
    </div>
    {% if kb_categories %}
        <div class="kb-categories">
            <div class="card mb-3">
                <div class="card-header">
                    {% if id %}
                        {{ lang('sub_categories') }}
                    {% else %}
                        {{ lang('categories') }}
                    {% endif %}
                </div>
                <div class="card-body">
                    {% for c in kb_categories %}
                        <div class="" id="menu-{{ c.category_id }}">
                            <a href="{{ site_url }}{{kb_uri}}/category/{{ c.category_id }}-{{ c.category_url }}">{{ i('fa fa-folder-open-o') }}
                                {{ c.category_name }}</a>
                            {% if loop.last == false %}
                                <hr/>
                            {% endif %}
                        </div>
                    {% endfor %}
                </div>
            </div>
        </div>

    {% endif %}
</div>