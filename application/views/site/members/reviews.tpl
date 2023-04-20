{% extends "global/base.tpl" %}
{% block title %}{{ lang('user_reviews') }}{% endblock %}
{% block page_header %}
    <div id="product-reviews-header" class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">{{ lang('user_reviews') }}</h2>
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
        <div class="col-md-12">
            {%  if reviews.values %}
                {% for r in reviews.values %}
                    <div class="row">
                        <div class="col-11">
                            <h5><a href="{{ site_url('product/details/'~r.product_id)}}-{{url_title(r.product_name|lower)}}">{{r.product_name}}</a>
                            <div class="star-rating float-lg-right">  {{ format_ratings(r.ratings)}}</div>
                            </h5>
                            <div class="box-meta">
                                    <ul class="list-inline">
                                        <li>{{ i('fa fa-clock-o') }} {{ lang('posted_on') }} {{ display_date(r.date, FALSE, 3) }}</li>
                                        {% if r.status == 0 %}
                                        <li><span class="badge badge-pill badge-warning">{{lang('pending_approval') }}</span> </li>
                                        {% endif %}
                                    </ul>
                            </div>
                            <h5>{{ r.title }}</h5>
                            <p class="minimize">{{ r.comment|nl2br }}</p>
                        </div>
                    </div>
                    <hr />
                {% endfor %}
    {% endif %}
        </div>
    </div>
{% endblock content %}