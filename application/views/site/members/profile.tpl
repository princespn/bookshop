{% extends "global/base.tpl" %}
{% block page_header %}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="headline">{{ lang('member_profile') }}</h2>
        </div>

    </div>
</div>
{% endblock page_header %}
{% block container %}
<div class="pt-3" {% if p.profile_background %}style="background: url('{{ p.profile_background }}'); background-position: center"{% endif %}>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-sm-center">
                        <p class="card-text">
                            {% if p.profile_photo %}
                            <img src="{{ p.profile_photo }}"
                                 class="rounded-circle mx-auto d-block"/>
                            {% else %}
                            <img src="{{ base_url('images/profile.png') }}"
                                 class="rounded-circle mx-auto d-block"/>
                            {% endif %}
                        </p>
                        {% if p.is_affiliate %}
                        <h5><a href="{{ affiliate_url(p.username) }}"
                               class="btn btn-block btn-lg btn-primary">@{{ p.username }}</a></h5>
                        {% endif %}

                    </div>
                </div>

            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="bg-primary text-white jumbotron hidden-sm-down">
                                    <h4 class="profile-line">{{ p.fname }} {{ p.lname }}</h4>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <address>
                                            {% if p.company %}<strong>{{ p.company }}</strong><br/>{% endif %}
                                            {{ i('fa fa-envelope-o') }} {{ encode_email(p.primary_email) }}<br/>
                                            {% if p.address_1 %}{{ p.address_1 }}<br/>{% endif %}
                                            {% if p.address_2 %}{{ p.address_2 }}<br/> {% endif %}
                                            {% if p.city %}{{ p.city }}
                                            {% endif %}  {% if p.region_name %}{{ p.region_name }}
                                            {% endif %}  {% if p.postal_code %}{{ p.postal_code }} <br/>{% endif %}
                                            {% if p.country_name %}{{ p.country_name }}<br/>{% endif %}
                                            {% if p.work_phone %}
                                            <span class="phone">{{ i('fa fa-phone-square') }} {{ p.work_phone }}</span>
                                            <br/>{% endif %}
                                        </address>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <p class="card-text">
                                            {% if (p.facebook_id) %}
                                            <a href="//facebook.com/{{ p.facebook_id }}" target="_blank"
                                               class="btn btn-facebook btn-icon">{{ i('fa fa-facebook') }}</a>
                                            {% endif %}
                                            {% if (p.youtube_id) %}
                                            <a href="//youtube.com/{{ p.youtube_id }}" target="_blank"
                                               class="btn btn-danger btn-icon">{{ i('fa fa-youtube-play') }}</a>
                                            {% endif %}
                                            {% if (p.twitter_id) %}
                                            <a href="//twitter.com/{{ p.twitter_id }}" target="_blank"
                                               class="btn btn-twitter btn-icon">{{ i('fa fa-twitter') }}</a>
                                            {% endif %}
                                            {% if (p.instagram_id) %}
                                            <a href="//instagram.com/{{ p.instagram_id }}" target="_blank"
                                               class="btn btn-instagram btn-icon">{{ i('fa fa-instagram') }}</a>
                                            {% endif %}
                                            {% if (p.tumblr_id) %}
                                            <a href="//{{ p.tumblr_id }}.tumblr.com" target="_blank"
                                               class="btn btn-tumblr btn-icon">{{ i('fa fa-tumblr') }}</a>
                                            {% endif %}
                                            {% if (p.linked_in_id) %}
                                            <a href="//linkedin.com/in/{{ p.linked_in_id }}" target="_blank"
                                               class="btn btn-linkedin btn-icon">{{ i('fa fa-linkedin') }}</a>
                                            {% endif %}
                                            {% if (p.pinterest_id) %}
                                            <a href="//www.pinterest.com/{{ p.pinterest_id }}" target="_blank"
                                               class="btn btn-pinterest btn-icon">{{ i('fa fa-pinterest') }}</a>
                                            {% endif %}
                                        </p>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-12">
                                        {% if p.profile_line %}
                                        <h3> <strong>{{ p.profile_line }}</strong></h3>
                                        <p>
                                           {{ p.profile_description }}
                                        </p>
                                        {% endif %}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock container %}
{% block javascript_footer %}
{{ parent() }}
{% endblock javascript_footer %}