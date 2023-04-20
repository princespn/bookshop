<!-- start slideshows -->
<div id="slideshow" class="carousel carousel-fade slide" data-ride="carousel">
    <ol class="carousel-indicators">
        {% for k,p in row.slide_shows %}
            {% if k == 0 %}
                <li data-target="#slideshow" data-slide-to="{{ k }}" class="active"></li>
            {% else %}
                <li data-target="#slideshow" data-slide-to="{{ k }}"></li>
            {% endif %}
        {% endfor %}
    </ol>
    <div class="carousel-inner" role="listbox">
        {% for k,p in row.slide_shows %}
            <div class="carousel-item {% if k == 0 %}active{% endif %}">
                {% if p.type == 'simple' %}
                    <div class="slide-item w-100" style="background:linear-gradient({{ p.background_color }}, {{ p.background_color }}),{{ p.background_color }} url('{{ p.background_image }}') no-repeat center;">
                        <div class="container">
                            <div class="row">
                                {% if p.position == 'center' %}
                                    <div class="col-10 offset-1 slide-div-center">
                                        <h1 class="text-center shadow-text slide-headline animated zoomInUp"
                                            style="color: {{ p.text_color }}">{{ p.headline }}</h1>
                                        <div class="text-center shadow-text slide-description animated zoomInDown"
                                             style="color: {{ p.text_color }}">
                                            <div>{{ p.slide_show }}</div>
                                            {% if p.action_url %}
                                            <div><a href="{{ p.action_url }}" class="btn btn-lg btn-primary">{{ p.button_text }}</a></div>
                                            {% endif %}
                                        </div>
                                    </div>
                                {% elseif p.position == 'right' %}
                                    <div class="col-7 offset-5 slide-div-right">
                                        <h2 class="text-center shadow-text slide-headline animated zoomInUp"
                                            style="color: {{ p.text_color }}">{{ p.headline }}</h2>
                                        <div class="text-center shadow-text slide-description animated zoomInDown"
                                             style="color: {{ p.text_color }}">
                                            <div>{{ p.slide_show }}</div>
                                            {% if p.action_url %}
                                                <div><a href="{{ p.action_url }}" class="btn btn-lg btn-primary">{{ p.button_text }}</a>
                                                </div>
                                            {% endif %}
                                        </div>
                                    </div>
                                {% else %}
                                    <div class="col-12 slide-div-left">
                                        <h1 class="slide-headline shadow-text animated zoomInUp"
                                            style="color: {{ p.text_color }}">{{ p.headline }}</h1>
                                        <div class="slide-description shadow-text animated zoomInDown"
                                             style="color: {{ p.text_color }}">
                                            <div>{{ p.slide_show }}</div>
                                            {% if p.action_url %}
                                            <div><a href="{{ p.action_url }}" class="btn btn-lg btn-primary">{{ p.button_text }}</a></div>
                                            {% endif %}
                                        </div>
                                    </div>
                                {% endif %}
                            </div>
                        </div>
                    </div>
                {% else %}
                    <div class="slide-item w-100">
                        <div class="slide-item w-100"
                             style="background:{{ p.background_color }} url('{{ p.background_image }}') no-repeat center;">
                        {{ p.slide_show }}
                        </div>
                    </div>
                {% endif %}
            </div>
        {% endfor %}
    </div>
    <a class="carousel-control-prev" href="#slideshow" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">{{ lang('previous') }}</span>
    </a>
    <a class="carousel-control-next" href="#slideshow" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">{{ lang('next') }}</span>
    </a>
</div><!-- end slideshows-->