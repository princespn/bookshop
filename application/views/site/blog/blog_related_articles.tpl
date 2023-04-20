{% if related_articles %}
    <div class="row">
        <div class="col-md-12">
            <div class="related-articles min-pad-bottom">
                <h3>{{ lang('related_articles') }}</h3>
                <div class="carousel-three">
                    {% for p in related_articles %}
                        <div class="item">
                            <div class="card">
                                <figure class="gallery-item">
                                    {{ image('blog', p.overview_image, p.overview_image, 'img-fluid', FALSE) }}
                                    <figcaption class="hover-box">
                                        <h5><a href="{{ page_url('blog', p) }}"
                                               class="btn btn-primary btn-sm item-details">{{ lang('read_more') }}</a>
                                        </h5>
                                    </figcaption>
                                </figure>
                                <div class="card-body">
                                    <div><a href="{{ page_url('blog', p) }}">{{ p.title }}</a></div>
                                    <div class="box-meta">
                                        <ul class="list-inline">
                                            <li>{{ i('fa fa-user') }} {{ lang('by') }} {{ p.author }}</li>
                                            {% if p.category_id %}
                                                <li>{{ i('fa fa-folder-open-o') }}
                                                    {{ lang('in') }}
                                                    <a href="{{ site_url }}blog/category/{{ p.category_id }}/{{ url_title(p.category_name) }}">
                                                        {{ p.category_name }}</a>
                                                </li>
                                            {% endif %}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </div>
        </div>
    </div>
{% endif %}