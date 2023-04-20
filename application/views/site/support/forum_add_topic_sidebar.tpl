<div class="col-md-3">
    {% if forum_categories %}
        <div class="card mb-3">
            <div class="card-header">
                {{ lang('categories') }}
            </div>
            <div class="card-body">
                {% for p in forum_categories %}
                    <div class="row">
                        <div class="col-md-10">
                            <strong>
                                <a href="{{ site_url }}{{forum_uri}}/topics/{{ p.category_url }}">
                                    {{ i('fa fa-folder-o') }} {{ p.category_name }}</a>
                            </strong>
                            <small>{{ p.description }}</small>
                        </div>
                        <div class="col-md-1 text-sm-center">
                            <h4 class="badge badge-default">{{ p.topics }}</h4>
                        </div>
                        <div class="col-md-1 text-sm-center">
                            <h4 class="badge badge-default">{{ p.posts }}</h4>
                        </div>
                    </div>
                    {% if loop.last == false %}
                        <hr/>
                    {% endif %}
                {% endfor %}
            </div>
        </div>
    {% endif %}
</div>