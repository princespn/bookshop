{% extends "global/base.tpl" %}
{% block title %}{{ lang('events')|capitalize }}{% endblock %}
{% block page_header %}
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('events_for') }} {{ "now"|date('F') }} {{ y }}</h2>
            </div>

        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div id="commissions" class="content">
        {{ breadcrumb }}
        <div class="row">
            <div class="col-12">
                {% if events %}
                    <div class="card-columns">
                        {% for p in events %}
                            <div class="card">
                                {% if p.event_photo %}
                                    {{ image('blog', p.event_photo, p.title, 'img-fluid', FALSE) }}
                                {% else %}
                                    <img src="{{ base_url }}images/no-photo.jpg"
                                         class="img-fluid"/>
                                {% endif %}
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>{{ p.title }}</strong>
                                        {% if p.description %}
                                            <br/>{{ p.description }}
                                        {% endif %}
                                    <hr/>
                                    <small>
                                        {% if p.date %}
                                            {{ lang('date') }}: {{ p.formatted_date }}
                                        {% endif %}
                                        {% if p.start_time %}
                                            <br/>{{ lang('time') }}: {{ p.start_time }}
                                        {% endif %}
                                        {% if p.end_time %}
                                            - {{ p.end_time }}
                                        {% endif %}
                                        {% if p.location %}
                                            <br/>{{ lang('location') }}: {{ p.location }}
                                        {% endif %}
                                    </small>
                                    </p>
                                </div>
                            </div>
                        {% endfor %}
                    </div>
                {% else %}
                    <div class="alert alert-info">
                        {{ lang('no_events_found') }}
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
{% endblock content %}
