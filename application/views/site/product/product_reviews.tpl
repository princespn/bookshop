{% extends "global/base.tpl" %}
{% block title %}{{ p.meta_title }}{% endblock %}
{% block meta_description %}{{ p.meta_description }}{% endblock meta_description %}
{% block meta_keywords %}{{ p.meta_keywords }}{% endblock meta_keywords %}
{% block page_header %}
    <div id="product-reviews-header" class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('product_reviews') }}</h2>
            </div>
        </div>
    </div>
{% endblock page_header %}
{% block content %}
    <div class="row">
        <div class="col-md-12">
            {{ breadcrumb }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <h3>{{ p.product_name }}</h3>
        </div>
        <div class="col-md-5 text-md-right">
          <a href="{{ page_url('product', p) }}" class="btn btn-secondary btn-block-sm">{{ i('fa fa-undo') }} {{ lang('go_back') }}</a>
            {%  if member_logged_in == 1 %}
                <a href="{{ site_url }}product_reviews/add/{{ id }}" class="btn btn-primary btn-block-sm">{{ i('fa fa-plus') }} {{ lang('add_your_review') }}</a>
            {% else %}
                <a data-toggle="modal" data-target="#login-modal" href="#" class="btn btn-info btn-block-sm">{{ i('fa fa-lock') }} {{ lang('login_to_submit_review') }}</a>
            {% endif %}
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-md-12">
            {%  if reviews %}
                {% for r in reviews %}
                    <div class="row">
                        <div class="user-icon col-1 text-sm-center">
                            {{ image('members', r.profile_photo, r.username, 'img-thumbnail rounded-circle d-none d-sm-block', FALSE) }}
                        </div>
                        <div class="col-11">
                            <h5>{{ r.title }}
                            <div class="star-rating float-lg-right">  {{ format_ratings(r.ratings)}}</div>
                            </h5>
                            <div class="box-meta">
                                    <ul class="list-inline">
                                        <li>{{ i('fa fa-user') }} {{ lang('by') }} {{ format_name(r.username) }}</li>
                                        <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(r.date, FALSE, 3) }}</li>
                                    </ul>
                            </div>
                            <p class="minimize">{{ html_entity_decode(r.comment|nl2br) }}</p>
                        </div>
                    </div>
                    <hr />
                {% endfor %}
                {% include ('global/pagination.tpl') %}
    {% endif %}
        </div>
    </div>
{% endblock content %}